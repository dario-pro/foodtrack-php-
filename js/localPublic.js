async function cargarLocalesCategoria(categoria) {
    const loadingElement = document.getElementById('loading');
    const gridElement = document.getElementById('GridLocal');

    try {
        // Mostrar loading
        if (loadingElement) loadingElement.style.display = 'block';
        gridElement.innerHTML = '';

        // Realizar petición
        const response = await fetch(`../api/get_localPublic.php?categoria=${encodeURIComponent(categoria)}`);

        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status}`);
        }

        const data = await response.json();

        // Mantener spinner visible unos segundos (simula carga)
        setTimeout(() => {
            // Ocultar loading
            if (loadingElement) loadingElement.style.display = 'none';

            if (data.success && data.locales && data.locales.length > 0) {
                // Crear cards de locales
                data.locales.forEach(local => {
                    const card = document.createElement('div');
                    card.className = 'local-card';
                    card.onclick = () => verDetalleLocal(local.id, local.nombre_local); // Opcional: agregar navegación

                    card.innerHTML = `
                    <div class="local-image">
                        <img src="/proyectoComida/${local.imagen_url}" 
                             alt="${local.nombre_local}"
                             loading="lazy">
                    </div>    
                    <div class="local-info">
                        <h3>${local.nombre_local}</h3>
                        <strong>Tipo:</strong> ${local.tipo_local || 'No especificado'}
                    </div>
                    <div class="local-info">
                        <strong>Ubicación:</strong> ${local.direccion || 'No especificada'}
                    </div>
                    <div class="local-info">
                        <strong>Sector:</strong> ${local.sector || 'No especificado'}
                    </div>
                    `;
                    gridElement.appendChild(card);
                });

                console.log(`${data.count} locales de ${categoria} cargados correctamente`);

            } else {
                // No hay locales
                gridElement.innerHTML = `
                    <div class="no-locales">
                        <h3>No hay locales disponibles</h3>
                        <p>No se encontraron locales en la categoría "${categoria}".</p>
                        <p>Intenta con otra categoría o vuelve más tarde.</p>
                    </div>
                `;
            }

        }, 1000); // tiempo de espera para simular carga

    } catch (error) {
        console.error('Error al cargar locales:', error);

        // Ocultar loading
        if (loadingElement) loadingElement.style.display = 'none';

        // Mostrar error
        gridElement.innerHTML = `
            <div class="error">
                <h3>Error al cargar los locales</h3>
                <p>Ha ocurrido un error: ${error.message}</p>
                <button onclick="cargarLocalesCategoria('${categoria}')" style="
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                ">Reintentar</button>
            </div>
        `;
    }
}


// Función opcional para navegar al detalle del local
// Función para ver detalle del local (productos del local)


// Función mejorada para ver detalle del local con mejor debugging
async function verDetalleLocal(localId, nombreLocal = '') {
    console.log(`🔍 Iniciando carga de productos para local ${localId} (${nombreLocal})`);

    const localesSection = document.getElementById('GridLocal');

    // Crear sección de productos si no existe
    let productosSection = document.getElementById('productosSection');
    if (!productosSection) {
        console.log('📝 Creando sección de productos...');
        productosSection = document.createElement('div');
        productosSection.id = 'productosSection';
        productosSection.className = 'productos-section';

        productosSection.innerHTML = `
           
            <div id="productsGrid" class="products-grid"></div>
        `;

        localesSection.parentNode.insertBefore(productosSection, localesSection.nextSibling);
    }
    // Actualizar el título principal con el nombre del local
    const categoriaTitulo = document.getElementById('categoriaTitulo');
    if (categoriaTitulo) {
        categoriaTitulo.textContent = nombreLocal;
    }

    const gridProductos = document.getElementById('productsGrid');

    // Cambiar vista
    localesSection.style.display = 'none';
    productosSection.style.display = 'block';

    // Mostrar loading
    gridProductos.innerHTML = `
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p class="loading-text">Cargando productos del local...</p>
        </div>
    `;

    try {
        // Construir URL con parámetros
        const apiUrl = `../api/get_alimentosPublic.php?id_local=${localId}`;
        console.log(`🌐 Realizando petición a: ${apiUrl}`);

        // Realizar petición
        const response = await fetch(apiUrl);

        console.log(`📡 Respuesta recibida - Status: ${response.status} ${response.statusText}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('📊 Datos recibidos:', data);

        // Verificar estructura de respuesta
        if (!data || typeof data !== 'object') {
            throw new Error('Respuesta inválida del servidor');
        }

        if (data.success && data.products && Array.isArray(data.products) && data.products.length > 0) {
            console.log(`✅ ${data.products.length} productos encontrados`);

            gridProductos.innerHTML = '';

            data.products.forEach((product, index) => {
                console.log(`📦 Procesando producto ${index + 1}:`, product.nombre);

                const card = document.createElement('div');
                card.className = 'product-card';

                // Calcular precios
                const precioBase = parseFloat(product.precio || 0);
                const porcentaje = parseInt(product.porcentaje_descuento || 0);
                const tieneDescuento = porcentaje > 0;
                const precioConDescuento = tieneDescuento
                    ? (precioBase * (1 - porcentaje / 100)).toFixed(2)
                    : precioBase.toFixed(2);

                // HTML del precio anterior
                const precioAnteriorHTML = tieneDescuento
                    ? `<span class="price-original">$${precioBase.toFixed(2)}</span>`
                    : '';

                // HTML del badge de descuento
                const badgeHTML = tieneDescuento
                    ? `<div class="discount-badge">${porcentaje}% OFF</div>`
                    : '';

                // Determinar imagen
                const imagenUrl = product.imagen_url
                    ? `/proyectoComida/store/${product.imagen_url}`
                    : '../images/producto-default.jpg';

                // Generar HTML del producto
                card.innerHTML = `
                    <div class="product-image-container">
                        <img src="${imagenUrl}"
                             alt="${product.nombre || 'Producto'}"
                             class="product-image"
                             onerror="this.src='../images/producto-default.jpg'; console.log('Error cargando imagen:', '${imagenUrl}');"
                             onload="console.log('Imagen cargada:', this.src);">
                        ${badgeHTML}
                    </div>
                    <div class="product-info">
                        ${product.descripcion ? `
                            <p class="product-description">
                                ${product.descripcion}
                            </p>
                        ` : ''}
                        <h2 class="product-name">
                            ${product.nombre || 'Producto sin nombre'}
                        </h2>
                        <p class="product-price">
                            ${precioAnteriorHTML}
                            <span class="price-current">$${precioConDescuento}</span>
                        </p>
                        <p class="product-stock ${product.stock > 0 ? 'stock-available' : 'stock-unavailable'}">
                            <strong>Stock:</strong> ${product.stock} unidades disponibles
                        </p>
                        ${product.stock > 0 ? `
                            <button class="btn-add-to-cart open-login-modal" >
                                 Agregar al carrito
                            </button>
                        ` : `
                            <button class="btn-out-of-stock" disabled>
                                Sin stock
                            </button>
                        `}
                        
                    </div>
                `;

                gridProductos.appendChild(card);
            });

            console.log(`🎉 ${data.products.length} productos cargados exitosamente`);

        } else if (data.success && (!data.products || data.products.length === 0)) {
            console.log('⚠️ No se encontraron productos para este local');

            gridProductos.innerHTML = `
                <div class="empty-products">
                    <div class="empty-products-icon">🍽️</div>
                    <h3 class="empty-products-title">Sin productos disponibles</h3>
                    <p class="empty-products-text">
                        Este local no tiene productos disponibles por el momento.<br>
                        ¡Vuelve pronto para ver las nuevas ofertas!
                    </p>
                </div>
            `;
        } else {
            console.error('❌ Error en respuesta del servidor:', data);
            throw new Error(data.message || 'Error desconocido en el servidor');
        }

    } catch (error) {
        console.error('💥 Error al cargar productos:', error);

        gridProductos.innerHTML = `
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h3 class="error-title">Error al cargar los productos</h3>
                <p class="error-details">
                    <strong>Error:</strong> ${error.message}<br>
                    <strong>Local ID:</strong> ${localId}
                </p>
                <button class="btn-retry" onclick="verDetalleLocal(${localId}, '${nombreLocal}')">
                    🔄 Reintentar
                </button>
            </div>
        `;
    }
}


