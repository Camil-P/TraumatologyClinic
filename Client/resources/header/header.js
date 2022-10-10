const toggleButton = document.getElementById("header-toggle-button");
const navbarLinks = document.getElementById("header-navbar-links");

toggleButton.addEventListener("click", () => {
  navbarLinks.classList.toggle("active");
  navbarLinks.classList.toggle("inactive");
});
