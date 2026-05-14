
let productos = [];

// Cargar productos al inicializar la página
document.addEventListener('DOMContentLoaded', function () {
    cargarProductos();
});

// Función para cargar productos desde la API
async function cargarProductos() {
    try {
        const response = await fetch('../api_store/getInactivo_alimentos.php');
        const data = await response.json();

        if (data.success) {
            productos = data.data;
            renderizarTabla();
            inicializarBusquedaYPaginacion();
        } else {
            mostrarError('Error al cargar productos: ' + data.error);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

// Convierte el formato de fecha DD/MM/YYYY a YYYY-MM-DD
function convertirFecha(fecha) {
  const partes = fecha.split('/'); // Divide la fecha en partes [DD, MM, YYYY]
  return `${partes[2]}/${partes[1]}/${partes[0]}`; // Devuelve la fecha en formato con los /
}
// Función para renderizar la tabla con los datos
function renderizarTabla() {
  const tbody = document.getElementById('tbody-productos');

  if (!tbody) return;

  if (productos.length === 0) {
    tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;">No hay productos registrados</td></tr>';
    return;
  }

  tbody.innerHTML = productos.map((producto, index) => {
    const estadoClass = getEstadoClass(producto.estado);
    // Convierte la fecha de la base de datos de DD/MM/YYYY a YYYY-MM-DD
    const fechaCaducidad = convertirFecha(producto.fecha_caducidad);
    const fechaFormateada = new Date(fechaCaducidad).toLocaleDateString('es-ES');
    const precioFormateado = parseFloat(producto.precio).toFixed(2);
    const filaClase = !producto.activo ? 'texto-apagado' : '';
    const nombreClase = !producto.activo ? 'tachado' : '';
    const checked = producto.activo ? 'checked' : '';

    return `
      <tr id="fila-${index}" class="${filaClase}" data-producto-id="${producto.id}">
        <td><input type="checkbox" class="checkbox-fila" ${producto.activo ? 'disabled' : ''}></td>
        <td>${producto.imagen_url ? `<img src="/proyectoComida/store/${producto.imagen_url}" class="producto-imagen">` : 'Sin imagen'}</td>
        <td><strong class="${nombreClase}">${producto.nombre}</strong><br><small>${producto.descripcion || ''}</small></td>
        <td>${producto.donante}<br><small>${producto.contacto_donante || ''}</small></td>
        <td><span class="estado-badge ${estadoClass}">${formatearEstado(producto.estado)}</span></td>
        <td>${precioFormateado}</td>
        <td>${producto.stock}</td>
        <td>${fechaFormateada}</td>
        <td>
          <div class="acciones-container">
            <button class="btn-accion btn-editar ${producto.activo ? '' : 'deshabilitado'}" id="btn-editar-${producto.id}">Edit</button>
            <button class="btn-accion btn-eliminar" onclick="eliminarProducto(${producto.id}, ${index})">
            <span class="material-symbols-outlined">delete</span>        
            </button>
          </div>
        </td>
        <td>
          <label class="interruptor">
            <input class="interruptor-fila" type="checkbox" onchange="alternarEstadoProducto(${index}, ${producto.id})" ${checked}>
            <span class="deslizador"></span>
          </label>
        </td>
      </tr>
    `;
  }).join('');

  // Asignar listener para abrir modal de edición
  productos.forEach(producto => {
    const btn = document.getElementById(`btn-editar-${producto.id}`);
    if (btn) {
      btn.addEventListener('click', () => {
        abrirModalEditar(`../form/edt_alimentos.php?id=${producto.id}`);
      });
    }
  });
}


// Función para obtener la clase CSS del estado
function getEstadoClass(estado) {
    const clases = {
        'fresco': 'estado-fresco',
        'apto_consumo': 'estado-apto',
        'proximo_a_vencer': 'estado-proximo',
        'caducado': 'estado-caducado'
    };
    return clases[estado] || 'estado-apto';
}

// Función para formatear el texto del estado
function formatearEstado(estado) {
    const estados = {
        'fresco': 'Fresco',
        'apto_consumo': 'Apto',
        'proximo_a_vencer': 'Próximo a vencer',
        'caducado': 'Caducado'
    };
    return estados[estado] || estado;
}

// Función para mostrar errores
function mostrarError(mensaje) {
    const tbody = document.getElementById('tbody-productos');
    tbody.innerHTML = `<tr><td colspan="10" class="error">${mensaje}</td></tr>`;
}

// Función para alternar el estado de activación de un producto
async function alternarEstadoProducto(indice, idProducto) {
    const fila = document.getElementById(`fila-${indice}`);
    const interruptor = fila.querySelector('.interruptor-fila');
    const checkboxFila = fila.querySelector('.checkbox-fila');
    // para que se mantenga los botones de acciones deshabilitados si el producto está inactivo
    const contenedorAcciones = fila.querySelector('.acciones-container');
    const productoActivo = interruptor.checked; // El estado actual del interruptor (activo o inactivo)

    try {
        const response = await fetch('../api_store/actualizarAlimentos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_producto: idProducto,
                activo: interruptor.checked,
                id_moderador: 1, // Aquí deberías obtener el ID del moderador desde la sesión
                comentario: interruptor.checked ? 'Producto aprobado por moderador' : 'Producto desactivado'
            })
        });

        if (response.ok) {
            const data = await response.json();

            if (data.success) {
                // Actualizar el estado del producto en el array
                productos[indice].activo = interruptor.checked;

                if (interruptor.checked) {
                    // Si el producto se activa, eliminamos la clase deshabilitada
                    contenedorAcciones.classList.remove('deshabilitado');
                    // Producto activado - quitar estilos de inactivo
                    fila.classList.remove('texto-apagado');
                    fila.querySelector('strong').classList.remove('tachado');
                    checkboxFila.disabled = true; // DESHABILITAR checkbox para productos activos
                    checkboxFila.checked = false;
                    fila.classList.remove('resaltado');

                    mostrarToast('Producto activado correctamente');
                    // alert('Producto activado correctamente' + 
                    //   (data.message ? '. ' + data.message : ''));
                    // Recargar página después de activar individualmente con esto 
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 100);

                } else {
                    // Si el producto se desactiva, agregamos la clase deshabilitada al contenedor de acciones
                    contenedorAcciones.classList.add('deshabilitado');
                    // Producto desactivado - aplicar estilos de inactivo
                    fila.classList.add('texto-apagado');
                    fila.querySelector('strong').classList.add('tachado');
                    checkboxFila.disabled = false; // HABILITAR checkbox para productos inactivos
                    mostrarToast('Producto desactivado correctamente');
                    // alert('Producto desactivado correctamente' +
                    //   (data.message ? '. ' + data.message : ''));
                }

                actualizarCheckboxPrincipal();
                // Recargar página después de activar individualmente con esto 
                await cargarProductos();
            } else {
                // Revertir el interruptor si hay error
                interruptor.checked = !interruptor.checked;
                alert('Error al actualizar el producto: ' + data.error);
            }
        } else {
            // Revertir el interruptor si hay error HTTP
            interruptor.checked = !interruptor.checked;
            alert(`Error HTTP ${response.status} al actualizar el producto`);
        }
    } catch (error) {
        // Revertir el interruptor si hay error
        interruptor.checked = !interruptor.checked;
        alert('Error de conexión 444: ' + error.message);
        console.error('Error de conexión:', error);
    }
}

