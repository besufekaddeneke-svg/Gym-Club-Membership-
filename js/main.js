document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("themeToggle");
    const menuToggle = document.getElementById("menuToggle");
    const primaryNav = document.getElementById("primaryNav");

    try {
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
    } catch (error) {
        // Keep the current theme if browser storage is unavailable.
    }

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            document.body.classList.toggle("dark-mode");
            try {
                localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
            } catch (error) {
                // Theme still changes for this page view.
            }
        });
    }

    if (menuToggle && primaryNav) {
        menuToggle.addEventListener("click", function () {
            const isOpen = primaryNav.classList.toggle("is-open");
            menuToggle.setAttribute("aria-expanded", String(isOpen));
            menuToggle.setAttribute("aria-label", isOpen ? "Close navigation" : "Open navigation");
        });

        primaryNav.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                primaryNav.classList.remove("is-open");
                menuToggle.setAttribute("aria-expanded", "false");
                menuToggle.setAttribute("aria-label", "Open navigation");
            });
        });
    }

    const noticeDialog = document.getElementById("noticeDialog");
    if (noticeDialog) {
        const openNoticeDialog = function () {
            if (typeof noticeDialog.showModal === "function") {
                if (!noticeDialog.open) noticeDialog.showModal();
            } else {
                noticeDialog.setAttribute("open", "");
            }
        };

        if (noticeDialog.dataset.autoOpen === "true") {
            openNoticeDialog();
        }

        noticeDialog.querySelectorAll("[data-dialog-close]").forEach(function (button) {
            button.addEventListener("click", function () {
                if (typeof noticeDialog.close === "function") noticeDialog.close();
                else noticeDialog.removeAttribute("open");
            });
        });

        noticeDialog.addEventListener("click", function (event) {
            if (event.target === noticeDialog && typeof noticeDialog.close === "function") {
                noticeDialog.close();
            }
        });

        const paymentMethodSelect = document.getElementById("payment_method");
        const paymentOptions = JSON.parse(noticeDialog.dataset.paymentOptions || "{}");
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener("change", function () {
                const method = paymentMethodSelect.value;
                const details = paymentOptions[method];
                if (!details) return;

                document.getElementById("noticeDialogIcon").textContent = "↗";
                document.getElementById("noticeDialogEyebrow").textContent = "Payment destination";
                document.getElementById("noticeDialogTitle").textContent = "Pay with " + method;
                document.getElementById("noticeDialogDescription").textContent = "Transfer to the account below. Your plan-specific payment code will appear after you save your membership plan.";
                document.getElementById("dialogAccountLabel").textContent = details.account_label;
                document.getElementById("dialogAccountNumber").textContent = details.account_number;
                document.getElementById("dialogAccountPanel").hidden = false;
                document.getElementById("dialogReferencePanel").hidden = true;
                document.getElementById("dialogPaymentSummary").hidden = true;
                noticeDialog.dataset.autoOpen = "false";
                openNoticeDialog();
            });
        }

        const copyReferenceButton = document.getElementById("copyReferenceButton");
        const paymentReferenceValue = document.getElementById("paymentReferenceValue");
        const copyReferenceStatus = document.getElementById("copyReferenceStatus");
        if (copyReferenceButton && paymentReferenceValue && copyReferenceStatus) {
            copyReferenceButton.addEventListener("click", async function () {
                try {
                    await navigator.clipboard.writeText(paymentReferenceValue.textContent.trim());
                    copyReferenceStatus.textContent = "Payment reference copied.";
                } catch (error) {
                    copyReferenceStatus.textContent = "Copy unavailable. Select and copy the code above.";
                }
            });
        }
    }

    const regForm = document.getElementById("registerForm");
    if (regForm) {
        const passwordInput = document.getElementById("password");
        const confirmInput = document.getElementById("confirm_password");
        const validatePasswords = function () {
            confirmInput.setCustomValidity(confirmInput.value && confirmInput.value !== passwordInput.value ? "Passwords do not match." : "");
        };

        passwordInput.addEventListener("input", validatePasswords);
        confirmInput.addEventListener("input", validatePasswords);
        regForm.addEventListener("submit", function (event) {
            validatePasswords();
            if (passwordInput.value.length < 8) {
                passwordInput.setCustomValidity("Use at least 8 characters.");
            } else {
                passwordInput.setCustomValidity("");
            }
            if (!regForm.reportValidity()) event.preventDefault();
        });
    }
});