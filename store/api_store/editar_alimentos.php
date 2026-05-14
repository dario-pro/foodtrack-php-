<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require_once '../../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID de producto requerido']);
            exit;
        }

        $id_producto = intval($_GET['id']);

        $sql = "SELECT 
                    a.*,
                    a.imagen_url,
                    u.nombre as donante,
                    u.telefono as contacto_donante,
                    l.nombre_local as nombre_local,
                    l.id as id_local,
                    t.nombre as tipo_local
                FROM alimentos a 
                INNER JOIN usuarios u ON a.id_donante = u.id 
                INNER JOIN locales l ON l.id_usuario = u.id
                INNER JOIN tipo_local t ON l.id_tipo_local = t.id
                WHERE a.id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt->execute();

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Generar URL absoluta de la imagen para JS
            $producto['imagen_url'] = (!empty($producto['imagen_url']) && $producto['imagen_url'] !== 'null')
                ? "/proyectoComida/store/" . $producto['imagen_url']
                : null;

            echo json_encode(['success' => true, 'data' => $producto]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos']);
            exit;
        }

        $campos_requeridos = ['id_producto', 'nombre', 'descripcion', 'precio', 'stock', 'fecha_caducidad', 'estado'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($input[$campo]) || trim($input[$campo]) === '') {
                echo json_encode(['success' => false, 'error' => "Campo requerido: $campo"]);
                exit;
            }
        }

        $id_producto = intval($input['id_producto']);
        $nombre = trim($input['nombre']);
        $descripcion = trim($input['descripcion']);
        $precio = floatval($input['precio']);
        $stock = intval($input['stock']);
        $fecha_caducidad = date('Y-m-d', strtotime($input['fecha_caducidad']));
        $estado = trim($input['estado']);
        $porcentaje_descuento = isset($input['porcentaje_descuento']) ? intval($input['porcentaje_descuento']) : 0;

        // Validaciones
        if ($precio < 0 || $stock < 0 || $porcentaje_descuento < 0 || $porcentaje_descuento > 100) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            exit;
        }

        $stmt = $conn->prepare("SELECT id FROM alimentos WHERE id = :id");
        $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
            exit;
        }

        $sql = "UPDATE alimentos SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    stock = :stock,
                    fecha_caducidad = :fecha_caducidad,
                    porcentaje_descuento = :porcentaje_descuento,
                    estado = :estado,
                    actualizado_en = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':fecha_caducidad', $fecha_caducidad);
        $stmt->bindParam(':porcentaje_descuento', $porcentaje_descuento, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar el producto']);
        }

    } else {
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
