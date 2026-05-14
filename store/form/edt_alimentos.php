 <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Alimento</title>
  <link rel="stylesheet" href="../css_store/edt_alimento.css">
</head>
<body>

    <div class="form-wrapper">
        <div class="form-container">
            <h2 class="titulo-editar">Actualizar Alimento</h2>
            <form id="formAlimentos" enctype="multipart/form-data">
                <input type="hidden" id="id_producto" name="id_producto">

                <div class="input-group nombre">
                    <label for="nombre">Nombre del Alimento *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Escriba el nombre del alimento" required>
                </div>
                <div class="input-group fecha-caducidad">
                    <label for="fecha_caducidad">Fecha de Caducidad:</label>
                    <input type="date" id="fecha_caducidad" name="fecha_caducidad" required>
                </div>

                <div class="input-group precio">
                    <label for="precio">Precio *</label>
                    <input type="number" step="0.01" id="precio" name="precio" placeholder="Precio" required>
                </div>

                <div class="input-group stock">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" placeholder="Stock" required>
                </div>
                <div class="input-group descuento">
                    <label for="porcentaje_descuento">(0-100)% Descuento:</label>
                    <input type="number" id="porcentaje_descuento" name="porcentaje_descuento" min="0" max="100">
                </div>
                <div class="input-group descripcion">
                    <label for="descripcion">Descripción *</label>
                    <input type="text" id="descripcion" name="descripcion" placeholder="Descripción del alimento" required>
                </div>
                <div class="input-group estado">
                    <label for="estado">Estado del Alimento *</label>
                    <select id="estado" name="estado" required>
                        <option value="" disabled selected>Selecciona un Estado</option>
                        <option value="fresco">Fresco</option>
                        <option value="apto_consumo">Apto para consumo</option>
                        <option value="proximo_a_vencer">Próximo a vencer</option>
                        <option value="caducado">Caducado</option>
                    </select>
                </div>
                <div class="input-group imagen">
                    <label for="imagen">Imagen:</label>
                    <div class="file-upload">
                        <img id="preview" alt="Vista previa de imagen" />
                        <input type="file" id="imagen" name="imagen" accept="image/*">
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarEditar()">Cancelar</button>
                </div>
            </form>

        </div>
    </div>

    <script src="../js_store/accion_alimentos.js"></script>

</body>
</html>
 

