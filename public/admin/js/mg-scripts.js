document.addEventListener("DOMContentLoaded", function() {
    // Select all carousels on the page
    var carousels = document.querySelectorAll(".mg-gallery");

    carousels.forEach(function(carousel) {
        var slides = carousel.getElementsByClassName("carousel-slide");
        var currentIndex = 0;

        function showSlide(index) {
            for (var i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
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
