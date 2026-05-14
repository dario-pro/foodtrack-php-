<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
  <link rel="stylesheet" href="css/main.css">
     <link rel="stylesheet" href="css/video.css">
  <title>SecondBite- Comida Rescatada</title>
</head>

<body>

  <body>



    <!--  INICIO DEL HEADER -->
    <header id="header" class="custom-header">
      <div class="top-bar">
        <div class="top-bar-container">
          <div class="top-bar-left">
            <a href="https://wa.me/593999986376?text=Hola%2C%20quiero%20ordenar%20comida" class="whatsapp-link"
              title="WhatsApp">
              +593 99 998 6376
            </a>
            <span class="email">pedidos@secondbite.com</span>
            <span class="address">
              Entrega en toda la ciudad de Quito - Ecuador
            </span>
          </div>
          <div class="top-bar-right">
            <div class="social-links-header">
              <a href="#" title="Facebook" class="social-icon"
                target="_blank"><i class="fa-brands fa-facebook"></i></a>
              <a href="#" title="WhatsApp"
                class="social-icon"><i class="fa-brands fa-whatsapp"></i></a>
              <a href="#" title="TikTok" class="social-icon"><i class="fa-brands fa-tiktok"></i></a>
            </div>
          </div>
        </div>
      </div>

      <div class="header-container">
        <a href="index.html" class="logo">
          <img src="public/img/logo.png" alt="Logo" />
          <h1 class="titleLogo">SecondBite</h1>
        </a>
        <div class="search-bar-container">
          <form action="#" method="get" class="search-form">
            <input type="text" name="query" placeholder="Buscar productos..." class="search-input">
            <button type="submit" class="search-button">
              <i class="fa-solid fa-magnifying-glass"></i> </button>
          </form>
        </div>
        <nav class="navmenu">
          <ul>
            <li><a href="index.php" class="active">INICIO</a></li>
            <li><a href="#" onclick="cargarFormulario('comida.php')">Comidas</a></li>
            <li><a href="#" onclick="cargarFormulario('frutas.php')">Frutas</a></li>
            <li><a href="#" onclick="cargarFormulario('panaderia.php')">Panadería</a></li>
            <li><a href="pedidos.html">Contacto</a></li>
            <li><a href="#" id="btnCuenta">MI CUENTA</a></li>
          </ul>
          <span class="mobile-nav-toggle">☰</span>
        </nav>
      </div>
    </header>

    <div id="vista-principal">
      <!--  FIN DEL HEADER -->


    <!-- VIDEO EDUCATIVO Section -->
<p>¡Rescatando comida, salvando el planeta!</p>
<br><br><br><br>

<section class="educational-video-section">
  <div class="educational-container">
    <!-- LADO IZQUIERDO: VIDEO -->
    <div class="video-left">
      <span class="video-tag">🎓 Video educativo</span>
      <h2>Aprende sobre <strong>Rescate de Alimentos</strong></h2>
      <p class="video-description">
        Descubre cómo evitar el desperdicio de comida, salvar alimentos que aún son útiles 
        y contribuir al cuidado del medio ambiente mientras apoyas a tu comunidad.
      </p>

      <div class="video-wrapper">
        <video controls class="video-player">
          <source src="update/video/videoInformativo.mp4" type="video/mp4">
          Tu navegador no soporta la reproducción de video.
        </video>
      </div>
      <p class="video-caption">Haga clic en reproducir para ver el vídeo educativo sobre rescatar comida y reducir desperdicio.</p>
    </div>

    <!-- LADO DERECHO: CONTENIDO -->
    <div class="video-right">
      <h3>¿Qué aprenderás en este vídeo?</h3>
      <ul class="video-benefits">
        <li><i class="fa-solid fa-utensils"></i> <strong>Cómo salvar comida</strong> - Estrategias para evitar que los alimentos se desperdicien.</li>
        <li><i class="fa-solid fa-people-group"></i> <strong>Apoyo a la comunidad</strong> - Cómo el rescate de alimentos beneficia a personas y comercios locales.</li>
        <li><i class="fa-solid fa-earth-americas"></i> <strong>Cuidado del planeta</strong> - Reducir desperdicio ayuda a disminuir emisiones y proteger el medio ambiente.</li>
        <li><i class="fa-solid fa-hand-holding-heart"></i> <strong>Impacto positivo</strong> - Cada acción cuenta para crear un futuro más sostenible.</li>
      </ul>

      <div class="video-stats">
        <h4>Estadísticas del video</h4>
        <div class="stats-grid">
          <div><strong>1.2K</strong><br>Visualizaciones</div>
          <div><strong>1:00</strong><br>Duración</div>
          <div><strong>95%</strong><br>Gustos</div>
          <div><strong>5.0</strong><br>Clasificación</div>
        </div>
      </div>

      <div class="subscribe-box">
        <h4>¿Te gustó el contenido?</h4>
        <p>Suscríbete para recibir más vídeos educativos sobre rescate de alimentos y sostenibilidad</p>
        <a href="#" class="btn btn-success" onclick="document.getElementById('btnCuenta').click();">Iniciar Sesion</a>

       
      </div>
    </div>
  </div>
