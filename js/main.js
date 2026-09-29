document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("themeToggle");
    const menuToggle = document.getElementById("menuToggle");
    const primaryNav = document.getElementById("primaryNav");

    try {
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
    } catch {}

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            document.body.classList.toggle("dark-mode");
            try {
                localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
            } catch {}
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
        const paymentLabels = JSON.parse(noticeDialog.dataset.paymentLabels || "{}");
        const translations = JSON.parse(noticeDialog.dataset.translations || "{}");
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener("change", function () {
                const method = paymentMethodSelect.value;
                const details = paymentOptions[method];
                if (!details) return;
                const methodLabel = paymentLabels[method] || method;

                document.getElementById("noticeDialogIcon").textContent = "↗";
                document.getElementById("noticeDialogEyebrow").textContent = translations.payment_destination || "Payment destination";
                document.getElementById("noticeDialogTitle").textContent = (translations.pay_with || "Pay with") + " " + methodLabel;
                document.getElementById("noticeDialogDescription").textContent = translations.transfer_instructions || "Transfer to the account below. Your plan-specific payment code will appear after you save your membership plan.";
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
                    copyReferenceStatus.textContent = translations.payment_copied || "Payment reference copied.";
                } catch (error) {
                    copyReferenceStatus.textContent = translations.copy_unavailable || "Copy unavailable. Select and copy the code above.";
                }
            });
        }
    }

    const siteMessageDialog = document.getElementById("siteMessageDialog");
    if (siteMessageDialog) {
        if (siteMessageDialog.dataset.autoOpen === "true") {
            if (typeof siteMessageDialog.showModal === "function") siteMessageDialog.showModal();
            else siteMessageDialog.setAttribute("open", "");
        }

        siteMessageDialog.querySelectorAll("[data-site-dialog-close]").forEach(function (button) {
            button.addEventListener("click", function () {
                if (typeof siteMessageDialog.close === "function") siteMessageDialog.close();
                else siteMessageDialog.removeAttribute("open");
            });
        });
    }

    const cancelDialog = document.getElementById("cancelDialog");
    if (cancelDialog) {
        let pendingCancelForm = null;

        document.querySelectorAll("form[data-confirm]").forEach(function (form) {
            form.addEventListener("submit", function (event) {
                if (form.dataset.confirmed === "true") {
                    delete form.dataset.confirmed;
                    return;
                }

                event.preventDefault();
                pendingCancelForm = form;
                document.getElementById("cancelDialogDescription").textContent = form.dataset.confirm;
                if (typeof cancelDialog.showModal === "function") cancelDialog.showModal();
                else cancelDialog.setAttribute("open", "");
            });
        });

        const closeCancelDialog = function () {
            if (typeof cancelDialog.close === "function") cancelDialog.close();
            else cancelDialog.removeAttribute("open");
            pendingCancelForm = null;
        };

        cancelDialog.querySelectorAll("[data-cancel-dialog-close]").forEach(function (button) {
            button.addEventListener("click", closeCancelDialog);
        });

        document.getElementById("confirmCancelButton").addEventListener("click", function () {
            const form = pendingCancelForm;
            closeCancelDialog();
            if (form) {
                form.dataset.confirmed = "true";
                form.requestSubmit();
            }
        });
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