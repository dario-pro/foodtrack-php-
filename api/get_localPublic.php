<?php
// Configurar headers para permitir peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

try {
    // Obtener el parámetro de categoría desde la URL
    $categoria = isset($_GET['categoria']) ? strtolower(trim($_GET['categoria'])) : null;
    
    // Primero obtenemos los tipos de local desde la base de datos
    $sql_tipos = "SELECT id, nombre FROM tipo_local";
    $stmt_tipos = $conn->prepare($sql_tipos);
    $stmt_tipos->execute();
    $tipos_local = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
    
    // Crear mapeo dinámico basado en la BD
    $categorias_map = [];
    foreach ($tipos_local as $tipo) {
        $categorias_map[strtolower($tipo['nombre'])] = $tipo['id'];
    }

    // Validar categoría
    if (!$categoria || !isset($categorias_map[$categoria])) {
        throw new Exception('Categoría no válida. Categorías disponibles: ' . implode(', ', array_keys($categorias_map)));
    }

    $id_tipo_local = $categorias_map[$categoria];

    // CONSULTA SQL: obtener locales filtrados por categoría con JOIN para obtener nombre del tipo
    $sql = "SELECT 
    l.id, 
    l.id_tipo_local, 
    l.nombre_local, 
    l.imagen_url, 
    l.direccion,
    l.sector,
    tl.nombre AS tipo_local_nombre
FROM locales l
INNER JOIN tipo_local tl ON l.id_tipo_local = tl.id
INNER JOIN usuarios u ON l.id_usuario = u.id
WHERE 
    l.id_tipo_local = :id_tipo_local
    AND u.activo = TRUE
ORDER BY l.nombre_local ASC;
";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_tipo_local', $id_tipo_local, PDO::PARAM_INT);
    $stmt->execute();

    $locales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear datos
    $locales_formateados = [];
    foreach ($locales as $local) {
        $locales_formateados[] = [
            'id' => (int)$local['id'],
            'id_tipo_local' => (int)$local['id_tipo_local'],
            'nombre_local' => $local['nombre_local'],
            'imagen_url' => $local['imagen_url'],
            'direccion' => $local['direccion'],
            'sector' => $local['sector'],
            'tipo_local' => $local['tipo_local_nombre']
        ];
    }

    // Respuesta JSON
    $response = [
        'success' => true,
        'message' => "Locales de {$categoria} obtenidos correctamente",
        'categoria' => $categoria,
        'locales' => $locales_formateados,
        'count' => count($locales_formateados)
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'locales' => [],
        'count' => 0
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'locales' => [],
        'count' => 0
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>