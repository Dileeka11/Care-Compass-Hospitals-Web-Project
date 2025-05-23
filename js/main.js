document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    let currentIndex = 0;
  
    function showSlide(index) {
      slides.forEach((slide, i) => {
        slide.style.display = i === index ? 'block' : 'none';
      });
    }
  
    function changeSlide(step) {
      currentIndex = (currentIndex + step + slides.length) % slides.length;
      showSlide(currentIndex);
    }
  
    function autoSlide() {
      changeSlide(1);
      setTimeout(autoSlide, 3000); // Change image every 3 seconds
    }
  
    if (slides.length > 0) {
      showSlide(currentIndex); // Show the first slide
      autoSlide(); // Start the slideshow
    }
  });
  