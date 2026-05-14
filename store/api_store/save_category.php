<?php
require '../../config/db.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $nombre = $_POST['nombre'];
    
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Preparar SQL
    $sql = "INSERT INTO categorias (nombre, activo) 
            VALUES (:nombre, :activo)";

    try {
        ejecutarConsulta($sql, [
            ':nombre' => $nombre,
            ':activo' => $activo
        ]);

        // Redirigir o mostrar mensaje de éxito
        echo '
        <div style="max-width:500px; margin:50px auto; padding:20px; background-color:#eafaf1; border:2px solid #2ecc71; border-radius:10px; text-align:center; box-shadow:0 4px 12px rgba(0,0,0,0.08); font-family:Segoe UI, sans-serif;">
          <img src="../img/correcto.gif" alt="Correcto" style="width:60px; margin-bottom:15px;">
          <p style="font-size:18px; color:#2d6a4f; margin:10px 0;">Información actualizada correctamente.</p>
          <p><a href="../pagesAdmin/view_admin.php" style="text-decoration:none; color:#27ae60; font-weight:bold;">Volver al Inicio</a></p>
        </div>';
        exit;
    } catch (Exception $e) {
        echo "❌ Error al guardar la categoría: " . $e->getMessage();
        echo '<script>
                setTimeout(function() {
                    window.location.href = "../pagesAdmin/view_admin.php";
                }, 2000);
              </script>';
    }
} else {
    echo "⚠️ Acceso no permitido.";
}
