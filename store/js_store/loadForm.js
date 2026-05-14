//=========CODIGO PARA HACER EL MODAL 
// función para abrir modal con un src dinámico
function abrirModal(ruta) {
  const modal = document.getElementById('modalLogin');  
  const iframe = document.getElementById('modal-iframe');
  
  iframe.src = ruta; // cambiar el contenido del modal
  modal.style.display = 'flex'; // mostrar el modal
  
}
function abrirModalAlimentos(ruta) {
const modalAlimentos = document.getElementById('modalCargarAlimentos');
const iframeAlimentos = document.getElementById('iframe-cargar-alimentos');
iframeAlimentos.src = ruta; // cambiar el contenido del modal
  modalAlimentos.style.display = 'flex'; // mostrar el modal de alimentos
}

// CAMBIO PRINCIPAL: Usar querySelectorAll en lugar de querySelector
// y agregar event listeners a todos los elementos
document.addEventListener('DOMContentLoaded', function() {
    
    // Alimentos - usar querySelectorAll para todos los elementos con clase active-modal
    const alimentosLinks = document.querySelectorAll('.active-modal');
    alimentosLinks.forEach(function(link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            abrirModalAlimentos('../form/alimentos.php');
        });
    });

    // Admin Alimentos - verificar si existe antes de agregar listener
    const adminAlimentosBtn = document.getElementById('admin-alimentos');
    if (adminAlimentosBtn) {
        adminAlimentosBtn.addEventListener('click', function (e) {
            e.preventDefault();
            abrirModal('../form/listAlimentos.php');
        });
    }

    // Admin Usuarios - verificar si existe antes de agregar listener
    const adminUsuariosBtn = document.getElementById('admin-usuarios');
    if (adminUsuariosBtn) {
        adminUsuariosBtn.addEventListener('click', function (e) {
            e.preventDefault();
            abrirModal('../form/listUsuarios.php');
        });
    }

    // Cerrar modal al hacer clic en la "X"
    const closeBtn = document.querySelector('.custom-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            document.getElementById('modalLogin').style.display = 'none';
            document.getElementById('modal-iframe').src = ""; // limpiar iframe
        });
    }

    const closeAlimentosBtn = document.querySelector('.custom-close-alimentos');
    if (closeAlimentosBtn) {
        closeAlimentosBtn.addEventListener('click', function () {
            document.getElementById('modalCargarAlimentos').style.display = 'none';
            document.getElementById('iframe-cargar-alimentos').src = ""; // limpiar iframe
        });
    }
});

// Cerrar modal al hacer clic en la "X"
document.querySelector('.custom-close').addEventListener('click', function () {
  document.getElementById('modalLogin').style.display = 'none';
  document.getElementById('modal-iframe').src = ""; // limpiar iframe
});
document.querySelector('.custom-close-alimentos').addEventListener('click', function () {
  document.getElementById('modalCargarAlimentos').style.display = 'none';
  document.getElementById('iframe-cargar-alimentos').src = ""; // limpiar iframe
});


// Cerrar modal al hacer clic fuera del contenido
window.addEventListener('click', function (e) {
  const modal = document.getElementById('modalLogin');
  const modalAlimentos = document.getElementById('modalCargarAlimentos');
  if (e.target === modal) {
    modal.style.display = 'none';
    document.getElementById('modal-iframe').src = ""; // limpiar iframe
  }
  if (e.target === modalAlimentos) {
    modalAlimentos.style.display = 'none';
    document.getElementById('iframe-cargar-alimentos').src = ""; // limpiar iframe
  }
});
// cerrar modal con la tecla ESC
window.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        const modal = document.getElementById('modalLogin');
        const modalAlimentos = document.getElementById('modalCargarAlimentos');
        modal.style.display = 'none';
        document.getElementById('modal-iframe').src = ""; // limpiar iframe
        modalAlimentos.style.display = 'none';
        document.getElementById('iframe-cargar-alimentos').src = ""; // limpiar iframe
    }
});




