<?php
require_once '../config/db.php';

// Incluir la configuración de la base de datos
session_start();

// Variables para almacenar errores
$email_error = '';
$local_error = '';
$show_register_form = false;

// INICIO DE SESIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];

    try {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1";
        $stmt = ejecutarConsulta($sql, [$email]);
        $usuarioData = $stmt->fetch();

        if (!$usuarioData) {
            $emailLogin_error = "Usuario no encontrado o inactivo";
        } elseif (!password_verify($contrasena, $usuarioData['password'])) {
            $clave_error = "Contraseña incorrecta. Intente denuevo";
        } else {
            // Autenticación exitosa
            $_SESSION['email'] = $usuarioData['email'];
            $_SESSION['nombre'] = $usuarioData['nombre'];
            $_SESSION['id_rol'] = $usuarioData['id_rol'];
            $_SESSION['id'] = $usuarioData['id'];


            echo "<script>window.top.location.href='pageStore/store.php';</script>";
            exit;
        }
    } catch (Exception $e) {
        echo "<script>alert('Error al iniciar sesión: " . $e->getMessage() . "');</script>";
    }
}

// REGISTRO DE USUARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $show_register_form = true;

    $nombre     = trim($_POST['nombre']);
    $apellido   = trim($_POST['apellido']);
    $email      = trim($_POST['email']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $direccion  = trim($_POST['direccion']);
    $telefono   = trim($_POST['telefono']);
    $id_rol     = $_POST['id_rol'];
    $imagen_url = null; // Solo se usará si es comerciante

    try {
        // 1. Verificar si el email ya existe
        $sqlCheckEmail = "SELECT id FROM usuarios WHERE email = ?";
        $stmtCheckEmail = ejecutarConsulta($sqlCheckEmail, [$email]);
        $existingUser = $stmtCheckEmail->fetch();
        if ($existingUser) {
            $email_error = "Este correo ya está registrado";
        }

        // 2. Si es comerciante, verificar si el nombre del local ya existe
        if ($id_rol == 3) {
            $nombre_local = trim($_POST['nombre_local']);
            $sqlCheckLocal = "SELECT id FROM locales WHERE nombre_local = ?";
            $stmtCheckLocal = ejecutarConsulta($sqlCheckLocal, [$nombre_local]);
            $existingLocal = $stmtCheckLocal->fetch();
            if ($existingLocal) {
                $local_error = "Nombre de local ya registrado";
            }

            // Subida de imagen SOLO para locales
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $directorioDestino = '../update/';
                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0755, true); // Crear carpeta si no existe
                }

                $nombreArchivo = basename($_FILES['imagen']['name']);
                $rutaDestino = $directorioDestino . time() . '_' . $nombreArchivo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    // Guardar ruta relativa (sin ../)
                    $imagen_url = str_replace('../', '', $rutaDestino);
                }
            }
        }

        // 3. Solo insertar si NO HAY errores
        if (empty($email_error) && empty($local_error) && empty($telefono_error)) {
            // Registrar usuario
            $sql = "INSERT INTO usuarios (id_rol,nombre, apellido, email, password, direccion, telefono) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            ejecutarConsulta($sql, [$id_rol, $nombre, $apellido, $email, $contrasena, $direccion, $telefono]);

            $sqlLastId = "SELECT LAST_INSERT_ID() as id";
            $stmt = ejecutarConsulta($sqlLastId, []);
            $newUser = $stmt->fetch();
            $id_usuario = $newUser['id'];

            // Si es comerciante, insertar también en locales con imagen
            if ($id_rol == 3) {
                $id_tipo_local = $_POST['id_tipo_local'];
                $sector = $_POST['sector'];
                $sqlLocal = "INSERT INTO locales (id_usuario, id_tipo_local, nombre_local, sector, direccion, telefono, imagen_url) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
                ejecutarConsulta($sqlLocal, [$id_usuario, $id_tipo_local, $nombre_local, $sector, $direccion, $telefono, $imagen_url]);
            }

            echo "<script>alert('Registro exitoso. Espera la aprobación del moderador para acceder.');</script>";
            // Limpiar variables después del éxito
            $email_error = '';
            $local_error = '';
            $telefono_error = '';
            $show_register_form = false;
        }

    } catch (Exception $e) {
        echo "<script>alert('Error al registrar: " . $e->getMessage() . "');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="container <?php echo $show_register_form ? 'active' : ''; ?>">

        <!-- Formulario de Login -->
        <div class="form-container">
            <form class="form login-form" action="" method="POST">
                <input type="hidden" name="login" value="1">
                <h2 class="titulo">Iniciar Sesión</h2>
                <div class="social-networks">
                    <ion-icon name="logo-facebook"></ion-icon>
                    <ion-icon name="logo-tiktok"></ion-icon>
                    <ion-icon name="logo-instagram"></ion-icon>
                </div>
                <div class="input-group <?php echo !empty($emailLogin_error) ? 'emailLogin-error' : ''; ?>">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" placeholder="E-mail" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    <?php if (!empty($emailLogin_error)): ?>
                        <small class="error-message email"><?php echo $emailLogin_error; ?></small>
                    <?php endif; ?>
                </div>
                <div class="input-group <?php echo !empty($clave_error) ? 'has-error' : ''; ?>">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="contrasena" placeholder="Password" required>
                    <?php if (!empty($clave_error)): ?>
                        <small class="error-message clave"><?php echo $clave_error; ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-button">
                    <button class="btn-iniciar" type="submit">continuar</button>
                    <button class="btn-guardar" type="submit">Crear Cuenta</button>
                </div>
            </form>
        </div>

        <!-- Formulario de Registro -->
        <div class="form-container">
            <form class="form register-form" action="" method="POST" enctype="multipart/form-data">
                <h2 class="title_resgister">Crea tu cuenta</h2>
                <input type="hidden" name="register" value="1">
                <div class="social-networks icon">
                    <ion-icon name="logo-facebook"></ion-icon>
                    <ion-icon name="logo-tiktok"></ion-icon>
                    <ion-icon name="logo-instagram"></ion-icon>
                </div>

                <div class="input-select rol">
                    <select name="id_rol" id="id_rol" class="select-input" required>
                        <option value="">Selecciona tu tipo de Cuenta</option>
                        <option value="3" <?php echo (isset($_POST['id_rol']) && $_POST['id_rol'] == '3') ? 'selected' : ''; ?>>Comerciante</option>
                        <option value="4" <?php echo (isset($_POST['id_rol']) && $_POST['id_rol'] == '4') ? 'selected' : ''; ?>>Consumidor</option>
                    </select>
                </div>

                <div class="input-group local-fields Nlocal <?php echo !empty($local_error) ? 'has-error' : ''; ?>"
                    style="display:<?php echo (isset($_POST['id_rol']) && $_POST['id_rol'] == '3') ? 'flex' : 'none'; ?>;">
                    <ion-icon name="business-outline"></ion-icon>
                    <input type="text" name="nombre_local" placeholder="Nombre del Local"
                        value="<?php echo isset($_POST['nombre_local']) ? htmlspecialchars($_POST['nombre_local']) : ''; ?>">
                    <?php if (!empty($local_error)): ?>
                        <small class="error-message local"><?php echo $local_error; ?></small>
                    <?php endif; ?>
                </div>

                <div class="input-select local-fields" style="display:<?php echo (isset($_POST['id_rol']) && $_POST['id_rol'] == '3') ? 'block' : 'none'; ?>;">
                    <select name="id_tipo_local" class="select-input">
                        <option value="">Categoría de tu negocio</option>
                        <option value="1" <?php echo (isset($_POST['id_tipo_local']) && $_POST['id_tipo_local'] == '1') ? 'selected' : ''; ?>>Fruteria</option>
                        <option value="2" <?php echo (isset($_POST['id_tipo_local']) && $_POST['id_tipo_local'] == '2') ? 'selected' : ''; ?>>Comida</option>
                        <option value="3" <?php echo (isset($_POST['id_tipo_local']) && $_POST['id_tipo_local'] == '3') ? 'selected' : ''; ?>>Panadería</option>
                    </select>
                </div>

                <div class="input-group nombre">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="nombre" placeholder="Nombre"
                        value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                </div>

                <div class="input-group apellido">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="apellido" placeholder="Apellido"
                        value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" required>
                </div>

                <div class="input-group email <?php echo !empty($email_error) ? 'has-error' : ''; ?>">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" placeholder="E-mail"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    <?php if (!empty($email_error)): ?>
                        <small class="error-message email"><?php echo $email_error; ?></small>
                    <?php endif; ?>
                </div>

                <div class="input-group clave">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="contrasena" placeholder="Password" required>
                </div>

                <div class="input-group direccion">
                    <ion-icon name="location-outline"></ion-icon>
                    <input type="text" name="direccion" placeholder="Dirección"
                        value="<?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?>" required>
                </div>
                <div class="input-select local-fields sector" style="display:<?php echo (isset($_POST['id_rol']) && $_POST['id_rol'] == '3') ? 'block' : 'none'; ?>;">
                    <select name="sector" class="select-input">
                        <option value="">Sector del local</option>
                        <option value="norte" <?php echo (isset($_POST['sector']) && $_POST['sector'] == 'norte') ? 'selected' : ''; ?>>Norte</option>
                        <option value="sur" <?php echo (isset($_POST['sector']) && $_POST['sector'] == 'sur') ? 'selected' : ''; ?>>Sur</option>
                        <option value="este" <?php echo (isset($_POST['sector']) && $_POST['sector'] == 'este') ? 'selected' : ''; ?>>Este</option>
                        <option value="oeste" <?php echo (isset($_POST['sector']) && $_POST['sector'] == 'oeste') ? 'selected' : ''; ?>>Oeste</option>
                        <option value="centro" <?php echo (isset($_POST['sector']) && $_POST['sector'] == 'centro') ? 'selected' : ''; ?>>Centro</option>
                        <option value="no_definido" <?php echo (isset($_POST['sector']) && $_POST['sector'] == 'no_definido') ? 'selected' : ''; ?>>Sin especificar</option>
                    </select>
                </div>

                <div class="input-group telefono">
                    <ion-icon name="call-outline"></ion-icon>
                    <input type="text" name="telefono" id="telefono" placeholder="Teléfono (10 dígitos)"
                        value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>" maxlength="10" required>
                    <small class="error-telefono"></small>
                </div>
                
                    <div class="file-upload local-fields img">
                    <label for="imagen">Subir imagen del Local:</label>
                    <input  type="file" id="imagen" name="imagen" accept="image/*" required>
                    <small class="hint">Tamaño recomendado: entre 800×600 y 1200×800 px</small>
                </div>
                

                <div class="form-button btnregistarr">
                    <button class="btn-guardar" type="submit">Crear Cuenta</button>
                </div>
            </form>
        </div>

        <div class="container-welcome">
            <div class="welcome-register welcome">
                <h2>Bienvenido a SecondBite</h2>
                <p>Por favor, espera un momento mientras un moderador active tu cuenta.</p>
                <button class="btn-guardar" id="btn-register">Crear Cuenta</button>
            </div>

            <div class="welcome-login welcome">
                <h2>Bienvenido de nuevo</h2>
                <p>Ahorra en alimentos que necesitan venderse pronto.</p>
                <button class="btn-iniciar" id="btn-iniciar">Continuar</button>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../js/login.js"></script>
</body>

</html>