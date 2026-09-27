document.addEventListener("DOMContentLoaded", function () {
    // Client-side form validation for registration
    const regForm = document.getElementById("registerForm");
    if (regForm) {
        regForm.addEventListener("submit", function (e) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password.length < 6) {
                alert("Password must be at least 6 characters long.");
                e.preventDefault();
            } else if (password !== confirmPassword) {
                alert("Passwords do not match!");
                e.preventDefault();
            }
        });
    }
});