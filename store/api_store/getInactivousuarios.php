<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';

try {
    //query para obtener la informacion de susarios
    $query = "SELECT 
    u.id,
    CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo,
    u.email,
    r.nombre AS rol,
    u.telefono,
    u.direccion,
    u.creado_en,
    u.activo,
    l.nombre_local,
    t.nombre AS categoria_local
FROM usuarios u
INNER JOIN roles r ON u.id_rol = r.id
LEFT JOIN locales l ON u.id = l.id_usuario
LEFT JOIN tipo_local t ON l.id_tipo_local = t.id
ORDER BY u.creado_en DESC;";

$stmt = ejecutarConsulta($query);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $usuarios
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>