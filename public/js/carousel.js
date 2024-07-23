// Carousel for Single Gallery
document.addEventListener("DOMContentLoaded", function() {
    var singleCarousels = document.querySelectorAll(".mg-gallery-single-carousel");

    singleCarousels.forEach(function(carousel) {
        var slides = carousel.querySelectorAll(".carousel-slide");
        var currentIndex = 0;

        function showSlide(index) {
            slides.forEach(function(slide) {
                slide.style.display = "none";
            });
            slides[index].style.display = "block";
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        showSlide(currentIndex);
        setInterval(nextSlide, 3000); // Change slide every 3 seconds
    });
});

// Carousel for Multi Gallery
document.addEventListener("DOMContentLoaded", function() {
    var multiCarousels = document.querySelectorAll(".mg-gallery.multi-carousel");

    multiCarousels.forEach(function(carousel) {
        var slides = carousel.querySelectorAll(".mg-multi-carousel-slide");
        var currentIndex = 0;

        function showSlide(index) {
            slides.forEach(function(slide) {
                slide.style.display = "none"; // Hide all slides
            });
            slides[index].style.display = "flex"; // Show the current slide
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        showSlide(currentIndex);
        setInterval(nextSlide, 3000); // Change slide every 3 seconds
    });
});
