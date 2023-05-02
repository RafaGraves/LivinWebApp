function validatePassword(input: HTMLInputElement) {
    // Must be of at least 6 digits
    const pswInputLength = input.value.length;
    const pswLenError = document.getElementById('psw-error') as HTMLDivElement;
    pswLenError.innerText = '';
    if (pswInputLength < 6) {
        pswLenError.style.visibility = 'block';
        pswLenError.innerText = 'Escribe una constraseña de al menos 6 caracteres';
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

function validateMail(input: HTMLInputElement) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailError = document.getElementById('email-error') as HTMLDivElement;

    // Validate email
    if (!emailRegex.test(input?.value)) {
        emailError.innerText = 'Escribe un correo electrónico válido';
        throw Error('invalid mail');
    } else {
        emailError.innerText = '';
    }
}

function mailProblem(message: string) {
    const emailError = document.getElementById('email-error') as HTMLInputElement;
    emailError.innerText = message;
}

function terminateRegistrationModalFromOK(_regModalContainer: HTMLDivElement) {
    const modalContainer = document.querySelector("#modal-container");
    _regModalContainer!.innerHTML = '';
    modalContainer?.classList.add("out");
    document.body.classList.remove("modal-active");
}

async function validateForm(event: Event) {
    event.preventDefault();
    const overlayBlock = document.getElementById('loading-overlay') as HTMLDivElement;
    const registrationFormElement = document.getElementById('registrationForm') as HTMLFormElement;

    async function hashPassword(password: string): Promise<string> {
        const sha256 = window.crypto.subtle.digest('SHA-256', new TextEncoder().encode(password));

        return new Promise<string>((resolve, reject) => {
            sha256.then(buffer => {
                const hashArray = Array.from(new Uint8Array(buffer));
                const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
                resolve(hashHex);
            }).catch(error => {
                reject(error);
            });
        });
    }

    try {
        // Disable each form element
        Array.from(registrationFormElement.elements).forEach((element: Element) => {
            (element as HTMLInputElement).disabled = true;
        });
        overlayBlock.style.display = 'block';

        const pswInput = document.getElementById('password') as HTMLInputElement;
        validatePassword(pswInput);

        const pswConfirmation = document.getElementById('confirmPassword') as HTMLInputElement;
        validateMatchingPasswords(pswInput, pswConfirmation);

        const phNumber = document.getElementById('phone') as HTMLInputElement;
        validatePhoneNumber(phNumber);

        const email = document.getElementById('email') as HTMLInputElement;
        validateMail(email);


        const formDataJson = JSON.stringify({
            name: (document.getElementById('firstname') as HTMLInputElement).value,
            lastname: (document.getElementById('lastname') as HTMLInputElement).value,
            password: await hashPassword(pswInput.value),
            phone: phNumber.value,
            email: email.value
        });

        fetch('http://localhost:4000/api/signup', {
            method: 'POST',
            body: formDataJson,
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                return response.json();
            } else {
                throw new TypeError('Response from backend is not JSON');
            }
        }).then(data => {
            switch (data.code) {
                case 0:
                    // Everything ok
                    // Get the registration modal
                    const registrationModalContainer = document.getElementById("registration-modal");
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
                case 3001:
                    mailProblem('El correo que escribiste ya se encuentra registrado');
                    throw TypeError('api');
                case 3002:
                    mailProblem('No se puede enviar el correo a la dirección proporcionada');
                    throw TypeError('api');
                case 3000:
                    // TODO: this
                    break;
                default:
                    break;
            }
        }).catch(error => {
            if (error.message !== 'api') {
                console.error(`Error message:  ${error.message}`);
                // Send a click event to the modal container
                // To remove the form HTML of the div
                // And to 'hide' the form ad the overlay
                // This event is being listened and set it up in registration_listener.ts
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
            } else {
                // Enable each element
                Array.from(registrationFormElement.elements).forEach((element) => {
                    (element as HTMLInputElement).disabled = false;
                });
                overlayBlock.style.display = 'none';
            }
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
        validatePassword(input);
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