</section>
<!-- /VIDEO EDUCATIVO Section -->


      <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero-section">
          <div class="hero-container">
            <div class="slider-background">
              <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=1920&h=1080&fit=crop" class="slide activar" alt="Comida deliciosa">
              <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=1920&h=1080&fit=crop" alt="Platos rescatados" class="slide">
              <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920&h=1080&fit=crop" alt="Comida saludable" class="slide">
              <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=1920&h=1080&fit=crop" alt="Entrega a domicilio" class="slide">
            </div>
            <div class="overlay"></div>

            <!-- Contenido principal -->
            <div class="hero-content-wrapper">
              <!-- Sección izquierda del hero -->
              <div class="hero-left">
                <div class="hero-content">
                  <h1>DISFRUTA TU COMIDA FAVORITA A PRECIOS INCREÍBLES</h1>

                  <!-- Mensaje personalizado -->
                  <p class="hero-highlight">
                    Encuentra las mejores ofertas en comida cerca de ti.<br />
                    <strong>Hasta 70% de descuento en restaurantes y locales de calidad.</strong>
                  </p>
                  <p class="hero-lema">
                    <strong>SABOR - VARIEDAD - DESCUENTO</strong>
                  </p>

                  <!-- Botones de llamada a la acción -->
                  <div class="cta-buttons">
                    <a href="#pedidos" class="btn btn-primary" onclick="document.getElementById('btnCuenta').click();">Iniciar Sesion</a>
                    <a href="#menu" class="btn btn-secondary" onclick="cargarFormulario('comida.php')">Ver Ofertas</a>
                  </div>
                </div>
              </div>
              <!-- Fin sección izquierda -->

            </div>
            <!-- Fin del contenido principal -->
          </div>
        </section>
        <!-- /Hero Section -->
        <!-- ABOUT Section -->
        <section class="about-section">
          <div class="about-container">
            <div class="description">
              <h2 class="section-title" data-aos="zoom-in-up"
                data-aos-delay="200"
                data-aos-duration="800"
                data-aos-easing="ease-in-out">¿Cómo funciona SecondBite?</h2>
              <p class="section-description" data-aos="fade-up" data-aos-delay="100" data-aos-duration="700">
                <strong>SecondBite</strong> es una plataforma que conecta a restaurantes, panaderías y tiendas con clientes que buscan
                <strong>ofertas irresistibles en comida de calidad</strong>. Aquí los negocios pueden publicar sus promociones
                y excedentes del día para venderlos a precios accesibles, evitando pérdidas y atrayendo más clientes.
              </p>
              <p class="section-description" data-aos="fade-up" data-aos-delay="300" data-aos-duration="700">
                Para comenzar, cada establecimiento debe <strong>registrarse en la plataforma</strong> creando una cuenta.
                Una vez hecho el registro, deberá iniciar sesión y esperar a que un moderador apruebe su correo electrónico.
                Solo después de esta verificación, el negocio podrá subir sus <strong>ofertas de alimentos</strong>
                y comenzar a llegar a más personas con sus promociones.
              </p>
            </div>
           
          </div>
        </section>

        <section class="rectangulo-flotante">
          <div class="contenedor-rectangulo">
            <h2>ENCUENTRA LAS MEJORES OFERTAS DE COMIDA</h2>
            <p>
              Nuestra plataforma es el marketplace donde restaurantes, panaderías y tiendas publican
              <strong>sus mejores promociones y descuentos</strong>. Descubre platos deliciosos a precios increíbles, todos los días.
            </p>
            <a href="#" class="boton-descarga" onclick="document.getElementById('btnCuenta').click();">Registrarse</a>
          </div>
        </section>

      <section class="stats-section">
  <div class="stats-container">
    <h2 class="title-stats">Impacto de Rescatar Comida</h2>
    <div class="stats">
      <div class="stat-item" data-aos="fade-up" data-aos-delay="100" data-aos-duration="700">
        <i class="fas fa-leaf"></i>
        <h3>+1,200 kg</h3>
        <p>Alimentos rescatados de desperdicio cada mes</p>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-delay="200" data-aos-duration="700">
        <i class="fas fa-people-group"></i>
        <h3>+350</h3>
        <p>Personas y familias beneficiadas</p>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-delay="300" data-aos-duration="700">
        <i class="fas fa-earth-americas"></i>
        <h3>+800 kg CO₂</h3>
        <p>Emisiones de carbono evitadas al rescatar comida</p>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-delay="400" data-aos-duration="700">
        <i class="fas fa-utensils"></i>
        <h3>+500</h3>
        <p>Platos aprovechados de forma creativa</p>
      </div>
    </div>
  </div>