// Ejecutar automáticamente si se pasa la categoría
function initLocales(categoria) {
    if (categoria && categoria.trim() !== '') {
        console.log(`Inicializando carga de locales para categoría: ${categoria}`);
        cargarLocalesCategoria(categoria.toLowerCase().trim());
    } else {
        console.error('No se proporcionó una categoría válida');
        document.getElementById('GridLocal').innerHTML = `
            <div class="error">
                <h3>Error de configuración</h3>
                <p>No se especificó una categoría válida para mostrar los locales.</p>
            </div>
        `;
    }
}

// Función para recargar los locales (útil para botones de actualizar)
function recargarLocales(categoria) {
    cargarLocalesCategoria(categoria);
}

// Función para abrir el modal
// Abrir modal al hacer clic en cualquier botón con clase .open-login-modal
function openLoginModal() {
    const modal = document.getElementById('modalLogin');
    const iframe = document.getElementById('loginFrame');
    if (modal && iframe) {
        iframe.src = '/proyectoComida/store/login.php';
        modal.style.display = 'block';
    }
}

// Cerrar modal
document.querySelectorAll('.custom-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = document.getElementById('modalLogin');
        if (modal) modal.style.display = 'none';
    });
});

window.addEventListener('click', (event) => {
    const modal = document.getElementById('modalLogin');
    if (event.target === modal) modal.style.display = 'none';
});

// Delegación de eventos para botones dinámicos
document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('open-login-modal')) {
        openLoginModal();
    }
});