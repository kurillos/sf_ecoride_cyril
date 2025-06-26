const inputNom = document.getElementById("registration_form_firstName");
const inputPrenom = document.getElementById("registration_form_lastName");
const inputPseudo = document.getElementById("registration_form_pseudo");
const inputEmail = document.getElementById("registration_form_email");
const inputPassword = document.getElementById("registration_form_plainPassword_first");
const inputPasswordConfirm = document.getElementById("registration_form_plainPassword_second");
const inputAgreeTerms = document.getElementById("registration_form_agreeTerms");

// récupération des div de feedback
const feedbackNom = document.getElementById("feedbackName");
const feedbackPrenom = document.getElementById("feedbackLastName");
const feedbackPseudo = document.getElementById("feedbackPseudo");
const feedbackEmail = document.getElementById("feedbackEmail");
const feedbackPassword = document.getElementById("feedbackPassword");
const feedbackPasswordConfirm = document.getElementById("feedbackPasswordConfirm");
const feedbackAgreeTerms = document.getElementById("feedbackCheckBox");

// bouton de validation
const btnValidation = document.getElementById("btn-validation-inscription");

inputNom.addEventListener("keyup", validateForm);
inputPrenom.addEventListener("keyup", validateForm);
inputPseudo.addEventListener("keyup", validateForm);
inputEmail.addEventListener("keyup", validateForm);
inputPassword.addEventListener("keyup", validateForm);
inputPasswordConfirm.addEventListener("keyup", validateForm);
inputAgreeTerms.addEventListener("change", validateForm);

const form = document.querySelector('.needs-validation');
if (form) {
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity() || btnValidation.disabled) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        } else {
            form.classList.remove('was-validated');
        }
    });
}

function validateForm() {
    const nomOK = validateRequired(inputNom, feedbackNom, "Veuillez entrer votre nom.");
    const prenomOK = validateRequired(inputPrenom, feedbackPrenom, "Veuillez entrer votre prénom.");
    const pseudoOK = validateRequired(inputPseudo, feedbackPseudo, "Veuillez entrer votre pseudonyme.");
    const emailOK = validateEmail(inputEmail, feedbackEmail, "Veuillez entrer une adresse e-mail valide.");
    const passwordOK = validatePassword(inputPassword, feedbackPassword);
    const passwordConfirmOK = validatePasswordConfirm(inputPassword, inputPasswordConfirm);
    const agreeTermsOK = validateCheckbox(inputAgreeTerms, feedbackAgreeTerms, "Vous devez accepter les conditions d'utilisation.");

    if (nomOK && prenomOK && pseudoOK && emailOK && passwordOK && passwordConfirmOK && agreeTermsOK) {
        btnValidation.disabled = false;
    } else {
        btnValidation.disabled = true;
    }
}

// affiche / masque le message d'erreur
function displayFeedback(feedbackElement, message, isValid) {
    if (isValid) {
        feedbackElement.textContent ="";
    } else { 
        feedbackElement.textContent = message;
    }
}

// Validation des champs requis
function validateRequired(input, feedbackElement, errorMessage) {
    if (input.value.trim() !== "") {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        displayFeedback(feedbackElement, "", true);
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        displayFeedback(feedbackElement, errorMessage, false);
        return false;
    }
}

// Validation de l'email
function validateEmail(input, feedbackElement, errorMessage) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (input.value.trilm().match(emailRegex)) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        displayFeedback(feedbackElement, "", true);
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        displayFeedback(feedbackElement, errorMessage, false);
        return false;
    }
}

// Validation du mot de passe
function validatePassword(input, feedbackElement) {
    // Regex pour 8+ caractère, 1 maj, 1 chiffre, 1 caractère spécial
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]).{8,}$/;
    const passwordUser = input.value;
    let message = "";

    if (passwordUser.length < 8) {
        message = "Le mot de passe doit contenir au moins 8 caractères.";
    } else if (!/[A-Z]/.test(passwordUser)) {
        message = "Le mot de passe doit contenir au moins une majuscule.";
    } else if (!/\d/.test(passwordUser)) {
        message = "Le mot de passe doit contenir au moins un chiffre.";
    } else if (!/[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/.test(passwordUser)) {
        message = "Le mot de passe doit contenir au moins un caractère spécial.";
    }

    if (passwordUser.match(passwordRegex)) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        displayFeedback(feedbackElement, message || "", true);
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        displayFeedback(feedbackElement, message || "Le mot de passe ne respecte pas les critères de sécurité requis.", false);
        return false;
    }
}

// Validation de la confirmation du mot de passe
function validateConfirmationPassword(inputPwd, inputConfirmPwd, feedbackElement) {
    if (inputPwd.value === inputConfirmPwd.value && inputConfirmPwd.value.length > 0) {
        inputConfirmPwd.classList.add("is-valid");
        inpuConfirmPwd.classList.remove("is-invalid");
        displayFeedback(feedbackPasswordConfirm, "", true);
        return true;  
    } else {
        inputConfirmPwd.classList.add("is-invalid");
        inputConfirmPwd.classList.add("is-valid");
        displayFeedback(feedbackPasswordConfirm, "Les mots de passe ne correspondent pas.", false);
        return false;
}

// validation de la checkbox
function validateCheckbox(input, feedbackElement, errorMessage) {
    if (input.checked) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        displayFeedback(feedbackElement, "", true);
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        displayFeedback(feedbackElement, errorMessage, false);
        return false;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    validateForm();
})};