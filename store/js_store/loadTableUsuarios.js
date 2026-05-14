let usuarios = [];
// cargar los usuarios al iniciar la pagina
document.addEventListener('DOMContentLoaded', function () {
    cargarUsuarios();
});
//funcion para cargar usuarios desde la api
async function cargarUsuarios() {
    try {
        const response = await fetch('../api_store/getInactivousuarios.php');
        const data = await response.json();

        if (data.success) {
            usuarios = data.data;
            renderizarTabla();
            inicializarBusquedaYPaginacion();
        } else {
            mostrarError('Error al cargar usuarios: ' + data.error);
        }

    } catch (error) {
        mostrarError('Error de conexion: ' + error.message);
    }
}
//funcion para renderizar la tabla de usuarios
function renderizarTabla() {
    const tbody = document.getElementById('tbody-usuarios');

    if (usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align: center;">No hay usuarios para mostrar</td></tr>';
        return;
    }

    tbody.innerHTML = usuarios.map((usuario, index) => {
        const filaClase = !usuario.activo ? 'texto-apagado' : '';
        const nombreClase = !usuario.activo ? 'tachado' : '';
        const checked = usuario.activo ? 'checked' : '';

        return `
            <tr id="fila-${index}" class="${filaClase}" 
                data-usuario-id="${usuario.id}" 
                data-usuario-activo="${usuario.activo}">
                <td><input type="checkbox" class="casilla checkbox-fila" 
                           onchange="alternarSeleccionFila(${index})" 
                           ${usuario.activo ? 'disabled' : ''}></td>
                <td>
                    <strong class="${nombreClase}">${usuario.nombre_completo}</strong>
                    <br><small>${usuario.rol}</small>
                </td>
                <td>${usuario.email}</td>
                <td>${usuario.nombre_local || 'NA'}</td>
                <td>${usuario.categoria_local || 'Sin categoría'}</td>
                <td>${usuario.telefono || 'Sin teléfono'}</td>
                <td>${usuario.direccion || 'Sin dirección'}</td>
                <td>${usuario.creado_en}</td>
                <td>
                    <div class="acciones-container">
                        <button class="btn-accion btn-editar ${usuario.activo ? '' : 'deshabilitado'}" id="btn-editar-${usuario.id}">Edit</button>
                        <button class="btn-accion btn-eliminar" onclick="eliminarUsuario(${usuario.id}, ${index})">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </td>
                <td>
                    <label class="interruptor">
                        <input type="checkbox" 
                               onchange="alternarEstadoUsuario(${index}, ${usuario.id})" 
                               ${checked}>
                        <span class="deslizador"></span>
                    </label>
                </td>
            </tr>       
        
        `;
    }).join('');
    // Asignar listener para abrir modal de edición
  usuarios.forEach(usuario => {
    const btn = document.getElementById(`btn-editar-${usuario.id}`);
    if (btn) {
      btn.addEventListener('click', () => {
        abrirModalEditar(`../form/edt_usuarios.php?id=${usuario.id}`);
      });
    }
  });
    actualizarCheckboxPrincipal();
}

//FUNCION PARA MOSTRAR ERRORES
function mostrarError(mensaje) {
    const tbody = document.getElementById('tbody-usuarios');
    tbody.innerHTML = `<tr><td colspan="10" class="error">${mensaje}</td></tr>`;
}

