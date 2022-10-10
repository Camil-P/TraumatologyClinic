var previousTimeout;
let slideIndex = 0;

showAutoSlides(true);

function showAutoSlides(autoIncrement) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");

  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  
  if (autoIncrement) { slideIndex++; }
  if (slideIndex > slides.length) {slideIndex = 1} 

  for (i = 0; i < dots.length; i++) {
    dots[i].classList.remove("active");
  }

  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].classList.add("active");
  previousTimeout = setTimeout(() => showAutoSlides(true), 2000); // Change image every 2 seconds
}

// Next/previous controls
function plusSlides(n) {
	if (n > 0){
		slideIndex = slideIndex === 3 ? 1 : (slideIndex + 1)
	}
	if (n < 0){
		slideIndex = slideIndex === 1 ? 3 : (slideIndex - 1)
	}
	clearTimeout(previousTimeout);
	showAutoSlides(false);
}

// Thumbnail image controls
function currentSlide(n) {
	slideIndex = n
	clearTimeout(previousTimeout);
	showAutoSlides(false);
}