let slideIndex = 0;

function moveSlide(n) {
    const carouselItems = document.querySelectorAll('.carousel-item'); // Selecciona todos los elementos del carrusel
    const totalSlides = carouselItems.length; // Obtén el número total de imágenes

    // Actualiza el índice de la diapositiva
    slideIndex += n;
    if (slideIndex >= totalSlides) {
        slideIndex = 0;
    } else if (slideIndex < 0) {
        slideIndex = totalSlides - 1;
    }

    // Establece la clase 'active' solo en el elemento actual
    carouselItems.forEach((item, index) => {
        item.classList.toggle('active', index === slideIndex);
    });
}

function autoSlide() {
    moveSlide(1);
}

// Cambia de imagen cada 2 segundos
setInterval(autoSlide, 2000);
