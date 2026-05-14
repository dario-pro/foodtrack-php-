// Variables globales para almacenar productos y carrito
let todosProductos = []; // Todos los productos de la BD
let filtradoProductos = []; // Productos filtrados por categoría/búsqueda
let cart = []; // Carrito de compras

// Funcion principal para inicializar la tienda cargar categorias y productos cunado la pagina se carga
document.addEventListener('DOMContentLoaded', function () {
    //cargarCategoriasDB();
    cargarProductosDb();

});

// Función para cargar productos desde la base de datos
async function cargarProductosDb() {
    try {
        // Hacer petición al servidor para obtener productos
        const response = await fetch('../api_store/get_alimentos.php'); // Ruta a tu archivo PHP
        
        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error('Error al cargar los alimentos: ' + response.status);
        }
        
        // Convertir respuesta a JSON
        const data = await response.json();
        
        // Verificar si hay productos
        if (data.success && data.products) {
            // Guardar todos los productos en la variable global
            todosProductos = data.products;
            filtradoProductos = [...todosProductos]; // Copia de todos los productos

            // Mostrar los productos en el HTML
            mostrarProductos(filtradoProductos);

            console.log('Alimentos cargados exitosamente:', todosProductos.length);
        } else {
            console.error('Error en la respuesta:', data.message);
            mostrarMensajeDeError('No se pudieron cargar los alimentos');
        }
        
    } catch (error) {
        console.error('Error al cargar productos:', error);
        mostrarMensajeDeError('Error de conexión. Intenta recargar la página.');
    }
}
// Función para mostar los producto en el html 
function mostrarProductos(productosParaMostrar){
    // Encontrar el contenedor que muestra los productos
    const grid= document.getElementById('productsGrid');
    // Limpiar el contenedor antes de mostrar los nuevos productos
    grid.innerHTML = '';
    // Verificar si hay productos para mostrar
    if(productosParaMostrar.length === 0){
        // Si no hay productos, mostrar un mensaje
        grid.innerHTML = '<p>No hay Alimentos disponibles en esta categoría.</p>';
        return;
    }
    //crear una tarjeta para cada producto
    productosParaMostrar.forEach(product => {
        // Crear un elemento de tarjeta del producto
        const productCard = crearTarjetaAlimentos(product);
        // Agregar la tarjeta al contenedor de productos
        grid.appendChild(productCard);
    });
}
// Función para crear una tarjeta de alimento
function crearTarjetaAlimentos(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.setAttribute("data-id", product.id);//se modifico esto

    const precioBase = parseFloat(product.precio);
    const porcentaje = parseInt(product.porcentaje_descuento || 0);
    const tieneDescuento = porcentaje > 0;

    const precioConDescuento = tieneDescuento
        ? (precioBase * (1 - porcentaje / 100)).toFixed(2)
        : precioBase.toFixed(2);

    const precioAnteriorHTML = tieneDescuento
        ? `<span class="precio-anterior">$${precioBase.toFixed(2)}</span>`
        : '';

    const badgeHTML = tieneDescuento
        ? `<div class="badge-descuento">${porcentaje}% OFF</div>`
        : '';

    card.innerHTML = `
        <div class="product-image">
            <img src="/proyectoComida/store/${product.imagen_url || '../images/producto-default.jpg'}" 
                 alt="${product.nombre}" 
                 onerror="this.src='../images/producto-default.jpg'">
            ${badgeHTML}
        </div>
        <div class="product-info">
            <p class="product-description">${product.descripcion}</p>
            <h2 class="product-name">${product.nombre}</h2>
            <p class="product-price">
                ${precioAnteriorHTML}
                <span class="precio-actual">$${precioConDescuento}</span>
            </p>
            <p class="product-stock">Stock: ${product.stock} unidades</p>
            <button class="add-to-cart-btn" 
                    onclick="aniadirAlCarrito(${product.id})" 
                    ${product.stock <= 0 ? 'disabled' : ''}>
                ${product.stock <= 0 ? 'Sin Stock' : 'Agregar al Carrito'}
            </button>
        </div>
    `;

    return card;
}
// ============ FUNCIONES DE FILTRADO=========== =====
// Función para filtrar productos por categoría especificada
// function filtrarProductosPorCategoria(categoryId) {
//     console.log("Categoría seleccionada:", categoryId);
//     console.log("Productos totales:", todosProductos);

//     filtradoProductos = todosProductos.filter(product =>
//         parseInt(product.id_categoria) === parseInt(categoryId) && product.activo
//     );

//     console.log("Productos filtrados:", filtradoProductos);

