document.addEventListener("DOMContentLoaded", function () {
    // Theme Toggle Logic
    const themeToggle = document.getElementById("themeToggle");
    const body = document.body;

    // Load saved theme from localStorage
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        body.classList.add("dark-mode");
    }

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            body.classList.toggle("dark-mode");
            // Save preference
            let theme = body.classList.contains("dark-mode") ? "dark" : "light";
            localStorage.setItem("theme", theme);
        });
    }

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