<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require_once '../../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);

        // También permitir pasar el ID como parámetro GET
        $id_usuario = null;
        if ($input && isset($input['id'])) {
            $id_usuario = intval($input['id']);
        } elseif (isset($_GET['id'])) {
            $id_usuario = intval($_GET['id']);
        }

        if (!$id_usuario) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
            exit;
        }

        // Verificar que el usuario existe
        $stmt = $conn->prepare("SELECT id, nombre, apellido, email, id_rol, activo FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            exit;
        }

        // Verificar si el usuario tiene relaciones que impiden la eliminación
        $relaciones = [];

        // Verificar alimentos como donante
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM alimentos WHERE id_donante = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $count_alimentos = intval($stmt->fetchColumn());

        if ($count_alimentos > 0) {
            $relaciones[] = "$count_alimentos alimento(s)";
        }

        // Verificar compras como receptor
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM compras WHERE id_receptor = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $count_compras = intval($stmt->fetchColumn());

        if ($count_compras > 0) {
            $relaciones[] = "$count_compras compra(s)";
        }

        // Verificar moderaciones
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM moderaciones WHERE id_moderador = :id");
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $count_moderaciones = intval($stmt->fetchColumn());

        if ($count_moderaciones > 0) {
            $relaciones[] = "$count_moderaciones moderación(es)";
        }

        /*
          Nueva lógica:
          - Si tiene relaciones Y está activo -> NO eliminar, devolver mensaje (recomendar desactivar).
          - Si no tiene relaciones -> eliminar (activo o inactivo).
          - Si tiene relaciones Y está inactivo -> ELIMINAR igualmente (pero devolver relaciones en la respuesta).
        */
        if (!empty($relaciones) && intval($usuario['activo']) === 1) {
            $mensaje = "No se puede eliminar el usuario porque tiene " . implode(', ', $relaciones) . " asociado(s). ";
            $mensaje .= "Se recomienda desactivar el usuario en lugar de eliminarlo.";

            echo json_encode([
                'success' => false,
                'error' => $mensaje,
                'usuario' => [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'] . ' ' . $usuario['apellido'],
                    'email' => $usuario['email'],
                    'activo' => (int)$usuario['activo']
                ],
                'relaciones' => $relaciones
            ]);
            exit;
        }

        // Si llega aquí: o no tiene relaciones (=> podemos eliminar), o tiene relaciones pero está INACTIVO (=> también eliminamos)
        $conn->beginTransaction();

        try {
            // Eliminar local si existe (CASCADE debería manejarlo, pero por seguridad)
            $stmt = $conn->prepare("DELETE FROM locales WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar usuario
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $conn->commit();
                $response = [
                    'success' => true,
                    'data' => [
                        'accion' => 'eliminado',
                        'nombre' => $usuario['nombre'] . ' ' . $usuario['apellido'],
                        'nombre_local' => null
                    ]
                ];
                // Si tenía relaciones (caso: estaba inactivo pero tenía relaciones), enviamos info de relaciones también
                if (!empty($relaciones)) {
                    $response['relaciones'] = $relaciones;
                    $response['message'] = 'Usuario inactivo eliminado. Tenía relaciones asociadas.';
                } else {
                    $response['message'] = 'Usuario eliminado correctamente.';
                }

                echo json_encode($response);
            } else {
                throw new Exception('Error al eliminar usuario');
            }
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    } else {
        // No permitimos POST/PUT aquí para activar/desactivar: esa funcionalidad está en otro API según indicaste.
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
