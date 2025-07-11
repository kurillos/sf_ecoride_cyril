beforeEach(() => {
    document.body.innerHTML = `
        <form class="needs-validation">
            <div class="mb-3">
                <input type="text" id="registration_form_firstName" class="form-control">
                <div id="feedbackFirstName" class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <input type="text" id="registration_form_lastName" class="form-control">
                <div id="feedbackLastName" class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <input type="text" id="registration_form_pseudo" class="form-control">
                <div id="feedbackPseudo" class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <input type="email" id="registration_form_email" class="form-control">
                <div id="feedbackEmail" class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <input type="password" id="registration_form_plainPassword_first" class="form-control">
                <div id="feebdackPassword" class="invalid-feedback"></div> </div>
            <div class="mb-3">
                <input type="password" id="registration_form_plainPassword_second" class="form-control">
                <div id="feedbackPasswordConfirm" class="invalid-feedback"></div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="registration_form_agreeTerms" class="form-check-input">
                <div id="feedbackCheckBox" class="invalid-feedback"></div>
            </div>
            <div class="d-grid">
                <button type="submit" id="btn-validation-inscription" class="btn btn-success btn-lg">S'inscrire</button>
            </div>
        </form>
    `;
    require('../form_validation.js');



const getElements = () => {
    return {
        inputNom: document.getElementById("registration_form_firstName"),
        inputPrenom: document.getElementById("registration_form_lastName"),
        inputPseudo: document.getElementById("registration_form_pseudo"),
        inputEmail: document.getElementById("registration_form_email"),
        inputPassword: document.getElementById("registration_form_plainPassword_first"),
        inputPasswordConfirm: document.getElementById("registration_form_plainPassword_second"),
        inputAgreeTerms: document.getElementById("registration_form_agreeTerms"),
        feedbackNom: document.getElementById("feedbackFirstName"),
        feedbackPrenom: document.getElementById("feedbackLastName"),
        feedbackPseudo: document.getElementById("feedbackPseudo"),
        feedbackEmail: document.getElementById("feedbackEmail"),
        feedbackPassword: document.getElementById("feedbackPassword"),
        feedbackPasswordConfirm: document.getElementById("feedbackPasswordConfirm"),
        feedbackAgreeTerms: document.getElementById("feedbackCheckBox"),
        btnValidation: document.getElementById("btn-validation-inscription"),
        form: document.querySelector('.needs-validation')
    };
};

describe('Form Validation - Frontend (JavaScript)', () => {

    test('should disable button initially if form is invalid', () => {
        const { btnValidation } = getElements();
        expect(btnValidation.disabled).toBe(true);
    });

    test('validateRequired should add is-invalid and show message for empty input', () => {
        const { inputNom, feedbackNom } = getElements();
        inputNom.value = '';
        inputNom.dispatchEvent(new Event('keyup'));
        expect(inputNom.classList.contains('is-invalid')).toBe(true);
        expect(feedbackNom.textContent).toBe('Veuillez entrer votre prénom.');
    });

    test('validateRequired should add is-valid and clear message for valid input', () => {
        const { inputNom, feedbackNom } = getElements();
        inputNom.value = 'Cyril';
        inputNom.dispatchEvent(new Event('keyup'));
        expect(inputNom.classList.contains('is-valid')).toBe(true);
        expect(feedbackNom.textContent).toBe('');
    });

    test('validateEmail should add is-invalid and show message for invalid email', () => {
        const { inputEmail, feedbackEmail } = getElements();
        inputEmail.value = 'invalid-email';
        inputEmail.dispatchEvent(new Event('keyup'));
        expect(inputEmail.classList.contains('is-invalid')).toBe(true);
        expect(feedbackEmail.textContent).toBe('Veuillez entrer une adresse e-mail valide.');
    });

    test('validateEmail should add is-valid and clear message for valid email', () => {
        const { inputEmail, feedbackEmail } = getElements();
        inputEmail.value = 'test@example.com';
        inputEmail.dispatchEvent(new Event('keyup'));
        expect(inputEmail.classList.contains('is-valid')).toBe(true);
        expect(feedbackEmail.textContent).toBe('');
    });

    test('validatePassword should add is-invalid and show message for short password', () => {
        const { inputPassword, feedbackPassword } = getElements();
        inputPassword.value = 'short';
        inputPassword.dispatchEvent(new Event('keyup'));
        expect(inputPassword.classList.contains('is-invalid')).toBe(true);
        expect(feedbackPassword.textContent).toBe('Votre mot de passe doit contenir au moins 8 caractères.');
    });

    test('validatePassword should add is-valid and clear message for strong password', () => {
        const { inputPassword, feedbackPassword } = getElements();
        inputPassword.value = 'StrongPass123!';
        inputPassword.dispatchEvent(new Event('keyup'));
        expect(inputPassword.classList.contains('is-valid')).toBe(true);
        expect(feedbackPassword.textContent).toBe('');
    });

    test('validateConfirmationPassword should add is-invalid and show message for mismatched passwords', () => {
        const { inputPassword, inputPasswordConfirm, feedbackPasswordConfirm } = getElements();
        inputPassword.value = 'SecurePass123!';
        inputPasswordConfirm.value = 'MismatchPass123!';
        inputPasswordConfirm.dispatchEvent(new Event('keyup'));
        expect(inputPasswordConfirm.classList.contains('is-invalid')).toBe(true);
        expect(feedbackPasswordConfirm.textContent).toBe('Les mots de passe ne correspondent pas.');
    });

    test('validateConfirmationPassword should add is-valid and clear message for matching passwords', () => {
        const { inputPassword, inputPasswordConfirm, feedbackPasswordConfirm } = getElements();
        inputPassword.value = 'SecurePass123!';
        inputPasswordConfirm.value = 'SecurePass123!';
        inputPasswordConfirm.dispatchEvent(new Event('keyup'));
        expect(inputPasswordConfirm.classList.contains('is-valid')).toBe(true);
        expect(feedbackPasswordConfirm.textContent).toBe('');
    });

    test('validateCheckbox should add is-invalid and show message if not checked', () => {
        const { inputAgreeTerms, feedbackAgreeTerms } = getElements();
        inputAgreeTerms.checked = false;
        inputAgreeTerms.dispatchEvent(new Event('change'));
        expect(inputAgreeTerms.classList.contains('is-invalid')).toBe(true);
        expect(feedbackAgreeTerms.textContent).toBe('Vous devez accepter les conditions d\'utilisation.');
    });

    test('validateCheckbox should add is-valid and clear message if checked', () => {
        const { inputAgreeTerms, feedbackAgreeTerms } = getElements();
        inputAgreeTerms.checked = true;
        inputAgreeTerms.dispatchEvent(new Event('change'));
        expect(inputAgreeTerms.classList.contains('is-valid')).toBe(true);
        expect(feedbackAgreeTerms.textContent).toBe('');
    });


    test('form submission should be prevented if form is invalid', () => {
        const { form, inputNom, btnValidation } = getElements();

        inputNom.value = '';
        inputNom.dispatchEvent(new Event('keyup')); 

        
        const submitEvent = new Event('submit', { cancelable: true });
        submitEvent.preventDefault = jest.fn(); 

        form.dispatchEvent(submitEvent);

        expect(submitEvent.preventDefault).toHaveBeenCalled(); 
        expect(form.classList.contains('was-validated')).toBe(true);
        expect(btnValidation.disabled).toBe(true);
    });

    test('form submission should proceed if form is valid', () => {
        const { form, inputNom, inputPrenom, inputPseudo, inputEmail, inputPassword, inputPasswordConfirm, inputAgreeTerms, btnValidation } = getElements();

     
        inputNom.value = 'John';
        inputPrenom.value = 'Doe';
        inputPseudo.value = 'johndoe';
        inputEmail.value = 'john.doe@example.com';
        inputPassword.value = 'SecurePass123!';
        inputPasswordConfirm.value = 'SecurePass123!';
        inputAgreeTerms.checked = true;

     
        inputNom.dispatchEvent(new Event('keyup'));

        const submitEvent = new Event('submit', { cancelable: true });
        submitEvent.preventDefault = jest.fn();

        form.dispatchEvent(submitEvent);

        expect(submitEvent.preventDefault).not.toHaveBeenCalled(); 
        expect(form.classList.contains('was-validated')).toBe(false); 
        expect(btnValidation.disabled).toBe(false);
    });
});