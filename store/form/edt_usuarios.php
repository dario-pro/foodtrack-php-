<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link a Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap">
    <link rel="stylesheet" href="../css_store/edt_usuarios.css">
    <title>editar usuarios</title>
</head>

<body>
    <div class="form-wrapper">
        <div class="form-container">
            <h2 class="title_resgister">Actualizar Usuario</h2>
            <form class="register-form" id="formUsuarios" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="id_usuario" name="id_usuario">

                <div class="input-group nombre">
                    <label for="nombre">Nombres *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Escriba su nombre" required>
                </div>
                <div class="input-group apellido">
                    <label for="apellido">Apellidos *</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Escriba su apellido" required>
                </div>
                <div class="input-group telefono">
                    <label for="telefono">Teléfono*</label>
                    <input type="text" id="telefono" name="telefono" placeholder="Escriba su teléfono" required>
                </div>

                <div class="input-group direccion">
                    <label for="direccion">Dirección*</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Escriba su dirección" required>
                </div>



                <div class="input-group sector hidden">
                    <label for="sector">Sector *</label>
                    <select id="sector" name="sector" required>
                        <option value="" disabled selected>Sector del local</option>
                        <option value="norte">Norte</option>
                        <option value="sur">Sur</option>
                        <option value="centro">Centro</option>
                        <option value="oeste">Oeste</option>
                        <option value="este">Este</option>
                        <option value="no_definido">Sin especificar</option>

                    </select>
                </div>

                <div class="input-group email">
                    <label for="email">E-mail*</label>
                    <input type="text" id="email" name="email" placeholder="Escriba su E-mail" required>
                </div>
                <div class="input-group clave">
                    <label for="clave">Contraseña*</label>
                    <input type="password" id="clave" name="clave" placeholder="Escriba su contraseña">
                </div>

                <!--                 
                aqui poner un if para que solo el admin puedo modificar el rol -->
                <div class="input-group rol">
                    <label for="id_rol">Selecciona tu tipo de Cuenta *</label>
                    <select id="id_rol" name="id_rol" required>
                        <option value="" disabled selected>Categoría de tu negocio</option>
                        <option value="3">Comerciante</option>
                        <option value="4">Consumidor</option>

                    </select>
                </div>
                <div class="input-group nombre-local hidden">
                    <label for="nombre_local">Nombre del Local *</label>
                    <input type="text" id="nombre_local" name="nombre_local" placeholder="Nombre del Local" required>
                </div>

                <!-- aqui hacer lo mis mo que los gin solo si es dodante se carge eta parte  o solo mos NA -->
                <div class="input-group tipo-local hidden">
                    <label for="tipo-local">Categoria del Negocio *</label>
                    <select id="tipo-local" name="tipo-local" required>
                        <option value="" disabled selected>Categoría de tu negocio</option>
                        <option value="1">Fruteria</option>
                        <option value="2">Comida</option>
                        <option value="3">Panadería</option>

                    </select>
                </div>
                <div class="input-group imagen hidden">
                    <label for="imagen">Imagen del Local:</label>
                    <div class="file-upload" id="fileUploadArea">
                        <img id="preview" alt="Vista previa de imagen" />
                        <div class="file-upload-content">
                            <small class="hint">Tamaño recomendado: entre 800×600 y 1200×800 px (Máximo 5MB)</small>
                        </div>
                        <div class="image-overlay">
                            <button type="button" class="change-image-btn" onclick="document.getElementById('imagen').click();">
                                Cambiar Imagen
                            </button>
                        </div>
                    </div>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>

                <!-- <div class="input-group imagen">
                    <label for="imagen">Imagen del Local:</label>
                    <div class="file-upload" onclick="document.getElementById('imagen').click();">
                        <img id="preview" alt="Vista previa de imagen" />
                        <div class="file-upload-content">
                            <label for="imagen" class="file-upload-label">
                                📷 Seleccionar Imagen
                            </label>
                            <small class="hint">Tamaño recomendado: entre 800×600 y 1200×800 px (Máximo 5MB)</small>
                        </div>
                    </div>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div> -->


                <div class="btn-group">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js_store/accion_usuarios.js"></script>

</body>

</html>