//     mostrarProductos(filtradoProductos);
//     actualizarBotonFiltrarActivo(event.target);
// }
// Actualizar botón activo en los filtros
// function actualizarBotonFiltrarActivo(activeButton){
//     // Remover clase active de todos los botones
//     document.querySelectorAll('.filter-btn').forEach(btn => {
//         btn.classList.remove('active');
//     });
    
//     // Agregar clase active al botón clickeado
//     activeButton.classList.add('active');

// }
// Filtrar todos los productos (botón "Todos")
// function filtrarProductos(filter) {
//     if (filter === 'all') {
//         // Mostrar todos los productos activos
//         filtradoProductos = todosProductos.filter(product => product.activo);
//     }

//     mostrarProductos(filtradoProductos);
//     actualizarBotonFiltrarActivo(event.target);
// }

// ===== FUNCIÓN PARA AGREGAR AL CARRITO =====
function aniadirAlCarrito(productId) {
    // Buscar el producto en la lista
    const product = todosProductos.find(p => p.id == productId);
    
    if (!product) {
        alert('Producto no encontrado');
        return;
    }
    
    if (product.stock <= 0) {
        alert('Producto sin stock');
        return;
    }
    
    // Verificar si el producto ya está en el carrito
    const existingItem = cart.find(item => item.id == productId);
    
    if (existingItem) {
        // Si ya existe, aumentar cantidad (verificar stock)
        if (existingItem.cantidad < product.stock + existingItem.cantidad) {
            existingItem.cantidad++;
        } else {
            alert('No hay suficiente stock disponible');
            return;
        }
    } else {
        // Si no existe, agregarlo al carrito
        cart.push({
            id: product.id, // Usar product.id en lugar de product.id_producto
            nombre: product.nombre,
            precio: product.precio,
            cantidad: 1,
            imagen_url: product.imagen_url // Usar product.imagen en lugar de product.imagen_url
        });
    }
    product.stock--;//se modifico aqui para actualizar stock de card alimentos

    // ACTUALIZAR LA TARJETA EN PANTALLA
    actualizarTarjetaProducto(product);    
    // Actualizar interfaz del carrito
    actualizarElMostrarCarrito();
    
    console.log('Producto agregado al carrito:', product.nombre);
}
// FUNCION PARA ACTULIZAR CARD DE LA PANTALLA
function actualizarTarjetaProducto(product) {
    // Buscar la tarjeta en el DOM por su data-id
    const card = document.querySelector(`.product-card[data-id='${product.id}']`);
    if (!card) return;

    // Actualizar el stock en el texto
    const stockElement = card.querySelector('.product-stock');
    stockElement.textContent = `Stock: ${product.stock} unidades`;

    // Actualizar el botón
    const boton = card.querySelector('.add-to-cart-btn');
    if (product.stock <= 0) {
        boton.disabled = true;
        boton.textContent = "Sin Stock";
    }else{
        boton.disabled = false;
        boton.textContent = "Agregar al Carrito";
    }
}

// ===== FUNCIONE para mostar  al CARRITO =====
function actualizarElMostrarCarrito() {
    // Calcular cantidad total de productos
    const totalItems = cart.reduce((sum, item) => sum + item.cantidad, 0);

    // Calcular subtotal
    const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.precio) * item.cantidad), 0);

    // Calcular IVA (15%)
    const iva = subtotal * 0.15;

    // Total final con IVA
    const finalTotal = subtotal + iva;

    // Actualizar contador en header
    const cartCountEl = document.getElementById('cartCount');
    const cartTotalEl = document.getElementById('cartTotal');

    if (cartCountEl) {
        cartCountEl.textContent = totalItems;
    }

    if (cartTotalEl) {
        cartTotalEl.textContent = `$${finalTotal.toFixed(2)}`;
    }

    // Actualizar totales en el footer del carrito
    const subtotalEl = document.getElementById('subtotal');
    const finalTotalEl = document.getElementById('finalTotal');
    const ivaEl = document.getElementById('iva'); // si quieres mostrar el IVA por separado

    if (subtotalEl) {
        subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
    }

    if (ivaEl) {
        ivaEl.textContent = `$${iva.toFixed(2)}`;
    }

    if (finalTotalEl) {
        finalTotalEl.textContent = `$${finalTotal.toFixed(2)}`;
    }

    // Actualizar contenido del carrito lateral
    actualizarCarrito();
}

