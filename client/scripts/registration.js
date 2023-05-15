"use strict";
function validatePassword(input, inputError) {
    const pswInputLength = input.value.length;
    inputError.innerText = '';
    if (pswInputLength < 6) {
        inputError.style.visibility = 'block';
        inputError.innerText = 'Escribe una constraseña de al menos 6 caracteres';
        throw Error('invalid password');
    }
}
function validateMatchingPasswords(input0, input1) {
    const pswError = document.getElementById('conf-psw-error');
    pswError.innerText = '';
    if (input0.value !== input1.value) {
        pswError.innerText = 'Las contraseñas no coinciden';
        throw Error('mismatching passwords');
    }
}
function validatePhoneNumber(input) {
    const phoneInput = document.getElementById('phone-message');
    const phoneRegex = /^\d{2}-\d{4}-\d{4}$/;
    phoneInput.innerText = '';
    if (!phoneRegex.test(input.value)) {
        phoneInput.className = 'error-message';
        phoneInput.innerText = 'Escribe un número telefónico válido';
        throw Error('invalid phone number');
    }
}
function validateMail(input, inputError) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(input?.value)) {
        inputError.innerText = 'Escribe un correo electrónico válido';
        throw Error('invalid mail');
    }
    else {
        inputError.innerText = '';
    }
}
function mailProblem(message) {
    const emailError = document.getElementById('email-error');
    emailError.innerText = message;
}
async function validateRegistrationForm(event) {
    event.preventDefault();
    const overlayBlock = document.getElementById('loading-overlay');
    const registrationFormElement = document.getElementById('registrationForm');
    try {
        Array.from(registrationFormElement.elements).forEach((element) => {
            element.disabled = true;
        });
        overlayBlock.style.display = 'block';
        const pswInput = document.getElementById('password');
        validatePassword(pswInput, document.getElementById('psw-error'));
        const pswConfirmation = document.getElementById('confirmPassword');
        validateMatchingPasswords(pswInput, pswConfirmation);
        const phNumber = document.getElementById('phone');
        validatePhoneNumber(phNumber);
        const email = document.getElementById('email');
        validateMail(email, document.getElementById('email-error'));
        const formDataJson = JSON.stringify({
            name: document.getElementById('firstname').value,
            lastname: document.getElementById('lastname').value,
            password: await hashPassword(pswInput.value),
            phone: phNumber.value,
            email: email.value
        });
        await fetch('http://livin.test:4000/api/session/registration', {
            method: 'POST',
            body: formDataJson,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + window.__server
            }
        }).then(async (response) => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                if (response.ok)
                    return response.json();
                else
                    throw new LivinRequestExceptionHandler(response.status, (await response.json()));
            }
        }).then(data => {
            switch (data.status) {
                case 0:
                    const registrationModalContainer = document.getElementById("session-modal");
                    if (registrationModalContainer != null) {
                        registrationModalContainer.innerHTML = '';
                        registrationModalContainer.removeAttribute('class');
                        registrationModalContainer.classList.add('ok-modal');
                        registrationModalContainer.innerHTML = '<div class=\"ok-image\">' +
                            '<img src=\"Images/Icons8_flat_ok.svg.png\" alt=\"ok image\"/></div>' +
                            '<h1>Gracias por registrarte</h1>' +
                            '<h2>Enseguida llegará un correo a la dirección que especificaste para verificar tu usuario</h2>' +
                            '<div id=\"ok-button-success-form\" class=\"ok-button\" onclick=\"terminateRegistrationModalFromOK(registrationModalContainer);\">' +
                            '<h3>Aceptar</h3></div>';
                    }
                    break;
                default:
                    break;
            }
        }).catch(error => {
            if (error instanceof LivinRequestExceptionHandler) {
                const rError = error;
                switch (rError.Response.status) {
                    case 3001:
                        mailProblem('El correo que escribiste ya se encuentra registrado');
                        break;
                    case 3002:
                        mailProblem('No se puede enviar el correo a la dirección proporcionada');
                        break;
                    case 3000:
                        break;
                    default:
                        console.log('DEFAULT \'' + rError.Response.status + '\'');
                }
            }
            else {
                console.error(`Error message:  ${error.message}`);
                const modalContainer = document.querySelector("#modal-container");
                const clickEvent = new MouseEvent('click', {
                    view: window,
                    bubbles: false,
                    cancelable: true
                });
                modalContainer?.dispatchEvent(clickEvent);
                window.location.href = 'excep/oops.html?from=home';
            }
            Array.from(registrationFormElement.elements).forEach((element) => {
                element.disabled = false;
            });
            overlayBlock.style.display = 'none';
        });
    }
    catch (e) {
        Array.from(registrationFormElement.elements).forEach((element) => {
            element.disabled = false;
        });
        overlayBlock.style.display = 'none';
    }
}
function passwordChange(input) {
    try {
        validatePassword(input, document.getElementById('email-error'));
    }
    catch (e) {
    }
}
function confPasswordChange(input) {
    const pswError = document.getElementById('conf-psw-error');
    if (pswError.innerText !== '')
        pswError.innerText = '';
}
function formatPhoneNumber(input) {
    const phoneInput = document.getElementById('phone-message');
    let phoneNumber = input.value.replace(/\D/g, '');
    const phoneNumberLength = phoneNumber.length;
    if (phoneNumberLength === 10) {
        phoneNumber = phoneNumber.slice(0, 2) + '-' + phoneNumber.slice(2, 6) + '-' + phoneNumber.slice(6, 10);
        input.value = phoneNumber;
        input.setCustomValidity('');
        const event = new Event('change');
        input.dispatchEvent(event);
        phoneInput.className = 'correct-message';
        phoneInput.innerText = 'Número telefónico válido';
    }
    else {
        phoneInput.className = 'error-message';
        phoneInput.innerText = 'Escribe un número telefónico válido';
    }
}