</section>

       <!-- TIPS Section - Inspirado en Too Good To Go -->
<section class="tips-section">
  <div class="tips-container">
    <div class="tip-card" data-aos="fade-up" data-aos-delay="100" data-aos-duration="600">
      <img src="public/img/save-meal.png" alt="Salvar comida">
      <h3>Salva una comida</h3>
      <p>Únete a la misión de Too Good To Go y rescata alimentos antes de que se desperdicien.</p>
    </div>
    <div class="tip-card" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
      <img src="public/img/community.png" alt="Comunidad">
      <h3>Sé parte de la comunidad</h3>
      <p>Millones de personas ya salvan comida. ¡Cada acción suma contra el desperdicio!</p>
    </div>
    <div class="tip-card" data-aos="fade-up" data-aos-delay="300" data-aos-duration="600">
      <img src="public/img/planet.png" alt="Planeta">
      <h3>Ayuda al planeta</h3>
      <p>Reducir el desperdicio de alimentos significa reducir emisiones de CO₂ y cuidar el medioambiente.</p>
    </div>
    <div class="tip-card" data-aos="fade-up" data-aos-delay="400" data-aos-duration="600">
      <img src="public/img/shop.png" alt="Tienda">
      <h3>Apoya a comercios locales</h3>
      <p>Al salvar comida, ayudas a restaurantes, panaderías y supermercados de tu barrio.</p>
    </div>
    <div class="tip-card" data-aos="fade-up" data-aos-delay="500" data-aos-duration="600">
      <img src="public/img/history.png" alt="Historia">
      <h3>Conoce nuestra historia</h3>
      <p>Desde 2015 en Dinamarca, Too Good To Go se ha convertido en un movimiento global.</p>
    </div>
    <div class="tip-card" data-aos="fade-up" data-aos-delay="600" data-aos-duration="600">
      <img src="public/img/app.png" alt="App">
      <h3>Descarga la app</h3>
      <p>Encuentra establecimientos cercanos con excedentes de comida y ayuda a darles una segunda oportunidad.</p>
    </div>
    <div class="tip-card" data-aos="fade-up" data-aos-delay="700" data-aos-duration="600">
      <img src="public/img/impact.png" alt="Impacto">
      <h3>Multiplica tu impacto</h3>
      <p>Cada comida salvada evita desperdicio y promueve un futuro más sostenible.</p>
    </div>
  </div>