// Función para alternar la selección de una fila individual
function alternarSeleccionFila(indice) {
    const fila = document.getElementById(`fila-${indice}`);
    const checkboxFila = fila.querySelector('.checkbox-fila');

    if (checkboxFila.checked) {
        fila.classList.add('resaltado');
    } else {
        fila.classList.remove('resaltado');
    }

    actualizarCheckboxPrincipal();
}

// Función para seleccionar/deseleccionar todas las filas
function seleccionarTodos() {
    const checkboxPrincipal = document.getElementById('checkbox-principal');
    const checkboxesFilas = document.querySelectorAll('.checkbox-fila:not(:disabled)');

    checkboxesFilas.forEach((checkbox) => {
        checkbox.checked = checkboxPrincipal.checked;

        const fila = checkbox.closest('tr');
        if (checkbox.checked) {
            fila.classList.add('resaltado');
        } else {
            fila.classList.remove('resaltado');
        }
    });
    actualizarCheckboxPrincipal()
}

// Función para actualizar el estado del checkbox principal
function actualizarCheckboxPrincipal() {
    const checkboxPrincipal = document.getElementById('checkbox-principal');
    const checkboxesActivos = document.querySelectorAll('.checkbox-fila:not(:disabled)');
    const checkboxesMarcados = document.querySelectorAll('.checkbox-fila:not(:disabled):checked');

    if (checkboxesActivos.length === 0) {
        checkboxPrincipal.indeterminate = false;
        checkboxPrincipal.checked = false; // Si no hay productos activos, desmarcar
    } else if (checkboxesMarcados.length === checkboxesActivos.length) {
        checkboxPrincipal.indeterminate = false;
        checkboxPrincipal.checked = true; // Si todos los productos activos están seleccionados, marcar
    } else if (checkboxesMarcados.length > 0) {
        checkboxPrincipal.indeterminate = true; // Si algunos productos están seleccionados, poner en indeterminado
        checkboxPrincipal.checked = false; // No marcarlo completamente
    } else {
        checkboxPrincipal.indeterminate = false;
        checkboxPrincipal.checked = false; // Si ninguno está seleccionado, desmarcar
    }
}

