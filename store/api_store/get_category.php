<?php
// Configurar headers para permitir peticiones AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';
try {
    //consulta para obtener las categorías
    $sql = "SELECT id, nombre,activo FROM categorias WHERE activo = 1 ORDER BY nombre";
    //preparar la consulta y ejecutarla
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    //obtener todas las categorías
    //fetchAll devuelve un array de todas las filas del resultado
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //formatear los datos para la respuesta JSON
    $categorias_formateadas = [];
    foreach ($categorias as $categoria) {
        $categorias_formateadas[] = [
            'id' => (int)$categoria['id'],
            'nombre' => $categoria['nombre'],
            'activo' => (bool)$categoria['activo']
        ];
    }
    //enviar la respuesta JSON exitosa
    $response = [
        'success' => true,
        'message' => 'Categorías obtenidas correctamente',
        'categories' => $categorias_formateadas,
        'count' => count($categorias_formateadas)
    ];
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Error de base de datos
    $response = [
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'categories' => []
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error general
    $response = [
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage(),
        'categories' => []
    ];
    
    echo json_encode($response);
}
?>