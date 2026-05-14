<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $input = json_decode(file_get_contents('php://input'), true);
    if(!isset($input['id_usuario']) || !isset($input['activo'])){
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    try {
        require_once '../../config/db.php';
        $query = "UPDATE usuarios SET activo = :activo WHERE id = :id";
        $params = [
            ':activo' => $input['activo'] ? 1 : 0,
            ':id' => $input['id_usuario']
        ];
        $stmt = ejecutarConsulta($query, $params);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar usuario: ' . $e->getMessage()]);
    }

}

?>