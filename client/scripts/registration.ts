function validatePassword(input: HTMLInputElement, inputError: HTMLDivElement) {
    // Must be of at least 6 digits
    const pswInputLength = input.value.length;
    inputError.innerText = '';
    if (pswInputLength < 6) {
        inputError.style.visibility = 'block';
        inputError.innerText = 'Escribe una constraseña de al menos 6 caracteres';
        throw Error('invalid password');
    }
}

function validateMatchingPasswords(input0: HTMLInputElement, input1: HTMLInputElement) {
    const pswError = document.getElementById('conf-psw-error') as HTMLDivElement;
    pswError.innerText = '';
    if (input0.value !== input1.value) {
        pswError.innerText = 'Las contraseñas no coinciden';
        throw Error('mismatching passwords');
    }
}

function validatePhoneNumber(input: HTMLInputElement) {
    const phoneInput = document.getElementById('phone-message') as HTMLInputElement;
    const phoneRegex = /^\d{2}-\d{4}-\d{4}$/;
    phoneInput.innerText = '';
    if (!phoneRegex.test(input.value)) {
        phoneInput.className = 'error-message';
        phoneInput.innerText = 'Escribe un número telefónico válido';
        throw Error('invalid phone number');
    }
}

function validateMail(input: HTMLInputElement, inputError: HTMLDivElement) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Validate email
    if (!emailRegex.test(input?.value)) {
        inputError.innerText = 'Escribe un correo electrónico válido';
        throw Error('invalid mail');
    } else {
        inputError.innerText = '';
    }
}

function mailProblem(message: string) {
    const emailError = document.getElementById('email-error') as HTMLInputElement;
    emailError.innerText = message;
}


async function validateRegistrationForm(event: Event) {
    event.preventDefault();
    const overlayBlock = document.getElementById('loading-overlay') as HTMLDivElement;
    const registrationFormElement = document.getElementById('registrationForm') as HTMLFormElement;


    try {
        // Disable each form element
        Array.from(registrationFormElement.elements).forEach((element: Element) => {
            (element as HTMLInputElement).disabled = true;
        });
        overlayBlock.style.display = 'block';

        const pswInput = document.getElementById('password') as HTMLInputElement;
        validatePassword(pswInput, document.getElementById('psw-error') as HTMLDivElement);

        const pswConfirmation = document.getElementById('confirmPassword') as HTMLInputElement;
        validateMatchingPasswords(pswInput, pswConfirmation);

        const phNumber = document.getElementById('phone') as HTMLInputElement;
        validatePhoneNumber(phNumber);

        const email = document.getElementById('email') as HTMLInputElement;
        validateMail(email, document.getElementById('email-error') as HTMLDivElement);


        const formDataJson = JSON.stringify({
            name: (document.getElementById('firstname') as HTMLInputElement).value,
            lastname: (document.getElementById('lastname') as HTMLInputElement).value,
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
                    throw new LivinRequestExceptionHandler(response.status, (await response.json()) as ServerErrorResponse);
            }
        }).then(data => {
            switch (data.status) {
                case 0:
                    // Everything ok
                    // Get the registration modal
                    const registrationModalContainer = document.getElementById("session-modal");
                    if (registrationModalContainer != null) {
                        // Clear its HTML Contents
                        registrationModalContainer.innerHTML = '';

                        // Change the class to the ok modal
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
                const rError = error as LivinRequestExceptionHandler;
                switch (rError.Response.status) {
                    case 3001:
                        mailProblem('El correo que escribiste ya se encuentra registrado');
                        break;
                    case 3002:
                        mailProblem('No se puede enviar el correo a la dirección proporcionada');
                        break;
                    case 3000:
                        // TODO: this
                        break;
                    default:
                        console.log('DEFAULT \'' + rError.Response.status+'\'');
                }
            } else {
                console.error(`Error message:  ${error.message}`);
                // Send a click event to the modal container
                // To remove the form HTML of the div
                // And to 'hide' the form ad the overlay
                // This event is being listened and set it up in listeners.ts
                // This is done, in order to prevent that the registration form is displayed
                // when the user clicks 'Back' in the browser
                const modalContainer = document.querySelector<HTMLDivElement>("#modal-container");
                const clickEvent = new MouseEvent('click', {
                    view: window,
                    bubbles: false, // prevent propagation
                    cancelable: true
                });
                modalContainer?.dispatchEvent(clickEvent);
                window.location.href = 'excep/oops.html?from=home';
            }
            Array.from(registrationFormElement.elements).forEach((element) => {
                (element as HTMLInputElement).disabled = false;
            });
            overlayBlock.style.display = 'none';
        });

    } catch (e) {
        // Enable each element
        Array.from(registrationFormElement.elements).forEach((element) => {
            (element as HTMLInputElement).disabled = false;
        });
        overlayBlock.style.display = 'none';
    }
}

function passwordChange(input: HTMLInputElement) {
    try {
        validatePassword(input, document.getElementById('email-error') as HTMLDivElement);
    } catch (e) {
        //  console.error(e);
    }
}

function confPasswordChange(input: HTMLInputElement) {
    const pswError = document.getElementById('conf-psw-error') as HTMLDivElement;
    if (pswError.innerText !== '')
        pswError.innerText = '';
}

function formatPhoneNumber(input: HTMLInputElement) {
    const phoneInput = document.getElementById('phone-message') as HTMLInputElement;
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
    } else {
        phoneInput.className = 'error-message';
        phoneInput.innerText = 'Escribe un número telefónico válido';
    }
}