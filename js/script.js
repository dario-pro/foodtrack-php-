document.addEventListener('DOMContentLoaded', () => {
    // --- Lógica del Carrusel ---
    const carouselTrack = document.getElementById('productos-proximos-vencer');
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');
    const productCards = Array.from(carouselTrack.children); // Todas las tarjetas de producto

    let currentIndex = 0;
    let productsPerPage = 3; // Número de productos visibles inicialmente (ajustar en CSS con min-width)

    // Función para actualizar el carrusel
    const updateCarousel = () => {
        // Calcular el desplazamiento basado en el ancho de la tarjeta y el margen
        // Aquí asumimos un ancho de 300px + 2*15px (margen) = 330px por tarjeta
        const cardWidth = productCards[0] ? productCards[0].offsetWidth + (parseFloat(getComputedStyle(productCards[0]).marginLeft) * 2) : 0;
        carouselTrack.style.transform = `translateX(-${currentIndex * cardWidth}px)`;

        // Controlar la visibilidad de los botones de navegación
        prevButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex >= productCards.length - productsPerPage;

        // Ajustar productsPerPage para responsive (ejemplo, más avanzado sería calcular dinámicamente)
        if (window.innerWidth <= 768) {
            productsPerPage = 2; // Mostrar menos productos en tabletas
        }
        if (window.innerWidth <= 480) {
            productsPerPage = 1; // Mostrar un solo producto en móviles
        }
        // Recalcular el índice máximo para el botón 'next'
        nextButton.disabled = currentIndex >= (productCards.length - productsPerPage);
    };

    // Navegación del carrusel
    prevButton.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    nextButton.addEventListener('click', () => {
        // Asegurarse de no ir más allá del último producto visible
        if (currentIndex < productCards.length - productsPerPage) {
            currentIndex++;
            updateCarousel();
        }
    });

    // Ajustar el carrusel al redimensionar la ventana
    window.addEventListener('resize', () => {
        // Reiniciar el índice para evitar espacios vacíos al redimensionar
        currentIndex = 0;
        updateCarousel();
    });

    // Inicializar el carrusel
    updateCarousel();

    // --- Lógica de Filtro por Días de Vencimiento ---
    const filterSelect = document.getElementById('filter-days');

    filterSelect.addEventListener('change', (event) => {
        const filterValue = event.target.value;
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalizar a inicio del día

        productCards.forEach(card => {
            const expirationDateStr = card.dataset.expirationDate;
            const expirationDate = new Date(expirationDateStr);
            expirationDate.setHours(0, 0, 0, 0); // Normalizar a inicio del día

            const diffTime = expirationDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Redondear hacia arriba

            let isVisible = false;

            if (filterValue === 'all') {
                isVisible = true;
            } else if (filterValue === '1-3') {
                isVisible = diffDays >= 0 && diffDays <= 3;
            } else if (filterValue === '7') {
                isVisible = diffDays >= 0 && diffDays <= 7;
            } else if (filterValue === '15') {
                isVisible = diffDays >= 0 && diffDays <= 15;
            }

            card.style.display = isVisible ? 'flex' : 'none'; // Mostrar/ocultar tarjeta

            // Si hay un filtro, reiniciamos el carrusel para que empiece desde el primer elemento visible
            if (filterValue !== 'all') {
                currentIndex = 0; // Reiniciar el índice del carrusel
                // Es necesario recalcular productCards si algunos se ocultaron
                const visibleCards = Array.from(carouselTrack.children).filter(card => card.style.display !== 'none');
                carouselTrack.style.transform = `translateX(0px)`; // Reiniciar la posición visual
                // Ajustar los botones de navegación basados en los elementos visibles
                if (visibleCards.length <= productsPerPage) {
                    prevButton.disabled = true;
                    nextButton.disabled = true;
                } else {
                    prevButton.disabled = true; // El primero siempre estará deshabilitado al inicio
                    nextButton.disabled = false;
                }

            } else {
                 // Si se selecciona 'Todos', re-inicializamos el carrusel con todos los elementos
                 currentIndex = 0;
                 updateCarousel(); // Vuelve a calcular y mostrar todo
            }
        });
        // Después de aplicar el filtro y cambiar los displays, es buena idea actualizar el carrusel
        updateCarousel(); // Esto recalcula los botones y la posición
    });
});