// Cambiar estado de un usuario (activar/desactivar)
async function alternarEstadoUsuario(indice, idUsuario) {
    const fila = document.getElementById(`fila-${indice}`);
    const interruptor = fila.querySelector('.interruptor input');
    const checkboxFila = fila.querySelector('.checkbox-fila');

    try {
        const response = await fetch('../api_store/actualizarUsuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_usuario: idUsuario,
                activo: interruptor.checked,
                id_moderador: 1, // deberías obtenerlo de sesión
                comentario: interruptor.checked ?
                    'Usuario activado por moderador' :
                    'Usuario desactivado'
            })
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();

        // Mostrar información de debug
        console.log('Respuesta del servidor:', data);

        if (data.success) {
            // ✅ Actualiza la fila en la UI
            usuarios[indice].activo = interruptor.checked;

            if (interruptor.checked) {
                fila.classList.remove('texto-apagado');
                fila.querySelector('strong').classList.remove('tachado');
                checkboxFila.disabled = true;
                checkboxFila.checked = false;
                fila.classList.remove('resaltado');

                // Mostrar mensaje de éxito
                mostrarToast('Usuario activado correctamente');
                // alert('Usuario activado correctamente' +
                //     (data.message ? '. ' + data.message : ''));

                // recarga la página después de activar con esto 
                // setTimeout(() => {
                //     location.reload();
                // }, 100);

            } else {
                fila.classList.add('texto-apagado');
                fila.querySelector('strong').classList.add('tachado');
                checkboxFila.disabled = false;
                mostrarToast('Usuario desactivado correctamente');
                // alert('Usuario desactivado correctamente' +
                //     (data.message ? '. ' + data.message : ''));
            }

            actualizarCheckboxPrincipal();
            // recarga la página después de activar con esto 
            await cargarUsuarios();

        } else {
            // Mostrar error específico del correo si existe
            if (data.mail_error) {
                alert('Usuario actualizado, pero hubo un problema con el correo:\n' + data.mail_error);
                console.error('Error de correo:', data.mail_error);
            } else {
                throw new Error(data.error || "Error desconocido al actualizar usuario");
            }
        }

    } catch (error) {
        // ❌ Revierte el estado del switch en caso de error
        interruptor.checked = !interruptor.checked;
        alert('Error de conexión: ' + error.message);
        console.error('Error de conexión:', error);
    }
}

