<?php
// Configurar headers para permitir peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Habilitar logging de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla, solo en logs

require_once '../config/db.php';

try {
    // Verificar si se solicita filtrar por local específico
    $id_local = isset($_GET['id_local']) ? (int)$_GET['id_local'] : null;
    
    // Log para debugging (puedes comentar esto después)
    error_log("API get_alimentosPublic.php - Filtro por local: " . ($id_local ?: 'todos'));
    
    // CONSULTA SQL base
    $sql = "SELECT a.id, a.descripcion, a.nombre, a.precio, a.stock, a.imagen_url, a.activo, a.porcentaje_descuento, 
                   l.nombre_local AS nombre_local, l.id AS id_local
            FROM alimentos a
            INNER JOIN usuarios u ON a.id_donante = u.id
            INNER JOIN locales l ON l.id_usuario = u.id
            WHERE a.activo = 1 
            AND a.stock > 0"; // Solo mostrar productos con stock
    
    // Si se especifica un local, agregar filtro
    if ($id_local !== null) {
        $sql .= " AND l.id = :id_local";
    }
    
    $sql .= " ORDER BY a.nombre ASC";
    
    // Log de la consulta SQL (para debugging)
    error_log("SQL Query: " . $sql);
    if ($id_local) {
        error_log("Local ID parameter: " . $id_local);
    }
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    
    // Vincular parámetro si existe
    if ($id_local !== null) {
        $stmt->bindParam(':id_local', $id_local, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    // Obtener todos los productos
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log del resultado
    error_log("Productos encontrados: " . count($productos));
    
    // Formatear los datos para la respuesta JSON
    $productos_formateados = [];
    foreach ($productos as $producto) {
        $productos_formateados[] = [
            'id' => (int)$producto['id'],
            'id_local' => (int)$producto['id_local'],
            'descripcion' => $producto['descripcion'] ?? '',
            'nombre' => $producto['nombre'] ?? 'Sin nombre',
            'precio' => (float)$producto['precio'],
            'stock' => (int)$producto['stock'],
            'imagen_url' => $producto['imagen_url'] ?? null,
            'nombre_local' => $producto['nombre_local'] ?? 'Sin nombre de local',
            'activo' => (bool)$producto['activo'],
            'porcentaje_descuento' => (int)($producto['porcentaje_descuento'] ?? 0)
        ];
    }
    
    // Enviar la respuesta JSON exitosa
    $response = [
        'success' => true,
        'message' => $id_local ? "Alimentos del local $id_local obtenidos correctamente" : 'Alimentos obtenidos correctamente',
        'products' => $productos_formateados,
        'count' => count($productos_formateados),
        'debug' => [
            'id_local_requested' => $id_local,
            'total_found' => count($productos_formateados),
            'query_executed' => true
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    // Error de base de datos
    error_log("Database Error: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => 'Error de base de datos',
        'error_details' => $e->getMessage(),
        'products' => [],
        'debug' => [
            'error_type' => 'PDO',
            'id_local_requested' => $id_local ?? null
        ]
    ];
    
    http_response_code(500);
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error general
    error_log("General Error: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => 'Error del servidor',
        'error_details' => $e->getMessage(),
        'products' => [],
        'debug' => [
            'error_type' => 'General',
            'id_local_requested' => $id_local ?? null
        ]
    ];
    
    http_response_code(500);
    echo json_encode($response);
}
?>