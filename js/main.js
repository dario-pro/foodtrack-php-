//EFECTO DE CAMBIO DE IMAGENES DEN EL HERO
const slides = document.querySelectorAll('.slider-background .slide');
  let currentIndex = 0;

  setInterval(() => {
    slides[currentIndex].classList.remove('activar');
    currentIndex = (currentIndex + 1) % slides.length;
    slides[currentIndex].classList.add('activar');
  }, 4000);



// ANIMACION CUANDO SE HACE SCROLL
AOS.init({
    duration: 1000,   // duración de la animación en milisegundos
    once: false ,      // animar solo una vez al hacer scroll
    easing: 'ease-in-out',
});

//CODIGO PARA HACER EL MODAL 
// Abrir modal al hacer clic en "MI CUENTA"
document.getElementById('btnCuenta').addEventListener('click', function (e) {
  e.preventDefault();

  const modal = document.getElementById('modalLogin');
  const iframe = document.getElementById('loginFrame');

  // Cargar login.php en el iframe solo cuando se abre el modal
  iframe.src = 'store/login.php';

  // Mostrar modal
  modal.style.display = 'block';
});

// Cerrar modal con la "X"
document.querySelector('.custom-close').addEventListener('click', function () {
  document.getElementById('modalLogin').style.display = 'none';
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function (e) {
  const modal = document.getElementById('modalLogin');
  if (e.target === modal) {
    modal.style.display = 'none';
  }
});
//ceerrar con la techa ESC
window.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        const modal = document.getElementById('modalLogin');
        
        modal.style.display = 'none';        
        modalAlimentos.style.display = 'none';
        document.getElementById('loginFrame').src = ""; // limpiar iframe
    }
});
/////////////////////////////
// CARGAR FORMULARIO DE LOCALES
function cargarFormulario(pagina) {
    // Oculta TODO el contenido del index
    document.getElementById('vista-principal').style.display = 'none';

    // Muestra solo el contenedor del iframe
    const vistaFormulario = document.getElementById('vista-formulario');
    vistaFormulario.style.display = 'block';

    // Carga la página seleccionada en el iframe
    const iframe = document.getElementById('formularioFrame');
    iframe.src = 'page/' + pagina; // Ajusta la ruta si tus PHP están en otra carpeta
}