// Función para actualizar el estado del checkbox principal
function actualizarCheckboxPrincipal() {
    const checkboxPrincipal = document.getElementById('checkbox-principal');
    const checkboxesActivos = document.querySelectorAll('.checkbox-fila:not(:disabled)');
    const checkboxesMarcados = document.querySelectorAll('.checkbox-fila:not(:disabled):checked');

    if (checkboxesActivos.length === 0) {
        checkboxPrincipal.indeterminate = false;
        checkboxPrincipal.checked = false;
    } else if (checkboxesMarcados.length === checkboxesActivos.length) {
        checkboxPrincipal.indeterminate = false;
        checkboxPrincipal.checked = true;
    } else if (checkboxesMarcados.length > 0) {
        checkboxPrincipal.indeterminate = true;
        checkboxPrincipal.checked = false;
    } else {
        checkboxPrincipal.indeterminate = false;
        checkboxPrincipal.checked = false;
    }
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
//funcion para activar usuario por checkbox de la fila
async function activarSeleccionados() {
    // Solo obtener checkboxes marcados que NO estén deshabilitados (usuarios inactivos)
    const checkboxesMarcados = document.querySelectorAll('.checkbox-fila:checked:not(:disabled)');

    if (checkboxesMarcados.length === 0) {
        mostrarToast('Selecciona al menos un usuario inactivo', 'error');
        return;
    }

    if (!confirm(`¿Estás seguro de activar ${checkboxesMarcados.length} usuario(s)?`)) {
        return;
    }

    const usuariosActivar = [];

    // Iterar sobre los checkboxes marcados y obtener los datos correctos
    checkboxesMarcados.forEach((checkbox) => {
        const fila = checkbox.closest('tr');
        const indice = parseInt(fila.id.split('-')[1]);
        const usuarioId = parseInt(fila.dataset.usuarioId);
        const usuarioActivo = fila.dataset.usuarioActivo === 'true';
 console.log('Fila:', fila);
    console.log('ID Usuario:', usuarioId);
    console.log('Activo:', usuarioActivo);
        // Verificar que el usuario existe en el array y está inactivo
        if (usuarios[indice] && !usuarioActivo) {
            usuariosActivar.push({
                indice: indice,
                id: usuarioId,
                usuario: usuarios[indice]
            });
        }
    });

    if (usuariosActivar.length === 0) {
        mostrarToast('No hay usuarios inactivos válidos seleccionados', 'error');
        return;
    }

    console.log('Usuarios a activar:', usuariosActivar); // Para debug

    try {
        let exitosos = 0;
        let errores = 0;
        let errorDetails = [];

        for (const item of usuariosActivar) {
            try {
                console.log(`Activando usuario ID: ${item.id}`); // Para debug

                const response = await fetch('../api_store/actualizarUsuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_usuario: item.id,
                        activo: true,
                        id_moderador: 1,
                        comentario: 'Usuario aprobado en lote por moderador'
                    })
                });

                console.log(`Status HTTP para usuario ${item.id}: ${response.status}`); // Para debug

                if (response.ok) {
                    try {
                        const data = await response.json();
                        console.log(`Respuesta JSON para usuario ${item.id}:`, data); // Para debug

                        if (data.success === true) {
                            exitosos++;
                            console.log(`Usuario ${item.id} activado correctamente`);
                        } else {
                            errores++;
                            const errorMsg = data.error || 'Error desconocido en la API';
                            errorDetails.push(`Usuario ${item.id}: ${errorMsg}`);
                            console.error(`Error en API para usuario ${item.id}:`, data);
                        }
                    } catch (jsonError) {
                        errores++;
                        const responseText = await response.text();
                        errorDetails.push(`Usuario ${item.id}: Error de JSON - ${responseText}`);
                        console.error(`❌ Error parseando JSON para usuario ${item.id}:`, jsonError, responseText);
                    }
                } else {
                    errores++;
                    const responseText = await response.text();
                    errorDetails.push(`Usuario ${item.id}: HTTP ${response.status} - ${responseText}`);
                    console.error(`❌ Error HTTP ${response.status} para usuario ${item.id}:`, responseText);
                }
            } catch (error) {
                errores++;
                errorDetails.push(`Usuario ${item.id}: Error de conexión - ${error.message}`);
                console.error(`❌ Error de conexión para usuario ${item.id}:`, error);
            }
        }

        console.log(`Resumen: ${exitosos} exitosos, ${errores} errores`);
        console.log('Detalles de errores:', errorDetails);

        // Mostrar resultado y recargar página si hubo éxitos
        if (exitosos > 0) {
            mostrarToast(`${exitosos} usuario(s) activado(s) correctamente` +
             (errores > 0 ? ` (${errores} errores)` : ''), 'success');
                console.log(`${exitosos} usuario(s) activado(s) correctamente` +
                (errores > 0 ? ` (${errores} errores)` : ''));
            // Recargar la página automáticamente para reflejar los cambios
            await cargarUsuarios();
            // setTimeout(() => {
            //     location.reload();
            // }, 100);

        } else {
            alert(`No se pudo activar ningún Usuario. Errores: ${errores}\n\nRevisa la consola para más detalles.`);
            console.error('❌ DETALLES DE ERRORES:');
            errorDetails.forEach(detail => console.error(detail));
        }

    } catch (error) {
        alert('Error general al activar Usuario: ' + error.message);
        console.error('❌ Error general:', error);
    }

}   











//CODIGO PARA LA PAGINACIÓN Y BUSQUEDA
// Funciones para búsqueda + paginación JS
let registrosPorPagina = 4;
let paginaActual = 1;
let filasOriginales = [];

