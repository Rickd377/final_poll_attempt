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