<?php
// Configurar headers para permitir peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';
try {
    //consulta para obtener las categorías
    $sql = "SELECT id, nombre FROM tipo_local  ORDER BY nombre";
    //preparar la consulta y ejecutarla
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    //obtener todas las categorías
    //fetchAll devuelve un array de todas las filas del resultado
    $tipoLocal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //formatear los datos para la respuesta JSON
    $tipoLocal_formateados = [];
    foreach ($tipoLocal as $tipo) {
        $tipoLocal_formateados[] = [
            'id' => (int)$tipo['id'],
            'nombre' => $tipo['nombre']
        ];
    }
    //enviar la respuesta JSON exitosa
    $response = [
        'success' => true,
        'message' => 'Tipo local obtenidos correctamente',
        'tipoLocal' => $tipoLocal_formateados,
        'count' => count($tipoLocal_formateados)
    ];
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Error de base de datos
    $response = [
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'tipoLocal' => []
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error general
    $response = [
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage(),
        'tipoLocal' => []
    ];
    
    echo json_encode($response);
}
?>