</section>

        <!-- /TIPS Section -->

        <!-- ..........Ofertas Section......... -->
        <!-- <section class="daily-offers-section">
          <div class="container">
            <h2 class="section-title" data-aos="fade-up">Ofertas del Día</h2>
            <div class="offers-grid">
              Tarjeta de producto 
              <div class="offer-card" data-aos="zoom-in" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1604908177522-f6e25191a53b" alt="Pan artesanal">
                <div class="offer-info">
                  <h3>Pan artesanal integral</h3>
                  <p>Horneado hoy, perfecto para sandwiches o tostadas.</p>
                  <div class="price">
                    <span class="original-price">$2.00</span>
                    <span class="discounted-price">$1.00</span>
                  </div>
                  <a href="#pedidos" class="btn-order">Pedir ahora</a>
                </div>
              </div>

              <div class="offer-card" data-aos="zoom-in" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1600628422011-1be3a1a97b86" alt="Verduras mixtas">
                <div class="offer-info">
                  <h3>Verduras mixtas</h3>
                  <p>Ideal para sopas o salteados. Frescas y listas para cocinar.</p>
                  <div class="price">
                    <span class="original-price">$4.00</span>
                    <span class="discounted-price">$2.50</span>
                  </div>
                  <a href="#pedidos" class="btn-order">Pedir ahora</a>
                </div>
              </div>

               Puedes duplicar más tarjetas aquí
            </div>
          </div>
        </section> -->


        <!-- /Ofertas Section -->

        <!-- FOOTER -->
        <footer id="footer" class="custom-footer">
          <div class="footer-top">
            <div class="footer-row">
              <div class="footer-col about">
                <a href="index.html" class="footer-logo">
                  <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&h=100&fit=crop&crop=center" alt="Logo" class="logo-footer" />
                  <span class="sitename">SecondBite</span>
                </a>
                <div class="footer-contact">
                  <p>Centro de Quito, Ecuador</p>
                  <p>Entrega en toda la ciudad</p>
                  <p><strong>Teléfono:</strong> + 593 99 998 6376</p>
                  <p><strong>Email:</strong> pedidos@secondbite.com</p>
                  <p><strong>Horario:</strong> Lun-Dom 10:00-22:00</p>
                </div>
                <div class="social-links-footer">
                  <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                  <a href="#" title="Facebook"><i class="fa-brands fa-facebook"></i></a>
                  <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                  <a href="#" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
              </div>

              <div class="footer-col links">
                <h4>Enlaces Rápidos</h4>
                <ul>
                  <li><a href="index.php">Inicio</a></li>
                  <li><a href="#" onclick="cargarFormulario('comida.php')">Ofertar Comida</a></li>
                  <li><a href="#" onclick="cargarFormulario('frutas.php')">Ofertar Fruteria </a></li>
                  <li><a href="#" onclick="cargarFormulario('panaderia.php')">Ofertar Panaderia</a></li>
                  <li><a onclick="document.getElementById('btnCuenta').click();">Registrarse</a></li>
                </ul>
              </div>

              <div class="footer-col links">
                <h4>Compromiso</h4>
                <ul>
                  <li><a href="#">Calidad Garantizada</a></li>
                  <li><a href="#">Ofertas Reales</a></li>
                  <li><a href="#">Seguridad en las Compras</a></li>
                  <li><a href="#">Términos de Servicio</a></li>
                  <li><a href="#">Política de Privacidad</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="footer-bottom">
            <p>
              © <span>Copyright</span><strong class="sitename"> SecondBite 2025</strong> - Rescatando comida, salvando el planeta
            </p>
          </div>
        </footer>
        <!-- FIN FOOTER -->
      </main>
      <!-- Ofertas flotantes -->
      <!-- <div class="offer-banner" id="offerBanner">
      <h4> OFERTA ESPECIAL</h4>
      <p>50% OFF en tu primera orden</p>
      <div class="offer-timer" id="timer">23:45:10</div>
    </div> -->
    </div>
    <!-- MODAL PERSONALIZADO LOGIN -->
    <div id="modalLogin" class="custom-modal">
      <div class="custom-modal-content">
        <span class="custom-close">&times;</span>
        <!-- <div id="loginContainer">Cargando...</div> -->
        <iframe id="loginFrame" src=""></iframe>
        <!-- <iframe src="store/login.php"></iframe> -->
      </div>
    </div>

    <div id="vista-formulario" style="display: none; width: 100%; height: 100vh;">
      <iframe id="formularioFrame" style="width:100%; height:100%; border:none;"></iframe>
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="js/main.js"></script>

  </body>

</html>