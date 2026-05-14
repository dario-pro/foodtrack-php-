<?php
// login_process.php - Archivo separado para procesar login
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // LOGIN
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $contrasena = $_POST['contrasena'];

        try {
            $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1";
            $stmt = ejecutarConsulta($sql, [$email]);
            $usuarioData = $stmt->fetch();

            if (!$usuarioData) {
                echo json_encode(['success' => false, 'message' => '🟡 Usuario no encontrado o inactivo']);
            } elseif (!password_verify($contrasena, $usuarioData['password'])) {
                echo json_encode(['success' => false, 'message' => '🔒 Contraseña incorrecta']);
            } else {
                $_SESSION['email'] = $usuarioData['email'];
                $_SESSION['nombre'] = $usuarioData['nombre'];
                $_SESSION['id_rol'] = $usuarioData['id_rol'];
                $_SESSION['id'] = $usuarioData['id'];
                
                echo json_encode(['success' => true, 'redirect' => 'pageStore/store.php']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => '⚠️ Error: ' . $e->getMessage()]);
        }
    }
    
    // REGISTRO
    elseif (isset($_POST['register'])) {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        $direccion = trim($_POST['direccion']);
        $telefono = trim($_POST['telefono']);
        $id_rol = $_POST['id_rol'];

        try {
            $sql = "INSERT INTO usuarios (id_rol,nombre, apellido, email, password, direccion, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)";
            ejecutarConsulta($sql, [$id_rol, $nombre, $apellido, $email, $contrasena, $direccion, $telefono]);
            
            echo json_encode(['success' => true, 'message' => '✅ Usuario registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => '❌ Error: ' . $e->getMessage()]);
        }
    }
}
?>