// Función para añadir un producto al carrito
function actualizarCarrito() {
    const cartItems = document.getElementById('cartItems');
    
    if (!cartItems) {
        console.error('Elemento cartItems no encontrado');
        return;
    }
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p>Tu carrito está vacío</p>';
        return;
    }
    
    // Crear HTML para cada item del carrito con controles de cantidad
    cartItems.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img src="/proyectoComida/store/${item.imagen_url || '../images/producto-default.jpg'}" alt="${item.nombre}">
            <div class="item-info">
                <h4>${item.nombre}</h4>
                <p class="item-price">${parseFloat(item.precio).toFixed(2)} c/u</p>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="disminuirCantidad(${item.id})">-</button>
                    <span class="quantity-display">${item.cantidad}</span>
                    <button class="quantity-btn" onclick="aumentarCantidad(${item.id})">+</button>
                </div>
                <p class="item-total"><strong>$ ${(parseFloat(item.precio) * item.cantidad).toFixed(2)}</strong></p>
            </div>
            <button onclick="borrarDelCarrito(${item.id})" class="remove-btn">×</button>
        </div>
    `).join('');
}
// FUNCIÓN TOGGLE CART MEJORADA =====
function abrirCart() {
    // Obtener elementos
    const cartSidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('overlay');
    
    // Verificar que los elementos existen
    if (!cartSidebar) {
        console.error('Elemento cartSidebar no encontrado');
        return;
    }
    
    if (!overlay) {
        console.error('Elemento overlay no encontrado');
        return;
    }
    
    // Verificar si el carrito está actualmente abierto
    const isOpen = cartSidebar.classList.contains('active');
    
    if (isOpen) {
        // Cerrar carrito
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Restaurar scroll
    } else {
        // Abrir carrito
        cartSidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevenir scroll del body
    }
    
    console.log('Toggle cart - Estado:', isOpen ? 'Cerrado' : 'Abierto');
}
// FUNCIÓN PARA BORRAR DEL CARRITO =====
function borrarDelCarrito(productId) {
    const productInCart = cart.find(item => item.id == productId);
    if (productInCart && confirm(`¿Deseas eliminar "${productInCart.nombre}" del carrito?`)) {
        
        // devolver stock
        const product = todosProductos.find(p => p.id == productId);
        if (product) {
            product.stock += productInCart.cantidad;
            actualizarTarjetaProducto(product);
        }

        // quitar del carrito
        cart = cart.filter(item => item.id != productId);
        actualizarElMostrarCarrito();

        console.log(`Producto ${productInCart.nombre} eliminado del carrito`);
    }
}
// ===== FUNCIÓN PARA AUMENTAR CANTIDAD =====
function aumentarCantidad(productId){
    // Buscar el producto en todosProductos para verificar stock
    const product = todosProductos.find(p => p.id == productId);
    const cartItem = cart.find(item => item.id == productId);

    if (!product || !cartItem) {
        alert('Error: Producto no encontrado');
        return;
    }
    // Verificar si hay stock disponible
    if (product.stock <= 0) {
        alert('No hay stock disponible');
        return;
    }
    // Aumentar cantidad en el carrito
    cartItem.cantidad++;
    // Disminuir stock del producto
    product.stock--;
    // Actualizar tarjeta y carrito
    actualizarTarjetaProducto(product);
    actualizarElMostrarCarrito();
    console.log(`Cantidad aumentada: ${product.nombre} - ${cartItem.cantidad}`);
}

// ===== FUNCIÓN PARA DISMINUIR CANTIDAD =====
function disminuirCantidad(productId) {
    const cartItem = cart.find(item => item.id == productId);
    // Verificar si el producto está en el carrito
     if (!cartItem) {
        alert('Error: Producto no encontrado en el carrito');
        return;
    }
    // Verificar si la cantidad es mayor a 1
    if (cartItem.cantidad > 1) {
        // Reducir cantidad
        cartItem.cantidad--;
        // Devolver 1 unidad al stock del producto
        const product = todosProductos.find(p => p.id == productId);
        if (product) {
            product.stock++;
            actualizarTarjetaProducto(product); // actualizar stock en la tarjeta
        }
        actualizarElMostrarCarrito();
        console.log(`Cantidad reducida: ${cartItem.nombre} - ${cartItem.cantidad}`);
    }else { 
        // Si la cantidad es 1, preguntar si desea eliminar el producto
        if (confirm(`¿Deseas eliminar "${cartItem.nombre}" del carrito?`)) {
            borrarDelCarrito(productId);
        }
    }
}

// ===== FUNCIÓN PARA MOSTRAR MENSAJES DE ERROR =====
function mostrarMensajeDeError(message) {
    const grid = document.getElementById('productsGrid');
    grid.innerHTML = `
        <div class="error-message">
            <p>⚠️ ${message}</p>
            <button onclick="loadProductsFromDatabase()">Reintentar</button>
        </div>
    `;
}
// ===== FUNCIÓN DE BÚSQUEDA =====
// BÚSQUEDA CON ENTER =====
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                buscarProductos();
            }
        });
    }
    
});

// FUNCIÓN DE BÚSQUEDA 
function buscarProductos() {
    // Obtener texto de búsqueda
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        // Si no hay búsqueda, mostrar todos los productos
        filtradoProductos = todosProductos.filter(product => product.activo);
    } else {
        // Filtrar productos que contengan el término de búsqueda en el nombre
        filtradoProductos = todosProductos.filter(product => 
            product.activo && 
            product.nombre.toLowerCase().includes(searchTerm)
        );
    }    
    // Mostrar productos filtrados
    mostrarProductos(filtradoProductos);
    //limpiar input después de buscar
    searchInput.value = '';
}

//=========cargar productos por cada local
async function cargarProductosPorLocal(localId) {
    try {
        const response = await fetch(`../api_store/get_alimentos.php?id_local=${localId}`);
        const data = await response.json();

        if (data.success && data.products) {
            todosProductos = data.products;      // Sobrescribe productos globales
            filtradoProductos = [...todosProductos];
            mostrarProductos(filtradoProductos); // Mostrar solo productos de este local
        } else {
            const grid = document.getElementById("productsGrid");
            grid.innerHTML = "<p>No hay productos para este local.</p>";
        }
    } catch (error) {
        console.error("Error cargando productos del local:", error);
        const grid = document.getElementById("productsGrid");
        grid.innerHTML = "<p>Error al cargar productos del local.</p>";
    }
}


// ============FUNCION PARA GUARDAR LA COMPRA EN DB y abrir el modal =========
document.addEventListener('DOMContentLoaded', () => {

    // Función para abrir el modal
    window.abrirModalCheckout = function () {
        if (!cart || cart.length === 0) {
            alert('Tu carrito está vacío');
            return;
        }

        const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.precio) * item.cantidad), 0);
        const iva = subtotal * 0.15;
        const total = subtotal + iva;

        // Mostrar resumen en el modal
        const resumenDiv = document.getElementById('resumenCompra');
        if (resumenDiv) {
            resumenDiv.innerHTML = `
                Productos: ${cart.length} <br>
                Subtotal: $${subtotal.toFixed(2)} <br>
                IVA: $${iva.toFixed(2)} <br>
                Total: $${total.toFixed(2)}
            `;
        }

        // ELIMINAR ESTAS LÍNEAS - Los valores ya vienen prellenados desde PHP
        // No necesitamos sobrescribir los valores aquí porque ya están en el HTML
        
        // Mostrar modal
        const modal = document.getElementById('checkoutModal');
        if (modal) modal.style.display = 'block';
    };

    // Cerrar modal al hacer clic en X
    const closeBtn = document.querySelector('#checkoutModal .close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            const modal = document.getElementById('checkoutModal');
            if (modal) modal.style.display = 'none';
        });
    }

    // Cerrar modal al hacer clic fuera del contenido
    window.addEventListener('click', (e) => {
        if (e.target.id === 'checkoutModal') {
            e.target.style.display = 'none';
        }
    });

    // Confirmar compra
    const confirmarBtn = document.getElementById('confirmarCompraBtn');
    if (confirmarBtn) {
        confirmarBtn.addEventListener('click', async () => {
            const cliente_celular = document.getElementById('clienteCelular').value.trim();
            const cliente_direccion = document.getElementById('clienteDireccion').value.trim();
            const fecha_entrega = document.getElementById('fechaEntrega').value;
            const metodo_pago = document.getElementById('metodoPago').value;

            if (!cliente_celular) return alert('El número de celular es obligatorio');
            if (!cliente_direccion) return alert('La dirección es obligatoria');
            if (!fecha_entrega) return alert('La fecha de entrega es obligatoria');

            const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.precio) * item.cantidad), 0);
            const iva = subtotal * 0.15;
            const total = subtotal + iva;

            // Deshabilitar botón mientras se procesa
            confirmarBtn.disabled = true;
            confirmarBtn.textContent = 'Procesando...';

            try {
                const orderData = {
                    cart,
                    total,
                    fecha_entrega,
                    direccion_entrega: cliente_direccion,
                    cliente_celular
                };

                const response = await fetch('../api_store/save_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();

                if (result.success) {
                    alert(`¡Compra realizada!\nNúmero de orden: ${result.data.id_compra}`);
                    cart = [];
                    actualizarElMostrarCarrito();
                    abrirCart();
                    const modal = document.getElementById('checkoutModal');
                    if (modal) modal.style.display = 'none';
                } else {
                    throw new Error(result.message || 'Error desconocido');
                }
            } catch (error) {
                alert('Error al procesar la compra: ' + error.message);
            } finally {
                confirmarBtn.disabled = false;
                confirmarBtn.textContent = 'Confirmar compra';
            }
        });
    }

});
