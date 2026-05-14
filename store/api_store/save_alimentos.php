<?php
session_start();
require_once '../../config/db.php';

// Validar que haya un usuario logueado
if (!isset($_SESSION['id'])) {
    die("No se ha iniciado sesión");
}

$id_donante = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    //$id_categoria = $_POST['id_categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $fecha_caducidad = $_POST['fecha_caducidad'] ?? '';
    $porcentaje_descuento = $_POST['porcentaje_descuento'] ?? 0;
    $estado = $_POST['estado'] ?? 'apto_consumo';

    // Verificar y mover la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = '../update/';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true); // Crear carpeta si no existe
        }

        $nombreArchivo = basename($_FILES['imagen']['name']);
        $rutaDestino = $directorioDestino . time() . '_' . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            // Ruta relativa para la base de datos
            $imagen_url = str_replace('../', '', $rutaDestino);

            // Insertar en la base de datos
            $sql = "INSERT INTO alimentos 
                (id_donante, nombre, descripcion, precio, stock, imagen_url, fecha_caducidad, porcentaje_descuento, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            try {
                ejecutarConsulta($sql, [
                    $id_donante,
                    //$id_categoria,
                    $nombre,
                    $descripcion,
                    $precio,
                    $stock,
                    $imagen_url,
                    $fecha_caducidad,
                    $porcentaje_descuento,
                    $estado
                ]);

                // Mostrar mensaje de éxito con imagen y redirección automática
                echo '
            <div style="max-width:500px; margin:50px auto; padding:20px; background-color:#eafaf1; border:2px solid #2ecc71; border-radius:10px; text-align:center; box-shadow:0 4px 12px rgba(0,0,0,0.08); font-family:Segoe UI, sans-serif;">
                <img src="../../public/img/correcto.png" alt="Correcto" style="width:60px; margin-bottom:15px;">
                <p style="font-size:18px; color:#2d6a4f; margin:10px 0;">✅ Producto guardado correctamente.</p>
                <p>El producto se mostrará públicamente después de ser aprobado por el moderador.</p>
                <p>Serás redirigido en unos segundos...</p>
            </div>

            <script>
                setTimeout(function() {
                 window.top.location.href = "../pageStore/store.php";
                }, 5000); // Redirige en 4 segundos
            </script>
            ';
                exit;

                exit;
            } catch (Exception $e) {
                echo "Error al guardar en la base de datos: " . $e->getMessage();
            }
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        echo "No se ha subido ninguna imagen válida.";
    }
} else {
    echo "Acceso no permitido.";
}
