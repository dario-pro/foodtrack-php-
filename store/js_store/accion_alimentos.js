document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    const preview = document.getElementById("preview");

    if (id) {
        fetch(`../api_store/editar_alimentos.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const alimento = data.data;

                    document.getElementById("id_producto").value = alimento.id;
                    document.getElementById("nombre").value = alimento.nombre;
                    document.getElementById("descripcion").value = alimento.descripcion;
                    document.getElementById("precio").value = alimento.precio;
                    document.getElementById("stock").value = alimento.stock;
                    document.getElementById("fecha_caducidad").value = alimento.fecha_caducidad;
                    document.getElementById("porcentaje_descuento").value = alimento.porcentaje_descuento || 0;
                    document.getElementById("estado").value = alimento.estado;

                    // Vista previa de imagen
                    if (alimento.imagen_url) {
                        console.log("Ruta de imagen:", alimento.imagen_url);

                        preview.src = alimento.imagen_url;

                        preview.onerror = () => {
                            console.warn("No se pudo cargar la imagen:", alimento.imagen_url);
                            preview.style.display = "none";
                        };

                        preview.style.display = "block";
                        preview.style.maxWidth = "200px";
                        preview.style.maxHeight = "200px";
                        preview.style.marginBottom = "10px";
                    } else {
                        console.warn("No se encontró imagen para este alimento.");
                        preview.style.display = "none";
                    }
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => console.error("Error cargando alimento:", err));
    }

    // Enviar datos con POST
    const form = document.getElementById("formAlimentos");
    form.addEventListener("submit", e => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("id_producto", id);

        fetch("../api_store/editar_alimentos.php", {
            method: "POST",
            body: JSON.stringify(Object.fromEntries(formData)),
            headers: { "Content-Type": "application/json" }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Producto actualizado correctamente");
                    cerrarEditar();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => console.error("Error guardando alimento:", err));
    });

    // Vista previa cuando se selecciona nueva imagen
    const inputImagen = document.getElementById("imagen");
    inputImagen.addEventListener("change", function () {
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


