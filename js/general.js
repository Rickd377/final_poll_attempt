document.querySelectorAll('.fa-eye-slash').forEach(function(icon) {
  icon.addEventListener("click", function() {
    const passwordInput = this.previousElementSibling;
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    } else {
      passwordInput.type = "password";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    }
  });
});

document.querySelectorAll('.login-register-form input').forEach(function(input) {
  input.addEventListener('input', function() {
    const form = input.closest('.login-register-form');
    const allValid = Array.from(form.querySelectorAll('input')).every(input => input.checkValidity());
    const submitBtn = form.querySelector('.submit-btn');
    if (allValid) {
      submitBtn.classList.add('valid');
    } else {
      submitBtn.classList.remove('valid');
    }
  });
});