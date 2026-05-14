<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require_once '../../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
            exit;
        }

        $id_usuario = intval($_GET['id']);

        $sql = "SELECT 
                    u.*,
                    u.id_rol,
                    r.nombre as nombre_rol,
                    l.nombre_local,
                    l.sector,
                    l.direccion as direccion_local,
                    l.telefono as telefono_local,
                    l.imagen_url as imagen_local,
                    l.id as id_local,
                    tl.nombre as tipo_local,
                    tl.id as id_tipo_local
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id 
                LEFT JOIN locales l ON l.id_usuario = u.id
                LEFT JOIN tipo_local tl ON l.id_tipo_local = tl.id
                WHERE u.id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Generar URL absoluta de la imagen para JS
            $usuario['imagen_local'] = (!empty($usuario['imagen_local']) && $usuario['imagen_local'] !== 'null')
                ? "/proyectoComida/" . $usuario['imagen_local']
                : null;

            // Remover la password del resultado por seguridad
            unset($usuario['password']);

            echo json_encode(['success' => true, 'data' => $usuario]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos']);
            exit;
        }

        // Campos requeridos
        $campos_requeridos = ['id_usuario', 'nombre', 'apellido', 'email', 'direccion', 'telefono', 'id_rol'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($input[$campo]) || trim($input[$campo]) === '') {
                echo json_encode(['success' => false, 'error' => "Campo requerido: $campo"]);
                exit;
            }
        }

        $id_usuario = intval($input['id_usuario']);
        $nombre = trim($input['nombre']);
        $apellido = trim($input['apellido']);
        $email = trim($input['email']);
        $direccion = trim($input['direccion']);
        $telefono = trim($input['telefono']);
        $id_rol = intval($input['id_rol']);

        // Obtener estado activo actual del usuario
        $stmt = $conn->prepare("SELECT activo FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $estado_actual = $stmt->fetchColumn();

        // Usar el valor que llega en JSON o mantener el existente
        $activo = isset($input['activo']) ? (bool)$input['activo'] : (bool)$estado_actual;

        // Campos opcionales para comerciantes
        $nombre_local = isset($input['nombre_local']) ? trim($input['nombre_local']) : '';
        $tipo_local = isset($input['tipo_local']) ? trim($input['tipo_local']) : '';

        // Validaciones
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Email inválido']);
            exit;
        }

        if (!preg_match('/^[0-9+\-\s]+$/', $telefono)) {
            echo json_encode(['success' => false, 'error' => 'Teléfono inválido']);
            exit;
        }

        // Verificar que el usuario existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            exit;
        }

        // Verificar que el email no esté en uso por otro usuario
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'El email ya está en uso por otro usuario']);
            exit;
        }

        $conn->beginTransaction();

        try {

            // Actualizar usuario
            $sql_usuario = "UPDATE usuarios SET 
                            nombre = :nombre,
                            apellido = :apellido,
                            email = :email,
                            direccion = :direccion,
                            telefono = :telefono,
                            id_rol = :id_rol,
                            activo = :activo
                            WHERE id = :id";

            $stmt = $conn->prepare($sql_usuario);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar usuario');
            }

            // Si es comerciante, manejar datos del local
            if ($id_rol === '3') {
                if (empty($nombre_local) || empty($tipo_local)) {
                    throw new Exception('Para comerciantes son requeridos: nombre del local y tipo de local');
                }

                // Obtener ID del tipo de local
                $stmt = $conn->prepare("SELECT id FROM tipo_local WHERE nombre = :tipo");
                $stmt->bindParam(':tipo', $tipo_local);
                $stmt->execute();
                $id_tipo_local = $stmt->fetchColumn();

                if (!$id_tipo_local) {
                    throw new Exception('Tipo de local no encontrado');
                }

                // Verificar si ya existe un local para este usuario
                $stmt = $conn->prepare("SELECT id FROM locales WHERE id_usuario = :id_usuario");
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();
                $local_existente = $stmt->fetchColumn();

                if ($local_existente) {
                    // Actualizar local existente
                    $sql_local = "UPDATE locales SET 
                                  nombre_local = :nombre_local,
                                  id_tipo_local = :id_tipo_local,
                                  direccion = :direccion,
                                  telefono = :telefono,
                                  sector = :sector
                                  WHERE id_usuario = :id_usuario";

                    $stmt = $conn->prepare($sql_local);
                    $stmt->bindParam(':nombre_local', $nombre_local);
                    $stmt->bindParam(':id_tipo_local', $id_tipo_local, PDO::PARAM_INT);
                    $stmt->bindParam(':direccion', $direccion);
                    $stmt->bindParam(':telefono', $telefono);
                    $stmt->bindParam(':sector', $input['sector']);
                    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

                    if (!$stmt->execute()) {
                        throw new Exception('Error al actualizar local');
                    }
                } else {
                    // Crear nuevo local
                    $sql_local = "INSERT INTO locales (id_usuario, id_tipo_local, nombre_local, direccion, telefono, sector, imagen_url) 
                                  VALUES (:id_usuario, :id_tipo_local, :nombre_local, :direccion, :telefono, :sector, '')";

                    $stmt = $conn->prepare($sql_local);
                    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                    $stmt->bindParam(':id_tipo_local', $id_tipo_local, PDO::PARAM_INT);
                    $stmt->bindParam(':nombre_local', $nombre_local);
                    $stmt->bindParam(':direccion', $direccion);
                    $stmt->bindParam(':telefono', $telefono);
                    $stmt->bindParam(':sector', $sector);

                    if (!$stmt->execute()) {
                        throw new Exception('Error al crear local');
                    }
                }
            } else {
                // Si no es comerciante, eliminar local si existe
                $stmt = $conn->prepare("DELETE FROM locales WHERE id_usuario = :id_usuario");
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Endpoint para actualizar solo la contraseña
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['success' => false, 'error' => 'Datos JSON inválidos']);
            exit;
        }

        if (!isset($input['id_usuario']) || !isset($input['nueva_password'])) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario y nueva contraseña requeridos']);
            exit;
        }

        $id_usuario = intval($input['id_usuario']);
        $nueva_password = trim($input['nueva_password']);

        if (strlen($nueva_password) < 6) {
            echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            exit;
        }

        // Verificar que el usuario existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            exit;
        }

        // Actualizar contraseña
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET password = :password WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar contraseña']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
