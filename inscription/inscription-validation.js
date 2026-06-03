document.querySelectorAll("[data-validate-form]").forEach((form) => {
    const alertBox = form.querySelector("[data-form-alert]");
    const requiredFields = Array.from(form.querySelectorAll("[required]"));

    const clearError = (field) => {
        field.classList.remove("is-invalid");
    };

    const showError = (field) => {
        field.classList.add("is-invalid");
    };

    const fieldIsFilled = (field) => {
        if (field.type === "radio") {
            return form.querySelectorAll(`input[type="radio"][name="${field.name}"]:checked`).length > 0;
        }

        if (field.type === "email") {
            return field.value.trim() !== "" && field.checkValidity();
        }

        return field.value.trim() !== "";
    };

    const showAlert = (message) => {
        if (!alertBox) {
            return;
        }

        alertBox.textContent = message;
        alertBox.hidden = false;
    };

    const hideAlert = () => {
        if (!alertBox) {
            return;
        }

        alertBox.hidden = true;
        alertBox.textContent = "";
    };

    form.addEventListener("submit", (event) => {
        const invalidFields = [];
        const checkedRadioGroups = new Set();

        requiredFields.forEach((field) => {
            if (field.type === "radio") {
                if (checkedRadioGroups.has(field.name)) {
                    return;
                }

                checkedRadioGroups.add(field.name);
                const group = Array.from(form.querySelectorAll(`input[type="radio"][name="${field.name}"]`));
                const isValid = group.some((radio) => radio.checked);

                group.forEach((radio) => {
                    if (isValid) {
                        clearError(radio);
                    } else {
                        showError(radio);
                    }
                });

                if (!isValid) {
                    invalidFields.push(field);
                }

                return;
            }

            if (fieldIsFilled(field)) {
                clearError(field);
            } else {
                showError(field);
                invalidFields.push(field);
            }
        });

        if (invalidFields.length === 0) {
            hideAlert();
            return;
        }

        event.preventDefault();
        showAlert(form.dataset.errorMessage || "Veuillez remplir tous les champs obligatoires avant de continuer.");

        const firstVisibleInvalid = invalidFields.find((field) => field.type !== "hidden");
        if (firstVisibleInvalid) {
            firstVisibleInvalid.focus({ preventScroll: true });
        }

        if (alertBox) {
            alertBox.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    });

    requiredFields.forEach((field) => {
        const eventName = field.type === "radio" ? "change" : "input";
        field.addEventListener(eventName, () => {
            if (field.type === "radio") {
                form.querySelectorAll(`input[type="radio"][name="${field.name}"]`).forEach(clearError);
                return;
            }

            if (fieldIsFilled(field)) {
                clearError(field);
            }
        });
    });
});
