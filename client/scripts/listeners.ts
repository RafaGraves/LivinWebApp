
const modalContainer = document.querySelector<HTMLDivElement>("#modal-container");
const registrationModalContainer = document.getElementById("session-modal");
//const registrationBackgroundContainer = document.getElementById("session-modal");

const registrationHTMLForm: string = '<div class="register"><header class="registration-header">' +
    '<h2>Regístrate</h2></header><div id="loading-overlay" class="form-loading-overlay">' +
    '<div class="loader"></div></div><div id="general-error" class="bked-error"></div>' +
    '<form id="registrationForm"><div class="form-group"><div class="col">' +
    '<input class="reg-form-input" type="text" name="firstname" id="firstname" required><label for="firstname">Nombre</label>' +
    '</div><div class="col"><input class="reg-form-input" type="text" name="lastname" id="lastname" required><label for="lastname">Apellidos</label>' +
    '</div></div><div class="form-group form-group--single"><div class="col2">' +
    '<input class="reg-form-input" type="password" id="password" name="password" minlength="6" oninput="passwordChange(this)" required>' +
    '<label for="password">Contraseña</label><div id="psw-error" class="error-message"></div></div>\</div>' +
    '<div class="form-group form-group--single"><div class="col2">' +
    '<input class="reg-form-input" type="password" id="confirmPassword" name="confirmPassword" oninput="confPasswordChange(this)" required>' +
    '<label for="confirmPassword">Confirmar contraseña</label><div id="conf-psw-error" class="error-message"></div></div>' +
    '</div> <div class="form-group form-group--single"><div class="col2">' +
    '<input class="reg-form-input" type="tel" name="phone" id="phone" pattern="\\d{2}-\\d{4}-\\d{4}" maxlength="12" oninput="formatPhoneNumber(this)" required>' +
    '<label for="phone">Número telefónico</label><div id="phone-message" class="correct-message"></div></div>\</div>' +
    '<div class="form-group form-group--single"> <div class="col2"><input class="reg-form-input" type="email" name="email" id="email" required>' +
    '<label for="email">e-mail</label><div id="email-error" class="error-message"></div></div>' +
    '</div><button class="submit-button" type="submit" value="Registrar">Registrar</button></form></div>';

const signInHTMLForm: string = '<div class="register"><header class="signin-header"><h2>Inicia sesión</h2></header>' +
    '<div class="form-loading-overlay" id="loading-overlay"> <div class="loader"></div></div>' +
    '<div class="bked-error" id="general-error"></div><form id="signInForm" method="post"><div class="form-group form-group--single">' +
    '<div class="col2"><input class="reg-form-input" id="signin-email" name="email" required type="email">' +
    '<label for="email">e-mail</label><div class="error-message" id="email-session-error"></div></div></div>' +
    '<div class="form-group form-group--single"><div class="col2">' +
    '<input class="reg-form-input" id="signin-password" minlength="6" name="password" required type="password">' +
    '<label for="password">Contraseña</label><div class="error-message" id="psw-session-error"></div></div>' +
    '</div><button class="submit-button" type="submit" value="Ingresar">Ingresar</button></form></div>';

function installNavSessionButtons() {
    const regBtnElement = document.getElementById("registrationButton");
    const signInBtnElement = document.getElementById("signInButton");

    regBtnElement?.addEventListener("click", function () {
        if (modalContainer == null)
            return;

        // Restore the class because other buttons can change it
        registrationModalContainer!.removeAttribute('class');
        registrationModalContainer!.classList.add('modal');

        registrationModalContainer!.innerHTML = registrationHTMLForm;

        const inputs = document.querySelectorAll('.reg-form-input') as NodeListOf<HTMLInputElement>;
        inputs.forEach((input: HTMLInputElement) => {
            input.addEventListener('change', () => {
                if (input.value !== '') {
                    input.classList.add('has-value');
                } else {
                    input.classList.remove('has-value');
                }
            });
        });

        const registrationForm = document.getElementById('registrationForm');
        registrationForm?.addEventListener('submit', validateRegistrationForm);

        modalContainer.removeAttribute("class");
        modalContainer.classList.add('activeButton');
        document.body.classList.add("modal-active");
    });

    signInBtnElement?.addEventListener('click', function () {
        if (modalContainer == null)
            return;

        // Restore the class because other buttons can change it
        registrationModalContainer!.removeAttribute('class');
        registrationModalContainer!.classList.add('signin-modal');

        registrationModalContainer!.innerHTML = signInHTMLForm;

        const inputs = document.querySelectorAll('.reg-form-input') as NodeListOf<HTMLInputElement>;
        inputs.forEach((input: HTMLInputElement) => {
            input.addEventListener('change', () => {
                if (input.value !== '') {
                    input.classList.add('has-value');
                } else {
                    input.classList.remove('has-value');
                }
            });
        });

        const signInFormElement = document.getElementById('signInForm');
        signInFormElement?.addEventListener('submit', signInForm);

        modalContainer.removeAttribute("class");
        modalContainer.classList.add('activeButton');
        document.body.classList.add("modal-active");
    })
}
installNavSessionButtons();


modalContainer?.addEventListener("click", function () {
    // Remove the HTML, so when the user goes back in the browser does not show the form
    registrationModalContainer!.innerHTML = '';
    modalContainer.classList.add("out");
    document.body.classList.remove("modal-active");
});


// Prevent the modal closing if the user clicks within the container area
registrationModalContainer?.addEventListener('click', eventPreventModalPropagation);

function eventPreventModalPropagation(event: MouseEvent) {
    event.stopPropagation();
}

// Check when we are about to leave the page
addEventListener('beforeunload', userUnloadSession);