// Función para activar productos seleccionados en lote - CORREGIDA
async function activarSeleccionados() {
    // Solo obtener checkboxes marcados que NO estén deshabilitados (productos inactivos)
    const checkboxesMarcados = document.querySelectorAll('.checkbox-fila:checked:not(:disabled)');

    if (checkboxesMarcados.length === 0) {
        mostrarToast('Selecciona al menos un producto inactivo', 'error');
        return;
    }

    if (!confirm(`¿Estás seguro de activar ${checkboxesMarcados.length} producto(s)?`)) {
        return;
    }

    const productosActivar = [];

    // Iterar sobre los checkboxes marcados y obtener los datos correctos
    checkboxesMarcados.forEach((checkbox) => {
        const fila = checkbox.closest('tr');
        const indice = parseInt(fila.id.split('-')[1]);
        const productoId = parseInt(fila.dataset.productoId);
        const productoActivo = fila.dataset.productoActivo === 'true';
 console.log('Fila:', fila);
    console.log('ID Producto:', productoId);
    console.log('Activo:', productoActivo);
        // Verificar que el producto existe en el array y está inactivo
        if (productos[indice] && !productoActivo) {
            productosActivar.push({
                indice: indice,
                id: productoId,
                producto: productos[indice]
            });
        }
    });

    if (productosActivar.length === 0) {
        mostrarToast('No hay productos inactivos válidos seleccionados', 'error');
        return;
    }

    console.log('Productos a activar:', productosActivar); // Para debug

    try {
        let exitosos = 0;
        let errores = 0;
        let errorDetails = [];

        for (const item of productosActivar) {
            try {
                console.log(`Activando producto ID: ${item.id}`); // Para debug

                const response = await fetch('../api_store/actualizarAlimentos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_producto: item.id,
                        activo: true,
                        id_moderador: 1,
                        comentario: 'Producto aprobado en lote por moderador'
                    })
                });

                console.log(`Status HTTP para producto ${item.id}: ${response.status}`); // Para debug

                if (response.ok) {
                    try {
                        const data = await response.json();
                        console.log(`Respuesta JSON para producto ${item.id}:`, data); // Para debug

                        if (data.success === true) {
                            exitosos++;
                            console.log(`Producto ${item.id} activado correctamente`);
                        } else {
                            errores++;
                            const errorMsg = data.error || 'Error desconocido en la API';
                            errorDetails.push(`Producto ${item.id}: ${errorMsg}`);
                            console.error(`Error en API para producto ${item.id}:`, data);
                        }
                    } catch (jsonError) {
                        errores++;
                        const responseText = await response.text();
                        errorDetails.push(`Producto ${item.id}: Error de JSON - ${responseText}`);
                        console.error(`❌ Error parseando JSON para producto ${item.id}:`, jsonError, responseText);
                    }
                } else {
                    errores++;
                    const responseText = await response.text();
                    errorDetails.push(`Producto ${item.id}: HTTP ${response.status} - ${responseText}`);
                    console.error(`❌ Error HTTP ${response.status} para producto ${item.id}:`, responseText);
                }
            } catch (error) {
                errores++;
                errorDetails.push(`Producto ${item.id}: Error de conexión - ${error.message}`);
                console.error(`❌ Error de conexión para producto ${item.id}:`, error);
            }
        }

        console.log(`Resumen: ${exitosos} exitosos, ${errores} errores`);
        console.log('Detalles de errores:', errorDetails);

        // Mostrar resultado y recargar página si hubo éxitos
        if (exitosos > 0) {
            mostrarToast(`${exitosos} producto(s) activado(s) correctamente` +
             (errores > 0 ? ` (${errores} errores)` : ''), 'success');
                console.log(`${exitosos} producto(s) activado(s) correctamente` +
                (errores > 0 ? ` (${errores} errores)` : ''));
            // Recargar la página automáticamente para reflejar los cambios
            await cargarProductos();
            // setTimeout(() => {
            //     location.reload();
            // }, 100);

        } else {
            alert(`No se pudo activar ningún producto. Errores: ${errores}\n\nRevisa la consola para más detalles.`);
            console.error('❌ DETALLES DE ERRORES:');
            errorDetails.forEach(detail => console.error(detail));
        }

    } catch (error) {
        alert('Error general al activar productos: ' + error.message);
        console.error('❌ Error general:', error);
    }

}
//CODIGO PARA LA PAGINACIÓN Y BUSQUEDA
// Funciones para búsqueda + paginación JS
let registrosPorPagina = 3;
let paginaActual = 1;
let filasOriginales = [];

