const regBtnElement = document.getElementById("registrationButton");
const modalContainer = document.querySelector<HTMLDivElement>("#modal-container");
const registrationModalContainer = document.getElementById("registration-modal");
const registrationBackgroundContainer = document.getElementById("registration-modal");

const registrationHTMLForm: string = '<div class="register"><header class="registration-header">' +
    '<h2>Regístrate</h2></header><div id="loading-overlay" class="form-loading-overlay">' +
    '<div class="loader"></div></div><div id="general-error" class="bked-error">eee</div>' +
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

regBtnElement?.addEventListener("click", function () {
    const buttonId = regBtnElement.getAttribute("id");
    if (modalContainer == null || buttonId == null)
        return;

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
    registrationForm?.addEventListener('submit', validateForm);

    modalContainer.removeAttribute("class");
    modalContainer.classList.add(buttonId);
    document.body.classList.add("modal-active");
});


modalContainer?.addEventListener("click", function () {
    // Remove the HTML, so when the user goes back in the browser does not show the form
    registrationModalContainer!.innerHTML = '';
    modalContainer.classList.add("out");
    document.body.classList.remove("modal-active");
});


// Prevent the modal closing if the user clicks within the container area
registrationBackgroundContainer?.addEventListener('click', eventPreventModalPropagation);

function eventPreventModalPropagation(event: MouseEvent) {
    event.stopPropagation();
}