function inicializarBusquedaYPaginacion() {
    const inputBusqueda = document.getElementById('busqueda');
    const tabla = document.querySelector('#tabla-usuarios tbody');
    filasOriginales = Array.from(document.querySelector('#tabla-usuarios tbody').rows);

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
// === Funciones Modal  EDITAR===
function abrirModalEditar(url) {
    const modal = document.getElementById("modalEditar-usuarios");
    const iframe = document.getElementById("iframe-editar-usuarios");

    // asignar la URL del formulario de edición
    iframe.src = url;

    // mostrar modal
    modal.style.display = "flex";

    // ocultar tabla
    document.querySelector(".barra-superior").style.display = "none";
    document.getElementById("tabla-usuarios").style.display = "none";    
    document.querySelector(".paginacion").style.display = "none";
    document.getElementById("acciones-btn").style.display = "none";
}
function cerrarModalEditar() {
    const modal = document.getElementById("modalEditar-usuarios");
    modal.style.display = "none";

    // mostrar de nuevo la tabla
    document.getElementById("tabla-usuarios").style.display = "table";
    document.querySelector(".barra-superior").style.display = "flex";
    document.querySelector(".paginacion").style.display = "block";
    document.getElementById("acciones-btn").style.display = "block";
    cargarUsuarios(); // recargar usuarios para reflejar cambios
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
    const modal = document.getElementById("modalEditar-usuarios");
    if(e.target === modal){
        modal.style.display = "none";
        this.document.getElementById("iframe-editar-usuarios").src = ""; // limpiar iframe
        // mostrar de nuevo la tabla
        document.getElementById("tabla-usuarios").style.display = "table";
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
// Escuchar mensajes del iframe para cerrar el modal que viene del editar usuarios
window.addEventListener("message", function(event) {
  if (event.data.action === "cerrarModalEditar") {
    // Cerrar el modal editar
    document.getElementById("modalEditar-usuarios").style.display = "none";
    // muestra denuevo el contendio del iframe padre  tabla usuarios

    document.getElementById("tabla-usuarios").style.display = "table";
        document.querySelector(".barra-superior").style.display = "flex";
        document.querySelector(".paginacion").style.display = "block";
        document.getElementById("acciones-btn").style.display = "block";
    cargarUsuarios(); 
  }
});

//............funvciones para eliminar usuarios
let usuarioEliminar = null;
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
function eliminarUsuario(idUsuario, index) {
    if (!usuarios || !usuarios[index]) {
        mostrarToast('Error: Usuario no encontrado', 'error');
        return;
    }
    const usuario = usuarios[index];
    usuarioEliminar = { ...usuario, index };

    // Actualizar información del usuario en el modal
    document.getElementById('usuario-nombre-display').textContent = usuario.nombre;
    document.getElementById('local-nombre-display').textContent = `Local: ${usuario.nombre_local || 'N/A'}`;

    // Verificar si el usuario está desactivado
    if (usuario.activo === 0 || usuario.activo === '0') {
        // Usuario desactivado - mensaje especial
        document.querySelector('.modal-title').textContent = '¿Confirmar eliminación?';
        document.querySelector('.modal-message').innerHTML = `
            <strong>Usuario desactivado</strong><br>
            <small style="color: #666;">
                Relaciones con otras tablas: ${usuario.relaciones ? usuario.relaciones.join(', ') : 'Ninguna'}
                <br>
            </small>
        `;
    } else {
        // Usuario activo - mensaje normal
        document.querySelector('.modal-message').textContent = 
            '¿Estás seguro de que deseas eliminar este Usuario?';
        document.querySelector('.modal-title').textContent = '¿Confirmar eliminación?';
    }

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
    usuarioEliminar = null;
    
    // Restaurar scroll del body
    document.body.style.overflow = '';
}
/**
 * Confirmar eliminación del usuarios
 */
async function confirmarEliminacion() {
    if (!usuarioEliminar) return;

    const btnConfirm = document.getElementById('btn-confirm-delete');
    btnConfirm.disabled = true;
    btnConfirm.classList.add('btn-loading');
    btnConfirm.textContent = 'Eliminando...';

    try {
        const response = await fetch(`../api_store/delete_usuarios.php?id=${usuarioEliminar.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const text = await response.text();
        console.log('Respuesta del servidor:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta no válida del servidor');
        }

        if (data.success) {
            // Recargar lista de usuarios
            await cargarUsuarios();

            // Cerrar modal
            cerrarModalEliminar();

            // Mensaje dinámico según acción
            let mensaje = '';
            if (data.data.accion === 'eliminado') {
                mensaje = `"${data.data.nombre}" eliminado correctamente del local "${data.data.nombre_local}"`;
            } else if (data.data.accion === 'desactivado') {
                mensaje = `"${data.data.nombre}" fue desactivado porque tiene relaciones (${data.data.relaciones.join(', ')})`;
            } else {
                mensaje = `Acción realizada en "${data.data.nombre}"`;
            }

            mostrarToast(mensaje);

            // Actualizar contadores si existen
            if (typeof actualizarContadores === 'function') {
                actualizarContadores();
            }

        } else {
            throw new Error(data.error || 'Error al eliminar/desactivar el usuario');
        }

    } catch (error) {
        console.error('Error al eliminar usuario:', error);
        mostrarToast(error.message || 'Error al eliminar el usuario', 'error');

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

