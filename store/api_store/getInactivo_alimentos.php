<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';

try {
    // Query para obtener productos inactivos con información del donante y categoría
    $query = "SELECT 
                a.id,
                a.nombre,
                a.descripcion,
                a.precio,
                a.stock,
                a.imagen_url,
                a.fecha_caducidad,
                a.porcentaje_descuento,
                a.estado,
                a.activo,                
                CONCAT(u.nombre, ' ', u.apellido) as donante,
                u.telefono as contacto_donante,
                l.nombre_local as nombre_local,
                a.creado_en
              FROM alimentos a              
              INNER JOIN usuarios u ON a.id_donante = u.id INNER JOIN  locales l ON u.id = l.id_usuario
              ORDER BY a.creado_en DESC";
    
    $stmt = ejecutarConsulta($query);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>