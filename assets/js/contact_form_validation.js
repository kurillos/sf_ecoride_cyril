document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form[name="contact_form"]');
    if (!form) return;

    const firstName = document.getElementById("contact_form_firstName");
    const lastName = document.getElementById("contact_form_lastName");
    const email = document.getElementById("contact_form_email");
    const subject = document.getElementById("contact_form_subject");
    const message = document.getElementById("contact_form_message");
    const phoneNumber = document.getElementById("contact_form_phoneNumber");
    const btnValidation = form.querySelector('button[type="submit"]');

    const fields = [firstName, lastName, email, subject, message, phoneNumber];

    fields.forEach(field => {
        if(field) {
            field.addEventListener("keyup", validateForm);
            field.addEventListener("blur", validateForm);
        }
    });

    function validateField(input, validationFn, errorMessage) {
        if (!input) return true; // Field does not exist, so it's valid

        const feedbackElement = input.nextElementSibling;
        if (validationFn(input.value)) {
            input.classList.add("is-valid");
            input.classList.remove("is-invalid");
            if (feedbackElement && feedbackElement.classList.contains('invalid-feedback')) {
                feedbackElement.textContent = "";
            }
            return true;
        } else {
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            if (feedbackElement && feedbackElement.classList.contains('invalid-feedback')) {
                feedbackElement.textContent = errorMessage;
            }
            return false;
        }
    }

    function validateForm() {
        const firstNameOK = validateField(firstName, val => val.trim().length >= 3, "Le prénom doit contenir au moins 3 caractères.");
        const lastNameOK = validateField(lastName, val => val.trim().length >= 3, "Le nom doit contenir au moins 3 caractères.");
        const emailOK = validateField(email, val => /^\S+@\S+\.\S+$/.test(val), "Veuillez entrer une adresse e-mail valide.");
        const subjectOK = validateField(subject, val => val.trim().length >= 5, "L'objet doit contenir au moins 5 caractères.");
        const messageOK = validateField(message, val => val.trim().length >= 10, "Le message doit contenir au moins 10 caractères.");
        const phoneOK = validateField(phoneNumber, val => val.length === 0 || /^0[1-9]([ .-]?[0-9]{2}){4}$/.test(val), "Le format du numéro de téléphone est invalide.");

        if (firstNameOK && lastNameOK && emailOK && subjectOK && messageOK && phoneOK) {
            btnValidation.disabled = false;
        } else {
            btnValidation.disabled = true;
        }
    }

    // Initial validation check
    validateForm();
});