function inicializarBusquedaYPaginacion() {
    const inputBusqueda = document.getElementById('busqueda');
    const tabla = document.querySelector('#tabla-productos tbody');
    filasOriginales = Array.from(document.querySelector('#tabla-productos tbody').rows);

    function filtrarYPaginar() {
        const termino = inputBusqueda.value.toLowerCase();
        const filasFiltradas = filasOriginales.filter(fila =>
            Array.from(fila.cells).some(celda =>
                celda.textContent.toLowerCase().includes(termino)
            )
        );

        const totalPaginas = Math.ceil(filasFiltradas.length / registrosPorPagina);
        if (paginaActual > totalPaginas) paginaActual = totalPaginas || 1;

        tabla.innerHTML = '';
        const inicio = (paginaActual - 1) * registrosPorPagina;
        const filasPagina = filasFiltradas.slice(inicio, inicio + registrosPorPagina);
        filasPagina.forEach(fila => tabla.appendChild(fila));

        renderizarPaginacion(totalPaginas);
    }

    function renderizarPaginacion(totalPaginas) {
        const contenedor = document.querySelector('.paginacion');
        contenedor.innerHTML = '';

        for (let i = 1; i <= totalPaginas; i++) {
            const link = document.createElement('a');
            link.href = '#';
            link.textContent = i;
            link.className = i === paginaActual ? 'actual' : '';
            link.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActual = i;
                filtrarYPaginar();
            });
            contenedor.appendChild(link);
        }
    }

    inputBusqueda.addEventListener('input', () => {
        paginaActual = 1;
        filtrarYPaginar();
    });

    filtrarYPaginar();
}


// === Funciones Modal ===
function abrirModalEditar(url) {
    const modal = document.getElementById("modalEditar");
    const iframe = document.getElementById("iframe-editar");

    // asignar la URL del formulario de edición
    iframe.src = url;

    // mostrar modal
    modal.style.display = "flex";

    // ocultar tabla
    document.getElementById("tabla-productos").style.display = "none";
    document.querySelector(".barra-superior").style.display = "none";
    document.querySelector(".paginacion").style.display = "none";
    document.getElementById("acciones-btn").style.display = "none";
}

function cerrarModalEditar() {
    const modal = document.getElementById("modalEditar");
    modal.style.display = "none";

    // mostrar de nuevo la tabla
    document.getElementById("tabla-productos").style.display = "table";
    document.querySelector(".barra-superior").style.display = "flex";
    document.querySelector(".paginacion").style.display = "block";
    document.getElementById("acciones-btn").style.display = "block";
    cargarProductos(); // recargar productos para reflejar cambios
}

// evento para el botón de cerrar en la X
document.addEventListener("DOMContentLoaded", () => {
    const cerrar = document.getElementById("cerrarEditar");
    if (cerrar) {
        cerrar.addEventListener("click", cerrarModalEditar);
    }
});
//EVENTO PARA CERRAR EL MODAL AL HACER CLICK FUERA DEL CONTENIDO
window.addEventListener("click",function(e){
    const modal = document.getElementById("modalEditar");
    if(e.target === modal){
        modal.style.display = "none";
        this.document.getElementById("iframe-editar").src = ""; // limpiar iframe
        // mostrar de nuevo la tabla
        document.getElementById("tabla-productos").style.display = "table";
        document.querySelector(".barra-superior").style.display = "flex";
        document.querySelector(".paginacion").style.display = "block";
        document.getElementById("acciones-btn").style.display = "block";

    }
});

//cerrar modal con la tecla ESC
window.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        cerrarModalEditar();
    }
});


// Escuchar mensajes del iframe para cerrar el modal que viene del editar alimentos
window.addEventListener("message", function(event) {
  if (event.data.action === "cerrarModalEditar") {
    // Cerrar el modal editar
    document.getElementById("modalEditar").style.display = "none";
    // muestra denuevo el contendio del iframe padre  tabla alimentos
    
    document.getElementById("tabla-productos").style.display = "table";
        document.querySelector(".barra-superior").style.display = "flex";
        document.querySelector(".paginacion").style.display = "block";
        document.getElementById("acciones-btn").style.display = "block";
    cargarProductos(); 
  }
});

