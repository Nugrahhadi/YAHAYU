// Improved dropdown functionality
function toggleDropdown(event) {
  if (event) {
    event.stopPropagation();
  }
  const dropdownMenu = document.getElementById("dropdownMenu");
  dropdownMenu.classList.toggle("active");
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
  const dropdown = document.getElementById("dropdownMenu");
  const trigger = document.querySelector(".nav-item-with-icon");

  if (!trigger.contains(event.target)) {
    dropdown.classList.remove("active");
  }
});

// Smooth scroll functionality
function scrollToDestinations() {
  const destinationsSection = document.getElementById("destinations");
  if (destinationsSection) {
    destinationsSection.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }
}

// Handle navigation menu on scroll
let lastScroll = 0;
const nav = document.querySelector(".nav-header");

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset;

  if (currentScroll > lastScroll && currentScroll > 100) {
    nav.style.transform = "translateY(-100%)";
  } else {
    nav.style.transform = "translateY(0)";
  }

  lastScroll = currentScroll;
});

// Initialize page
document.addEventListener("DOMContentLoaded", function () {
  // Add smooth scroll to all anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });

  // Fix navigation on page load
  window.scrollTo(0, 0);
  nav.style.transform = "translateY(0)";
});

// Handle mobile menu
if (window.innerWidth <= 768) {
  const navItemWithIcon = document.querySelector(".nav-item-with-icon");
  navItemWithIcon.addEventListener("click", function (event) {
    event.stopPropagation();
    const dropdownMenu = this.querySelector(".dropdown-menu");
    dropdownMenu.style.display =
      dropdownMenu.style.display === "flex" ? "none" : "flex";
  });
}

// Toggle Mobile Menu
function toggleMobileMenu() {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.getElementById("navMenu");
  const buttonContainer = document.querySelector(".button-container");

  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
  buttonContainer.classList.toggle("active");
}

// Close mobile menu when clicking outside
document.addEventListener("click", function (e) {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.getElementById("navMenu");
  const buttonContainer = document.querySelector(".button-container");

  if (!e.target.closest(".nav-header")) {
    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
    buttonContainer.classList.remove("active");
  }
});

// Close mobile menu when resizing window
window.addEventListener("resize", function () {
  if (window.innerWidth > 768) {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.getElementById("navMenu");
    const buttonContainer = document.querySelector(".button-container");

    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
    buttonContainer.classList.remove("active");
  }
});

// Password toggle functionality
function togglePasswordVisibility() {
  const passwordField = document.getElementById("password");
  const eyeIcon = document.getElementById("eyeIcon");

  if (passwordField.type === "password") {
    passwordField.type = "text";
    eyeIcon.src = "Images/eye-closed-icon.png";
    eyeIcon.alt = "Hide Password";
  } else {
    passwordField.type = "password";
    eyeIcon.src = "Images/eye-icon.png";
    eyeIcon.alt = "Show Password";
  }
}

// Form validation
document
  .getElementById("loginForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const password = document.getElementById("password").value;
    const errorMessage = document.getElementById("errorMessage");
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!passwordRegex.test(password)) {
      errorMessage.textContent =
        "Password must include uppercase, lowercase, number, and special character";
      errorMessage.style.display = "block";
    } else {
      errorMessage.style.display = "none";
      // Here you would typically handle the login API call
      window.location.href = "index.html"; // Redirect to home page
    }
  });

// Mobile menu functionality
function toggleMobileMenu() {
  const navMenu = document.getElementById("navMenu");
  const buttonContainer = document.querySelector(".button-container");
  const hamburger = document.querySelector(".hamburger");

  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
  buttonContainer.classList.toggle("active");
}

// Password visibility toggle
function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(
    inputId === "password" ? "passwordToggleIcon" : "confirmToggleIcon"
  );

  if (input.type === "password") {
    input.type = "text";
    icon.src = "Images/eye-closed-icon.png";
  } else {
    input.type = "password";
    icon.src = "Images/eye-icon.png";
  }
}

// Form validation
document
  .getElementById("registerForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const passwordError = document.getElementById("passwordError");
    const confirmError = document.getElementById("confirmError");
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    let isValid = true;

    if (!passwordRegex.test(password)) {
      passwordError.textContent =
        "Password must include uppercase, lowercase, number, and special character";
      passwordError.style.display = "block";
      isValid = false;
    } else {
      passwordError.style.display = "none";
    }

    if (password !== confirmPassword) {
      confirmError.textContent = "Passwords do not match";
      confirmError.style.display = "block";
      isValid = false;
    } else {
      confirmError.style.display = "none";
    }

    if (isValid) {
      // Here you would typically handle the registration API call
      window.location.href = "Login.html";
    }
  });

// Mobile menu toggle
// Mobile menu toggle
function toggleMobileMenu() {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.getElementById("navMenu");
  const buttonContainer = document.querySelector(".button-container");

  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
  buttonContainer.classList.toggle("active");

  // Prevent scroll when menu is open
  document.body.style.overflow = hamburger.classList.contains("active")
    ? "hidden"
    : "auto";
}

// Close menu when clicking outside
document.addEventListener("click", function (e) {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.getElementById("navMenu");
  const buttonContainer = document.querySelector(".button-container");

  if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
    buttonContainer.classList.remove("active");
    document.body.style.overflow = "auto";
  }
});

// Close menu when window is resized
window.addEventListener("resize", function () {
  if (window.innerWidth > 768) {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.getElementById("navMenu");
    const buttonContainer = document.querySelector(".button-container");

    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
    buttonContainer.classList.remove("active");
    document.body.style.overflow = "auto";
  }
});
