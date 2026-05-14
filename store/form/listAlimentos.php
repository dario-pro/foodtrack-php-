<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link a Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap">

    <link rel="stylesheet" href="../css_store/delete_modal.css">
    <link rel="stylesheet" href="../css_store/tablas.css">
    <title>Moderación de Productos</title>

</head>
<body>
<div class="barra-superior">
        <input type="text" id="busqueda" class="busqueda" placeholder="Buscar por  nombre Producto, categoria, Donante...">

    </div>
<table id="tabla-productos">
  <thead>
    <tr>
      <th><input type="checkbox" class="casilla" id="checkbox-principal" onchange="seleccionarTodos()"></th>
      <th>Imagen</th>
      <th>Nombre</th>      
      <th>Donante</th>
      <th>Estado</th>
      <th>Precio</th>
      <th>Stock</th>
      <th>Fecha Caducidad</th>
      <th>Acciones</th>
      <th>Activar</th>
    </tr>
  </thead>
  <tbody id="tbody-productos">
    <tr>
      <td colspan="10" class="loading">Cargando productos...</td>
    </tr>
  </tbody>
</table>


<div class="paginacion"></div>
<!-- Botón opcional para activar productos en lote -->
<div class="container-btn-acciones" id="acciones-btn">
    <button class="btn-accion-activar" onclick="activarSeleccionados()" >
        Activar Seleccionados
    </button>
    
</div>
<!-- MODAL PARA EDITAR ALIMENTOS -->
<div id="modalEditar" class="custom-modal">
  <div class="custom-modal-content">
    <span class="custom-close" id="cerrarEditar">&times;</span>
    <iframe id="iframe-editar" src="" frameborder="0"></iframe>
  </div>
</div>
 <!-- modal de eliminar -->
  <div id="modal-delete-overlay" class="modal-overlay">
    <div class="modal-delete">
        <div class="modal-icon"></div>
        <h3 class="modal-title">¿Confirmar eliminación?</h3>
        <p class="modal-message">
            ¿Estás seguro de que deseas eliminar este producto?
        </p>
        
        <div class="producto-info">
            <div class="producto-nombre" id="producto-nombre-display"></div>
            <div class="local-nombre" id="local-nombre-display"></div>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-modal btn-cancel" onclick="cerrarModalEliminar()">
                Cancelar
            </button>
            <button type="button" class="btn-modal btn-delete" id="btn-confirm-delete">
                Eliminar
            </button>
        </div>
    </div>
</div>


<script src="../js_store/loadTableAlimentos.js"></script>
</body>
</html>