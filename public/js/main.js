document.addEventListener("DOMContentLoaded", function () {
    // Confirmação antes de apagar
    const deleteLinks = document.querySelectorAll("[data-confirm-delete]");

    deleteLinks.forEach(function (link) {
        link.addEventListener("click", function (event) {
            const message = link.getAttribute("data-confirm-delete") || "Tens a certeza que queres apagar este registo?";

            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Validação visual simples dos formulários
    const forms = document.querySelectorAll("form");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const requiredFields = form.querySelectorAll("[required]");
            let valid = true;

            requiredFields.forEach(function (field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add("is-invalid");
                } else {
                    field.classList.remove("is-invalid");
                    field.classList.add("is-valid");
                }
            });

            if (!valid) {
                event.preventDefault();
                alert("Preenche todos os campos obrigatórios antes de continuar.");
            }
        });
    });

    // Mostrar/esconder password
    const toggleButtons = document.querySelectorAll("[data-toggle-password]");

    toggleButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            const targetId = button.getAttribute("data-toggle-password");
            const input = document.getElementById(targetId);

            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                button.textContent = "Esconder";
            } else {
                input.type = "password";
                button.textContent = "Mostrar";
            }
        });
    });

    // Atualiza o contador dos eventos futuros.
    const countdowns = document.querySelectorAll("[data-countdown-target]");

    function formatCountdown(milliseconds) {
        if (milliseconds <= 0) {
            return "O evento já começou";
        }

        const totalSeconds = Math.floor(milliseconds / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        if (days > 0) {
            return `Faltam <strong>${days}d ${hours}h ${minutes}m</strong>`;
        }

        return `Faltam <strong>${hours}h ${minutes}m ${seconds}s</strong>`;
    }

    function updateCountdowns() {
        const now = Date.now();

        countdowns.forEach(function (countdown) {
            const target = Number(countdown.getAttribute("data-countdown-target"));

            if (Number.isNaN(target)) {
                countdown.textContent = "";
                return;
            }

            countdown.innerHTML = formatCountdown(target - now);
        });
    }

    if (countdowns.length > 0) {
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    }
});
