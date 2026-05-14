<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Prevenir ataques CSRF
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once '../../config/db.php';

/**
 * Función para sanitizar entrada
 */
function sanitizeInput($input) {
    return filter_var(trim($input), FILTER_VALIDATE_INT);
}

/**
 * Obtener información completa del producto
 */
function obtenerInfoProducto($conn, $id) {
    $sql = "SELECT 
                a.id,
                a.nombre,
                a.id_donante,
                u.nombre as donante,
                l.nombre_local
            FROM alimentos a 
            INNER JOIN usuarios u ON a.id_donante = u.id 
            INNER JOIN locales l ON l.id_usuario = u.id
            WHERE a.id = :id LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Verificar si el producto tiene pedidos relacionados
 */
function tienePedidosRelacionados($conn, $id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM detalle_compras WHERE id_alimento = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $resultado['count'] > 0;
}

try {
    // Solo permitir método DELETE
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode([
            'success' => false, 
            'error' => 'Método no permitido'
        ]);
        exit;
    }

    // Obtener ID del producto
    $id_producto = null;
    
    if (isset($_GET['id'])) {
        $id_producto = sanitizeInput($_GET['id']);
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['id'])) {
            $id_producto = sanitizeInput($input['id']);
        }
    }

    // Validar ID del producto
    if (!$id_producto || $id_producto <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'ID de producto inválido o requerido'
        ]);
        exit;
    }

    // Verificar que el producto existe y obtener información
    $producto = obtenerInfoProducto($conn, $id_producto);
    if (!$producto) {
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'error' => 'Producto no encontrado'
        ]);
        exit;
    }

    // Iniciar transacción
    $conn->beginTransaction();

    try {
        // Verificar si tiene pedidos relacionados
        if (tienePedidosRelacionados($conn, $id_producto)) {
            // Si hay pedidos, desactivar en lugar de eliminar
            $sql = "UPDATE alimentos SET 
                        activo = 0, 
                        actualizado_en = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            $mensaje = 'Producto desactivado correctamente (tiene pedidos asociados)';
            $tipo_accion = 'desactivado';
            
        } else {
            // Si no hay pedidos, eliminar completamente
            $sql = "DELETE FROM alimentos WHERE id = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            $mensaje = 'Producto eliminado correctamente';
            $tipo_accion = 'eliminado';
        }

        if ($resultado) {
            $conn->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => $mensaje,
                'data' => [
                    'producto_nombre' => $producto['nombre'],
                    'local_nombre' => $producto['nombre_local'],
                    'donante' => $producto['donante'],
                    'accion' => $tipo_accion
                ]
            ]);
        } else {
            throw new Exception('Error al ejecutar la operación de eliminación');
        }

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Error de base de datos en eliminación: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Error interno del servidor'
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Error general en eliminación: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Error interno del servidor'
    ]);
}
?>