<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/db.php';

try {
    // Leer datos enviados en JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) throw new Exception("No se recibieron datos");

    // Validar campos requeridos
    if (!isset($input['cart'], $input['total'], $input['fecha_entrega'], $input['direccion_entrega'], $input['cliente_celular'])) {
        throw new Exception("Datos incompletos");
    }

    $cart = $input['cart'];
    $total = $input['total'];
    $fecha_entrega = $input['fecha_entrega'];
    $direccion_entrega = $input['direccion_entrega'];
    $cliente_celular = $input['cliente_celular'];

    if (empty($cart)) throw new Exception("El carrito está vacío");

    // Obtener id del usuario logueado
    if (!isset($_SESSION['id'])) throw new Exception("Usuario no autenticado");
    $id_receptor = $_SESSION['id'];

    // Iniciar transacción
    $conn->beginTransaction();

    // Actualizar teléfono y dirección si cambió
    $stmt_update_usuario = $conn->prepare("UPDATE usuarios SET telefono = ?, direccion = ? WHERE id = ?");
    $stmt_update_usuario->execute([$cliente_celular, $direccion_entrega, $id_receptor]);

    // Insertar en tabla compras
    $stmt_compra = $conn->prepare("
        INSERT INTO compras (id_receptor, fecha_compra, total, fecha_entrega, direccion_entrega)
        VALUES (?, CURDATE(), ?, ?, ?)
    ");
    $stmt_compra->execute([$id_receptor, $total, $fecha_entrega, $direccion_entrega]);
    $id_compra = $conn->lastInsertId();

    // Preparar inserción en detalle_compras
    $stmt_detalle = $conn->prepare("
        INSERT INTO detalle_compras (id_compra, id_alimento, cantidad, precio_unitario, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");

    // Preparar actualización de stock
    $stmt_stock = $conn->prepare("
        UPDATE alimentos SET stock = stock - ? 
        WHERE id = ? AND stock >= ?
    ");

    foreach ($cart as $item) {
        $subtotal_item = $item['precio'] * $item['cantidad'];

        // Insertar detalle
        $stmt_detalle->execute([
            $id_compra,
            $item['id'],
            $item['cantidad'],
            $item['precio'],
            $subtotal_item
        ]);

        // Actualizar stock
        $stmt_stock->execute([$item['cantidad'], $item['id'], $item['cantidad']]);
        if ($stmt_stock->rowCount() == 0) {
            throw new Exception("Stock insuficiente para el producto: " . $item['nombre']);
        }
    }

    // Confirmar transacción
    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Compra procesada exitosamente",
        "data" => [
            "id_compra" => $id_compra,
            "total" => $total,
            "fecha_compra" => date("Y-m-d"),
            "fecha_entrega" => $fecha_entrega,
            "direccion_entrega" => $direccion_entrega
        ]
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode([
        "success" => false,
        "message" => "Error al procesar compra: " . $e->getMessage()
    ]);
}

