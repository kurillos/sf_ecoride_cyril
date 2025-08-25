const inputNom = document.getElementById("registration_form_firstName");
const inputPrenom = document.getElementById("registration_form_lastName");
const inputPseudo = document.getElementById("registration_form_pseudo");
const inputEmail = document.getElementById("registration_form_email");
const inputPassword = document.getElementById("registration_form_plainPassword_first");
const inputPasswordConfirm = document.getElementById("registration_form_plainPassword_second");
const inputAgreeTerms = document.getElementById("registration_form_agreeTerms");

const feedbackNom = document.getElementById("feedbackFirstName");
const feedbackPrenom = document.getElementById("feedbackLastName");
const feedbackPseudo = document.getElementById("feedbackPseudo");
const feedbackEmail = document.getElementById("feedbackEmail");
const feedbackPassword = document.getElementById("feedbackPassword");
const feedbackPasswordConfirm = document.getElementById("feedbackPasswordConfirm");
const feedbackAgreeTerms = document.getElementById("feedbackCheckBox");

 const feedbackNomSpan = document.getElementById("firstNameErrorMessage");
 const feedbackPrenomSpan = document.getElementById("lastNameErrorMessage");
 const feedbackPseudoSpan = document.getElementById("pseudoErrorMessage");
 const feedbackEmailSpan = document.getElementById("emailErrorMessage");
 const feedbackPasswordSpan = document.getElementById("passwordErrorMessage");
 const feedbackPasswordConfirmSpan = document.getElementById("passwordConfirmErrorMessage");
 const feedbackAgreeTermsSpan = document.getElementById("agreeTermsErrorMessage");

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
    const nomOK = validateRequired(inputNom, feedbackNomSpan, "Veuillez entrer votre nom.");
    const prenomOK = validateRequired(inputPrenom, feedbackPrenomSpan, "Veuillez entrer votre prénom.");
    const pseudoOK = validateRequired(inputPseudo, feedbackPseudoSpan, "Veuillez entrer votre pseudonyme.");
    const emailOK = validateEmail(inputEmail, feedbackEmailSpan, "Veuillez entrer une adresse e-mail valide.");
    const passwordOK = validatePassword(inputPassword, feedbackPasswordSpan);
    const passwordConfirmOK = validateConfirmationPassword(inputPasswordSpan, inputPasswordConfirm, feedbackPasswordConfirm);
    const agreeTermsOK = validateCheckbox(inputAgreeTerms, feedbackAgreeTermsSpan, "Vous devez accepter les conditions d'utilisation.");

    if (nomOK && prenomOK && pseudoOK && emailOK && passwordOK && passwordConfirmOK && agreeTermsOK) {
        btnValidation.disabled = false;
    } else {
        btnValidation.disabled = true;
    }
}

// affiche / masque le message d'erreur
function displayFeedback(feedbackElementSpan, message, isValid) {
    if (isValid) {
        feedbackElementSpan.textContent ="";
    } else { 
        feedbackElementSpan.textContent = message;
    }
}

// Validation des champs requis
function validateRequired(input, feedbackElementSpan, errorMessage) {
    if (input.value.trim() !== "") {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        feedbackElementSpan.textContent = "";
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        feedbackElementSpan.textContent = errorMessage;
        return false;
    }
}

// Validation de l'email
function validateEmail(input, feedbackElementSpan, errorMessage) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (input.value.trim().match(emailRegex)) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        feedbackElementSpan.textContent = "";
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        feedbackElementSpan.textContent = errorMessage;
        return false;
    }
}

// Validation du mot de passe
function validatePassword(input, feedbackElementSpan) {
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
        feedbackElementSpan.textContent = message ||"";
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        feedbackElementSpan.textContent = message || "Le mot de passe ne respecte pas les critères de sécurité requis.";
        return false;
    }
}

// Validation de la confirmation du mot de passe
function validateConfirmationPassword(inputPwd, inputConfirmPwd, feedbackElementSpan) {
    if (inputPwd.value === inputConfirmPwd.value && inputConfirmPwd.value.length > 0) {
        inputConfirmPwd.classList.add("is-valid");
        inputConfirmPwd.classList.remove("is-invalid");
        feedbackElementSpan.textContent = "";
        return true;  
    } else {
        inputConfirmPwd.classList.add("is-invalid");
        inputConfirmPwd.classList.remove("is-valid");
        feedbackElementSpan.textContent = "Les mots de passe ne correspondent pas.";
        return false;
}
}

// validation de la checkbox
function validateCheckbox(input, feedbackElementSpan, errorMessage) {
    if (input.checked) {
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        feedbackElementSpan.textContent = "";
        return true;
    } else {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
        feedbackElementSpan.textContent = errorMessage;
        return false;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    validateForm();
});