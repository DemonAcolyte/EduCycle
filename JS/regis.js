//REGISTRATION
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registrationForm"); // Ensure this matches the HTML

  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm-password");

  emailInput.addEventListener("input", validateEmail);
  passwordInput.addEventListener("input", confirmPassword);
  confirmPasswordInput.addEventListener("input", confirmPassword);

  form.addEventListener("submit", function (event) {
    const isEmailValid = validateEmail();
    const isPasswordValid = confirmPassword();

    if (!isEmailValid || !isPasswordValid) {
      event.preventDefault(); // Prevent form submission if validation fails
    }
  });
});

function validateEmail() {
  const emailInput = document.getElementById("email");
  const emailError = document.getElementById("email-error");
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex

  const emailValue = emailInput.value.trim();

  if (emailValue === "") {
    emailError.textContent = "Email is required.";
    emailError.style.display = "block"; // Show error message
    return false;
  }
  if (!emailPattern.test(emailValue)) {
    emailError.textContent = "Please enter a valid email address.";
    emailError.style.display = "block"; // Show error message
    return false;
  } else {
    emailError.style.display = "none"; // Hide error message
    return true;
  }
}

function validateAge() {
  const ageRegex = /^[0-9]+$/;
  const ageInput = document.getElementById("age");
  const ageError = document.getElementById("age-error");
  const ageValue = ageInput.value.trim();

  if (ageValue === "") {
    ageError.style.display = "none"; // Hide error message when empty
    return false;
  }
  if (!ageRegex.test(ageValue)) {
    ageError.textContent = "Age must be a numeric value.";
    ageError.style.display = "block";
    return false; // Invalid input
  } else {
    ageError.style.display = "none";
    return true;
  }
}

function confirmPassword() {
  const truePassword = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm-password");
  const confirmPasswordError = document.getElementById(
    "confirm-password-error"
  );

  const password = truePassword.value.trim();
  const confirm = confirmPassword.value.trim();

  if (password === "" || confirm === "") {
    confirmPasswordError.textContent = "Both password fields are required.";
    confirmPasswordError.style.display = "block"; // Show error message
    return false;
  }

  if (password !== confirm) {
    confirmPasswordError.textContent = "Passwords do not match.";
    confirmPasswordError.style.display = "block"; // Show error message
    return false;
  } else {
    confirmPasswordError.style.display = "none"; // Hide error message
    return true;
  }
}