// ..............FUNCION PARA ELIMINAR...........
let productoAEliminar = null;
/**
 * Mostrar toast de notificación
 */
function mostrarToast(mensaje, tipo = 'success') {
    // Remover toast anterior si existe
    const toastExistente = document.querySelector('.toast');
    if (toastExistente) {
        toastExistente.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.innerHTML = `
        <div class="toast-icon">${tipo === 'success' ? '✓' : '✕'}</div>
        <div class="toast-message">${mensaje}</div>
    `;

    document.body.appendChild(toast);
    
    // Mostrar toast
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Ocultar después de 4 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

/**
 * Abrir modal de confirmación de eliminación
 */
function eliminarProducto(idProducto, index) {
    if (!productos || !productos[index]) {
        mostrarToast('Error: Producto no encontrado', 'error');
        return;
    }
    
    const producto = productos[index];
    productoAEliminar = { id: idProducto, index, nombre: producto.producto_nombre, local_nombre: producto.local_nombre };
    console.log('Producto seleccionado:', producto);
    // Actualizar información del producto en el modal
    document.getElementById('producto-nombre-display').textContent = producto.nombre;
    document.getElementById('local-nombre-display').textContent = `Local: ${producto.nombre_local || 'N/A'}`;
    
    // Mostrar modal
    const modal = document.getElementById('modal-delete-overlay');
    modal.classList.add('show');
    
    // Configurar botón de confirmación
    const btnConfirm = document.getElementById('btn-confirm-delete');
    btnConfirm.onclick = confirmarEliminacion;
    btnConfirm.disabled = false;
    btnConfirm.classList.remove('btn-loading');
    btnConfirm.textContent = 'Eliminar';

    // Enfocar el botón de cancelar
    document.querySelector('.btn-cancel').focus();
    
    // Prevenir scroll del body
    document.body.style.overflow = 'hidden';
}

/**
 * Cerrar modal de eliminación
 */
function cerrarModalEliminar() {
    const modal = document.getElementById('modal-delete-overlay');
    modal.classList.remove('show');
    productoAEliminar = null;
    
    // Restaurar scroll del body
    document.body.style.overflow = '';
}

/**
 * Confirmar eliminación del producto
 */
async function confirmarEliminacion() {
    if (!productoAEliminar) return;

    const btnConfirm = document.getElementById('btn-confirm-delete');
    btnConfirm.disabled = true;
    btnConfirm.classList.add('btn-loading');
    btnConfirm.textContent = 'Eliminando...';

    try {
        const response = await fetch(`../api_store/delete_alimentos.php?id=${productoAEliminar.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        console.log('Respuesta de eliminación:', data);
        if (data.success) {
            // Remover producto del array
            productos.splice(productoAEliminar.index, 1);
            
            //actualiza los datos 
            await cargarProductos();
            // Re-renderizar tabla
            //renderizarTabla();
            
            // Cerrar modal
            cerrarModalEliminar();
            
            // Mensaje personalizado con información del producto
            const mensaje = data.data.accion === 'eliminado' 
                ? `"${data.data.producto_nombre}" eliminado del local "${data.data.local_nombre}"`
                : `"${data.data.producto_nombre}" desactivado (tiene pedidos asociados)`;
                
            mostrarToast(mensaje);
            
            // Actualizar contadores si existen
            if (typeof actualizarContadores === 'function') {
                actualizarContadores();
            }

        } else {
            throw new Error(data.error || 'Error al eliminar el producto');
        }

    } catch (error) {
        console.error('Error al eliminar producto:', error);
        mostrarToast(error.message || 'Error al eliminar el producto', 'error');
        
        // Restaurar botón
        btnConfirm.disabled = false;
        btnConfirm.classList.remove('btn-loading');
        btnConfirm.textContent = 'Eliminar';
    }
}

// Event listeners globales
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('modal-delete-overlay');
            if (modal && modal.classList.contains('show')) {
                cerrarModalEliminar();
            }
        }
    });

    // Cerrar modal al hacer click fuera
    document.getElementById('modal-delete-overlay')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalEliminar();
        }
    });
});

/* Función para mostrar mensajes tipo toast */
function mostrarToast(mensaje, tipo = 'success') {
    // Remover toast anterior si existe
    const toastExistente = document.querySelector('.toast');
    if (toastExistente) {
        toastExistente.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.innerHTML = `
        <div class="toast-icon">${tipo === 'success' ? '✓' : '✕'}</div>
        <div class="toast-message">${mensaje}</div>
    `;

    document.body.appendChild(toast);
    
    // Mostrar toast
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Ocultar después de 4 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}