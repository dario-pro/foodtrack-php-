let todosLocales = []; // Todos los locales de la BD
let filtradoLocales = [];

document.addEventListener("DOMContentLoaded", function () {
    cargarTipoLocalDB();
    cargarLocalesDB();
});

async function cargarTipoLocalDB() {
    try {
        // hacer la peticion al api para obtener las categorias
        const response = await fetch("../api_store/get_tipoLocal.php");
        const data = await response.json(); // convertir la respuesta a json
        if (data.success && data.tipoLocal) {
            //cuando la verificacion es correcta creamos botones de tipo local
            crearBtnFiltro(data.tipoLocal);
        }
    } catch (error) {
        console.error("Error al cargar los tipos de local:", error);
    }
}

function crearBtnFiltro(tipoLocal) {
    //encontramos el contenedor de los botones
    const filtroContainer = document.querySelector(
        ".filters-local .container-local"
    );

    const botonesExistentes = filtroContainer.querySelectorAll(
        ".filter-button:not(.active)"
    );
    // Limpiar contenido previo menos el boton TODOS
    botonesExistentes.forEach((boton) => boton.remove());
    //creo cada boton de tipo locales

    tipoLocal.forEach((tipo) => {
        if (tipo) {
            //aqui no va .activo porq no tengo ese atributo en mi base
            const boton = document.createElement("button"); //creo un nuevo boton
            boton.className = "filter-btnLocal"; //creo una clase
            boton.textContent = tipo.nombre; //aqui ponemos el nombre que esta en la db
            boton.onclick = () => filtrarLocalesPorTipo(tipo.id);
            filtroContainer.appendChild(boton); //agrego el boton al contenedor
        }
    });
}

//funcion para cargar los locales desde la api
async function cargarLocalesDB() {
    try {
        // Hacer petición al servidor para obtener locales
        const response = await fetch("../api_store/get_local.php"); // Ruta a tu archivo PHP

        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error("Error al cargar los locales: " + response.status);
        }

        // Convertir respuesta a JSON
        const data = await response.json();

        // Verificar si hay locales en la respuesta
        if (data.success && data.locales) {
            // Guardar todos los locales en la variable global
            todosLocales = data.locales;
            filtradoLocales = [...todosLocales]; // Copia de todos los locales

            // Mostrar los locales en el HTML
            mostrarLocales(filtradoLocales);

            console.log("Locales cargados exitosamente:", todosLocales.length);
        } else {
            console.error("Error en la respuesta:", data.message);
            mostrarMensajeDeError("No se pudieron cargar los locales");
        }
    } catch (error) {
        console.error("Error al cargar locales:", error);
        mostrarMensajeDeError("Error de conexión. Intenta recargar la página.");
    }
}

//funcion para mostrar los locales en el html
function mostrarLocales(localesParaMostrar) {
    // Encontrar el contenedor que muestra los locales
    const grid = document.getElementById("GridLocal");
    // Limpiar el contenedor antes de mostrar los nuevos locales
    grid.innerHTML = "";
    // Verificar si hay locales para mostrar
    if (localesParaMostrar.length === 0) {
        // Si no hay locales, mostrar un mensaje
        grid.innerHTML = "<p>No hay Locales disponibles en esta categoría.</p>";
        return;
    }
    //crear una tarjeta para cada local
    localesParaMostrar.forEach((local) => {
        // Crear un elemento de tarjeta del local
        const localCard = crearTarjetaLocal(local);
        // Agregar la tarjeta al contenedor de locales
        grid.appendChild(localCard);
    });
}

//funcion para crear la tarjeta de cada local - CORREGIDA
function crearTarjetaLocal(local) {
    const card = document.createElement("div");
    card.className = "local-card";
    card.setAttribute("data-id", local.id);

    // Crear tarjeta adaptada a los datos que devuelve tu PHP
    card.innerHTML = `
        <div class="local-image">
            <img 
                src="/proyectoComida/${local.imagen_url}" 
                onerror="this.onerror=null; this.src='/proyectoComida/local-default.jpg'" 
                alt="${local.nombre_local}">
        </div>
        <div class="local-info">
            <h3 class="local-name">${local.nombre_local}</h3>
            <p class="local-sector"><strong>Sector:</strong> ${local.sector}</p>
            <p class="local-direccion"><strong>Ubicación:</strong> ${local.direccion}</p>
            <button class="view-local-btn" 
                    onclick="verLocal(${local.id})">
                Ver Local
            </button>
        </div>
    `;
    // Hacer clic en toda la tarjeta
    card.addEventListener("click", () => {
        verLocal(local.id);
    });

    // Evitar doble ejecución al hacer clic en el botón
    const button = card.querySelector(".view-local-btn");
    button.addEventListener("click", (event) => {
        event.stopPropagation(); // Evita que el evento burbujee al contenedor
        verLocal(local.id);
    });

    return card;
}

function filtrarLocalesPorTipo(tipoId) {
    console.log("Local selecionado:", tipoId);
    console.log("Todos los locales:", todosLocales);
    filtradoLocales = todosLocales.filter(local => local.id_tipo_local == tipoId);
    console.log("Locales filtrados:", filtradoLocales);
    mostrarLocales(filtradoLocales);
    volverALocales(tipoId);

}

// Función para ver un local específico (puedes implementar según tus necesidades)
function verLocal(localId) {
    console.log("Ver local ID:", localId);
    // Buscar el local según el ID para cambiar el titulo del local segun a donde ingrese 
    const localSeleccionado = todosLocales.find(local => local.id == localId);
    console.log("Local seleccionado:", localSeleccionado);
    if (localSeleccionado) {
        // ✅ Cambiar el título al nombre del local
        document.getElementById("categoriaTitulo").innerHTML = `<strong class="tipoLocal-title">${localSeleccionado.nombre_local}</strong>  : Seccion -${localSeleccionado.tipo_nombre}`;
    }

    // Ocultar sección de locales
    document.getElementById("localesSection").style.display = "none";

    // Mostrar sección de productos
    const productosSection = document.getElementById("productosSection");
    if (productosSection) productosSection.style.display = "block";

    // Mostrar mensaje de carga temporalmente
    const grid = document.getElementById("productsGrid");
    if (grid) grid.innerHTML = "<p>Cargando productos...</p>";

    // Cargar productos de este local
    if (typeof cargarProductosPorLocal === 'function') {
        cargarProductosPorLocal(localId);
    }
}

// Función para volver a mostrar locales
function volverALocales(filter) {
    if(filter === 'all'){
        filtradoLocales = todosLocales;
        mostrarLocales(filtradoLocales);
        actualizarBtnFiltrarLocal(event.target);
    }
    // Mostrar sección de locales
     document.getElementById("localesSection").style.display = "block";
    // Ocultar sección de productos
     document.getElementById("productosSection").style.display = "none";
}
function actualizarBtnFiltrarLocal(activeButton) {
    document.querySelectorAll('.filter-btnLocal').forEach(btn => {
        btn.classList.remove('active');
    });
    activeButton.classList.add('active');
}

// ===== FUNCIÓN PARA MOSTRAR MENSAJES DE ERROR =====
function mostrarMensajeDeError(message) {
    const grid = document.getElementById("GridLocal");
    grid.innerHTML = `
        <div class="error-message">
            <p>⚠️ ${message}</p>
            <button onclick="cargarLocalesDB()">Reintentar</button>
        </div>
    `;
}
