document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector(".container");
    const btniniciar = document.getElementById("btn-iniciar");
    const btnregistrar = document.getElementById("btn-register");

    if (btniniciar) {
        btniniciar.addEventListener("click", () => {
            container.classList.remove("active");
        });
    }

    if (btnregistrar) {
        btnregistrar.addEventListener("click", () => {
            container.classList.add("active");
        });
    }

    // Función para manejar el cambio de rol
const rolSelect = document.getElementById('id_rol');

if (rolSelect) {
    const localFields = document.querySelectorAll('.local-fields');
    const direccionDiv = document.querySelector('.input-group.direccion');
    
    // Función para mostrar/ocultar campos del local
    function toggleLocalFields() {
        const selectedRole = rolSelect.value;
        
        if (selectedRole === '3') { // Comerciante
            localFields.forEach(field => {
                field.style.display = 'flex';
                // Hacer los campos requeridos cuando son visibles
                const inputs = field.querySelectorAll('input, select,label');
                inputs.forEach(input => {
                    if (input.name === 'nombre_local' || input.name === 'id_tipo_local'|| input.name === 'sector'|| input.name === 'imagen') {
                        input.required = true;
                    }
                });
            });
            // Dirección ocupa solo 1 columna
            if (direccionDiv) {
                direccionDiv.classList.add('comerciante');
            }
        } else {
            localFields.forEach(field => {
                field.style.display = 'none';
                // Remover el required cuando están ocultos
                const inputs = field.querySelectorAll('input, select,label');
                inputs.forEach(input => {
                    input.required = false;
                    if (!input.closest('.error-local, .error-email')) { 
                        input.value = '';
                    }
                });
            });
            // Dirección vuelve a ocupar 2 columnas
            if (direccionDiv) {
                direccionDiv.classList.remove('comerciante');
            }
        }
    }
    
    // Ejecutar cuando cambie el rol
    rolSelect.addEventListener('change', toggleLocalFields);
    
    // Ejecutar al cargar la página por si ya hay un valor seleccionado
    toggleLocalFields();
} else {
    console.log('Elemento con id="id_rol" no encontrado');
}

/// ................validacion de l telefono ............
    // Validación simple de teléfono
// Validación simple de teléfono
const telefonoInput = document.getElementById('telefono');
const errorTelefono = document.querySelector('.error-telefono');

if (telefonoInput && errorTelefono) {
    const form = telefonoInput.closest('form');
    const telefonoGroup = telefonoInput.closest('.input-group.telefono');

    // Limpiar error si escribe
    telefonoInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        errorTelefono.style.display = 'none';
        telefonoGroup.classList.remove('input-error');
    });

    // Validar al salir del input
    telefonoInput.addEventListener('blur', function(e) {
        const valor = e.target.value;
        if (valor.length > 0 && valor.length !== 10) {
            errorTelefono.textContent = 'El teléfono debe tener exactamente 10 dígitos';
            errorTelefono.style.display = 'block';
            telefonoGroup.classList.add('input-error');
        } else {
            errorTelefono.style.display = 'none';
            telefonoGroup.classList.remove('input-error');
        }
    });

    // Validar antes de enviar
    if (form) {
        form.addEventListener('submit', function(e) {
            const valor = telefonoInput.value;
            if (valor.length !== 10) {
                e.preventDefault();
                errorTelefono.textContent = 'El teléfono debe tener exactamente 10 dígitos';
                errorTelefono.style.display = 'block';
                telefonoGroup.classList.add('input-error');
                telefonoInput.focus();
            }
        });
    }
}

});