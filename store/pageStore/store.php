<?php
session_start();
require_once('../../config/db.php');

// Inicializar variables por defecto
$telefonoUsuario = '';
$direccionUsuario = '';

// Obtener datos del usuario si está logueado
if (isset($_SESSION['id'])) {
    try {
        // Usar la función ejecutarConsulta de tu db.php
        $stmt = ejecutarConsulta("SELECT telefono, direccion FROM usuarios WHERE id = ?", [$_SESSION['id']]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $telefonoUsuario = $usuario['telefono'] ?? '';
            $direccionUsuario = $usuario['direccion'] ?? '';
        }
    } catch (Exception $e) {
        // En caso de error, las variables quedan vacías
        error_log("Error al obtener datos del usuario: " . $e->getMessage());
    }
}
function esModerador()
{
    return isset($_SESSION['id_rol']) && strtolower($_SESSION['id_rol']) === '2'; //moderador
}
function esDonante()
{
    return isset($_SESSION['id_rol']) && strtolower($_SESSION['id_rol']) === '3'; //donante
}

function esReceptor()
{
    return isset($_SESSION['id_rol']) && strtolower($_SESSION['id_rol']) === '4'; //receptor
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- //letras -->
     <link href="https://fonts.googleapis.com/css2?family=Quicksand&family=Josefin+Sans&display=swap" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Oswald:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css_store/store.css">
    <title>Tienda</title>
</head>

<body>
    <!-- CABECERA -->
    <header class="header">
        <div class="header-container">

            <!-- Logo y título -->
            <a href="index.html" class="logo">
                <img src="" alt="Logo" />
                <h1 class="title-logo">SecondBite</h1>
            </a>

            <!-- Sección para EMPLEADO -->
            <?php if (esReceptor()): ?>
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Buscar productos...">
                    <button onclick="buscarProductos()">Buscar</button>
                </div>
                <div class="empleado-header">
                    <!-- Carrito -->
                    <div class="cart-info" onclick="abrirCart()">
                        <span><i class="bi bi-cart4 icon-carrito"></i> Carrito (<span id="cartCount">0</span>)</span>
                        <span id="cartTotal">$0.00</span>
                    </div>

                    <!-- Usuario -->
                    <div class="user-info">
                        <span class="user-name">
                            <i class="bi bi-person-circle icon-register"></i>
                            <?php echo strtoupper(htmlspecialchars($_SESSION['nombre'] ?? 'INVITADO')); ?>
                        </span>
                        <a href="../../index.php" class="login-out">Cerrar Sesión</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Sección para ADMIN -->
            <?php if (esDonante()): ?>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Buscar productos...">
                    <button onclick="buscarProductos()">Buscar</button>
                </div>
                <nav class="admin-nav">
                    <ul class="admin-menu">
                        <li><a href="store.php">Inicio</a></li> <!-- onclick="mostrarInicio()" -->
                        <li class="menu-categorias">
                            <a class="active-modal" href="#" id="alimentos">Alimentos</a>

                        </li>
                        <li class="user-nav-item">
                            <div class="user-info">
                                <span class="user-name">
                                    <i class="bi bi-person-circle icon-register"></i>
                                    <?php echo strtoupper(htmlspecialchars($_SESSION['nombre'] ?? 'INVITADO')); ?>
                                </span>
                                <a href="../../index.php" class="login-out">Cerrar Sesión</a>
                            </div>
                        </li>
                    </ul>
                    <span class="mobile-nav-toggle">☰</span>
                </nav>
            <?php endif; ?>
            <!-- Sección del MODERADOR -->
            <?php if (esModerador()): ?>
                <nav class="admin-nav">
                    <ul class="admin-menu">
                        <li><a href="store.php" onclick="mostrarInicio()">Inicio</a></li>
                        <li class="menu-categorias">
                            <a href="#" id="alimentos" class="active-modal">Alimentos</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="#" id="admin-alimentos">Administrar Alimentos</a></li>

                            </ul>
                        </li>
                        <li class="menu-categorias">
                            <a href="#" id="admin-usuarios">Usuarios</a>
                        </li>
                        <li><a href="#" onclick="cargarListarVentas()">Administrar Ventas</a></li>

                        <li class="user-nav-item">
                            <div class="user-info">
                                <span class="user-name">
                                    <i class="bi bi-person-circle icon-register"></i>
                                    <?php echo strtoupper(htmlspecialchars($_SESSION['nombre'] ?? 'INVITADO')); ?>
                                </span>
                                <a href="../../index.php" class="login-out">Cerrar Sesión</a>
                            </div>
                        </li>
                    </ul>
                    <span class="mobile-nav-toggle">☰</span>
                </nav>
            <?php endif; ?>

        </div>
    </header>
    <div id="vista-principal">
        <!-- filtrar tipo local -->
        <div class="filters-local">
            <div class="container-local">
                <button class="filter-btnLocal active" onclick="volverALocales('all',event)">Todos los Locales</button>

            </div>
        </div>
        <!-- Locales -->

        <!-- Contenedor de locales -->
        <div id="localesSection">
            <div class="local-grid" id="GridLocal"></div>
        </div>

        <!-- Contenedor de productos (inicialmente oculto) -->
        <div id="productosSection" style="display: none;">
            <main class="main-content" id="productsMain">
                <div class="container-tittle">
                    <h2 id="categoriaTitulo" class="titulo-categoria">N/A</h2>
                </div>
                <div class="container">
                    <div class="products-grid" id="productsGrid"></div>
                </div>
            </main>

        </div>




        <!-- Cart Sidebar -->
        <div class="cart-sidebar" id="cartSidebar">
            <div class="cart-header">
                <h3>Mi Carrito</h3>
                <button onclick="abrirCart()">✕</button>
            </div>
            <div class="cart-items" id="cartItems">
                <p>Tu carrito está vacío</p>
            </div>
            <div class="cart-footer">
                <div class="cart-totals">
                    <p>Subtotal: <span id="subtotal">$0.00</span></p>
                    <p>Iva: <span id="iva">$0.00</span></p>
                    <strong>
                        <p>Total a Pagar: <span id="finalTotal">$0.00</span></p>
                    </strong>
                </div>
                <button class="checkout-btn" onclick="abrirModalCheckout()">Comprar Ahora</button>
            </div>
        </div>

        <!-- Overlay -->
        <div class="overlay" id="overlay" onclick="abrirCart()"></div>

    </div>
    <!-- Contenedor para cargar el formulario de Categorías -->
    <!-- <div id="vista-formulario" style="display: none;"></div> -->


    <!-- modal para cargar  formulario alimentos -->
    <div id="modalCargarAlimentos" class="custom-modal-alimentos">
        <div class="custom-modal-content-alimentos">
            <span class="custom-close-alimentos" id="cerrarModal">&times;</span>
            <iframe id="iframe-cargar-alimentos" src="" frameborder="0"></iframe>
        </div>

    </div>

    <!-- MODAL PERSONALIZADO cargar admin-alimentos tabla alimentos-->
    <div id="modalLogin" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-close">&times;</span>
            <iframe id="modal-iframe" src="" frameborder="0"></iframe>

        </div>
    </div>


    <!-- Modal de confirmación de compra -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirmar compra</h2>

            <div id="resumenCompra"></div>

            <div class="checkout-form">
                <div class="input-group celular">
                    <label for="clienteCelular">Celular:</label>
                    <input type="text" id="clienteCelular" class="input-text" value="<?php echo htmlspecialchars($telefonoUsuario); ?>">
                </div>

                <div class="input-group direccion">
                    <label for="clienteDireccion">Dirección:</label>
                    <input type="text" id="clienteDireccion" class="input-text" value="<?php echo htmlspecialchars($direccionUsuario); ?>">
                </div>

                <div class="input-group fecha">
                    <label for="fechaEntrega">Fecha de entrega:</label>
                    <input type="date" id="fechaEntrega" class="input-text fechaEntrega">
                </div>

                <div class="input-group metodo">
                    <label for="metodoPago">Método de pago:</label>
                    <select id="metodoPago" class="input-select metodo-fields">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>

                <div class="input-group btn-group">
                    <button id="confirmarCompraBtn" class="btn-confirm">Confirmar compra</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js_store/local.js"></script>
    <script src="../js_store/store.js"></script>
    <script src="../js_store/loadForm.js"></script>

</body>

</html>