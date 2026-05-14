document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    const preview = document.getElementById("preview");
    if (id) {
        fetch(`../api_store/editar_usuarios.php?id=${id}`).then(res => res.json()).then(data => {
            console.log("Respuesta del servidor:", data);//aqui si pasa el id al api 
            if (data.success) {
                const usuario = data.data;
                document.getElementById("id_usuario").value = usuario.id;

                document.getElementById("nombre").value = usuario.nombre;
                document.getElementById("apellido").value = usuario.apellido || '';
                document.getElementById("email").value = usuario.email;
                document.getElementById("direccion").value = usuario.direccion || '';
                document.getElementById("telefono").value = usuario.telefono || '';
                document.getElementById("nombre_local").value = usuario.nombre_local || '';
                document.getElementById("sector").value = usuario.sector || '';
                document.getElementById("tipo-local").value = usuario.id_tipo_local || '';

                document.getElementById("id_rol").value = usuario.id_rol;

                // Vista previa de imagen
                if (usuario.imagen_local) {
                    console.log("Ruta de imagen:", usuario.imagen_local);

                    preview.src = usuario.imagen_local;

                    preview.onerror = () => {
                        console.warn("No se pudo cargar la imagen:", usuario.imagen_local);
                        preview.style.display = "none";
                    };

                    preview.style.display = "block";
                    preview.style.maxWidth = "200px";
                    preview.style.maxHeight = "200px";
                    preview.style.marginBottom = "10px";
                } else {
                    console.warn("No se encontró imagen para este local.");
                    preview.style.display = "none";
                }

                mostrarOcultarCanpos(usuario.id_rol);
            } else {
                alert("Error al cargar el id: " + data.error);
            }

        }).catch(err => console.error("Error cargando usuario:", err));

    }
    // Enviar datos con POST
    const form = document.getElementById("formUsuarios");
    form.addEventListener("submit", e => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("id_usuario", id);

        fetch("../api_store/editar_usuarios.php", {
            method: "POST",
            body: JSON.stringify(Object.fromEntries(formData)),
            headers: { "Content-Type": "application/json" }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Usuario actualizado correctamente");
                    cerrarEditar();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => console.error("Error guardando usuario:", err));
    });

    //funcion  para menejar el rol  y se muestresn los campos del local
    function mostrarOcultarCanpos(selecionarRol) {
        const localFields = document.querySelectorAll('.input-group.hidden');
        if (parseInt(selecionarRol) === 3) { // Comerciante
            localFields.forEach(field => {
                field.style.display = 'flex';
                const inputs = field.querySelectorAll('input, select,label');
                inputs.forEach(input => {
                    if (input.name === 'nombre_local' || input.name === 'tipo-local' || input.name === 'sector' || input.name === 'imagen') {
                        input.required = true;
                    }
                });
            });

        } else {
            localFields.forEach(field => {
                field.style.display = 'none';
                // Remover el required cuando están ocultos
                const inputs = field.querySelectorAll('input, select,label');
                inputs.forEach(input => {
                    input.required = false;
                    // se quito esto para que no borre los datos si cambia de rol manuemamente
                    // if (!input.closest('.error-local, .error-email')) { 
                    //     input.value = '';
                    // }
                });
            });

        }

    }

    //manejar el cambio manual mente del rol
    const rolSelect = document.getElementById("id_rol");

    if (rolSelect) {
        // Cuando el usuario cambie el rol manualmente
        rolSelect.addEventListener("change", () => {
            mostrarOcultarCanpos(rolSelect.value);
        });
    }
// Vista previa cuando se selecciona nueva imagen
    const imagenInput = document.getElementById("imagen");
        imagenInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = "block";
                preview.style.maxWidth = "200px";
                preview.style.maxHeight = "200px";
                preview.style.marginBottom = "10px";
            };
            reader.readAsDataURL(file);
        } else {
            alert("Selecciona un archivo de imagen válido");
            this.value = '';
        }
    });

});

// envio un mensaje para que escuche el otro js
function cerrarEditar() {
    // Enviar el mensaje al padre (la ventana que contiene el iframe)
    window.parent.postMessage({ action: 'cerrarModalEditar' }, '*');
}

