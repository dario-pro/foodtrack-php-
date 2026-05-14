<?php
// api/actualizar_estado_producto.php
session_start(); // <-- Importante para acceder a $_SESSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id_producto']) || !isset($input['activo'])) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    try {
        require_once '../../config/db.php';

        $query = "UPDATE alimentos SET activo = :activo WHERE id = :id";
        $params = [
            ':activo' => $input['activo'] ? 1 : 0,
            ':id' => $input['id_producto']
        ];

        $stmt = ejecutarConsulta($query, $params);

        if ($stmt->rowCount() > 0) {
            $id_moderador = $_SESSION['id'] ?? 1; // <-- ahora lo tomas de la sesión
            $accion = $input['activo'] ? 'aprobado' : 'rechazado';

            $query_mod = "INSERT INTO moderaciones (id_moderador, id_alimento, accion, comentario) 
                         VALUES (:id_moderador, :id_alimento, :accion, :comentario)";
            $params_mod = [
                ':id_moderador' => $id_moderador,
                ':id_alimento' => $input['id_producto'],
                ':accion' => $accion,
                ':comentario' => $input['comentario'] ?? ''
            ];

            ejecutarConsulta($query_mod, $params_mod);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
