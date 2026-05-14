<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario Alimentos</title>
  <link rel="stylesheet" href="../css_store/alimentos.css">

</head>

<body>
  <div class="form-wrapper">
    <div class="form-container">
      <h2 class="alimentos-title">Nuevo Alimento</h2>
      <form class="alimentos-form" action="../api_store/save_alimentos.php" method="POST" id="formAlimentos" enctype="multipart/form-data">

        <div class="input-group nombre">
          <label for="nombre">Nombre del Alimento <span class="text-danger">*</span></label>
          <input type="text" id="nombre" name="nombre" placeholder="Escriba el nombre del alimento" required>
        </div>

        <div class="input-group precio">
          <label for="precio">Precio <span class="text-danger">*</span></label>
          <input type="number" step="0.01" id="precio" name="precio" placeholder="Precio" required>
        </div>
        <div class="input-group stock">
          <label for="stock">Stock <span class="text-danger">*</span></label>
          <input type="number" id="stock" name="stock" placeholder="Stock" required>
        </div>
        <div class="input-group porcentaje-descuento">
          <label for="porcentaje_descuento">Porcentaje de Descuento:</label>
          <input type="number" id="porcentaje_descuento" name="porcentaje_descuento" min="0" max="100" placeholder="% Descuento">
        </div>
        
        <div class="input-group descripcion">
          <label for="descripcion">Descripción <span class="text-danger">*</span></label>
          <input type="text" id="descripcion" name="descripcion" placeholder="Descripción del alimento" required>
        </div>
        <div class="input-group fecha-caducidad">
          <label for="fecha_caducidad">Fecha de Caducidad:</label>
          <input type="date" id="fecha_caducidad" name="fecha_caducidad" required>
        </div>
        
        <div class="input-group estado">
          <label for="estado">Estado del Alimento <span class="text-danger">*</span></label>
          <select id="estado" name="estado" required>
            <option value="" disabled selected>Selecciona un Estado</option>
            <option value="fresco">Fresco</option>
            <option value="apto_consumo">Apto para consumo</option>
            <option value="proximo_a_vencer">Próximo a vencer</option>
            <option value="caducado">Caducado</option>
          </select>
        </div>
        <div class="input-group imagen">
          <div class="file-upload">
            <label for="imagen">Subir imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>
          </div>
        </div>
        <div class="input-group message-footer">
          <p>El alimento está <strong>inactivo</strong> hasta que el moderador lo apruebe.</p>
        </div>


        <div class="btn-group">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" onclick="window.top.location.href='../pageStore/store.php'">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  <script src="../js_store/loadForm.js"></script>

</body>

</html>

