<?php
// Configurar headers para permitir peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';

try {
    // CONSULTA SQL: obtener solo id y nombre_local
    $sql = "SELECT 
            l.id,
            l.id_tipo_local,
            l.nombre_local,
            l.imagen_url,
            l.sector,
            l.direccion,
            t.nombre AS tipo_nombre
        FROM locales l
        INNER JOIN tipo_local t ON l.id_tipo_local = t.id
        ORDER BY l.nombre_local ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $locales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear datos (aunque aquí ya es sencillo)
    $locales_formateados = [];
    foreach ($locales as $local) {
        $locales_formateados[] = [
            'id' => $local['id'],
            'id_tipo_local' => $local['id_tipo_local'],
            'nombre_local' => $local['nombre_local'],
            'imagen_url' => $local['imagen_url'],
            'sector' => $local['sector'],
            'direccion' => $local['direccion'],
            'tipo_nombre' => $local['tipo_nombre']
        ];
    }

    // Respuesta JSON
    $response = [
        'success' => true,
        'message' => 'Locales obtenidos correctamente',
        'locales' => $locales_formateados,
        'count' => count($locales_formateados)
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'locales' => []
    ];
    echo json_encode($response);

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage(),
        'locales' => []
    ];
    echo json_encode($response);
}
?>
