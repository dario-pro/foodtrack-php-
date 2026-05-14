<?php
// Configurar headers para permitir peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';
try {
    // Verificar si se solicita filtrar por local específico
    $id_local = isset($_GET['id_local']) ? (int)$_GET['id_local'] : null;
    
    // CONSULTA SQL base
    $sql = "SELECT a.id, a.descripcion, a.nombre, a.precio, a.stock, a.imagen_url, a.activo, a.porcentaje_descuento, 
                    l.nombre_local AS nombre_local, l.id AS id_local
            FROM alimentos a
            
            JOIN usuarios u ON a.id_donante = u.id
            JOIN locales l ON l.id_usuario = u.id
            WHERE a.activo = 1 ";
    
    // Si se especifica un local, agregar filtro
    if ($id_local !== null) {
        $sql .= " AND l.id = :id_local";
    }
    
    $sql .= " ORDER BY a.nombre ASC";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    
    // Vincular parámetro si existe
    if ($id_local !== null) {
        $stmt->bindParam(':id_local', $id_local, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    // Obtener todos los productos
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear los datos para la respuesta JSON
    $productos_formateados = [];
    foreach ($productos as $producto) {
        $productos_formateados[] = [
            'id' => $producto['id'],
            //'id_categoria' => $producto['id_categoria'],
            'id_local' => $producto['id_local'], // ← ESTO ES NUEVO
            'descripcion' => $producto['descripcion'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'stock' => $producto['stock'],
            'imagen_url' => $producto['imagen_url'],
            //'categoria' => $producto['categoria'],
            'nombre_local' => $producto['nombre_local'],
            'activo' => (bool)$producto['activo'],
            'porcentaje_descuento' => $producto['porcentaje_descuento'] 
        ];
    }
    
    // Enviar la respuesta JSON exitosa
    $response = [
        'success' => true,
        'message' => $id_local ? "Alimentos del local $id_local obtenidos correctamente" : 'Alimentos obtenidos correctamente',
        'products' => $productos_formateados,
        'count' => count($productos_formateados)
    ];
    echo json_encode($response);

} catch (PDOException $e) {
    // Error de base de datos
    $response = [
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'products' => []
    ];
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error general
    $response = [
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage(),
        'products' => []
    ];
    echo json_encode($response);
}
?>