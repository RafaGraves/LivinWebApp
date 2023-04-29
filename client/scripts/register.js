function validatePassword(input) {
    // Must be of at least 6 digits
    const pswInputLength = input.value.length;
    const pswLenError = document.getElementById('psw-error');
    pswLenError.innerText = '';
    if (pswInputLength < 6) {
        pswLenError.style.visibility = 'block';
        pswLenError.innerText = 'Escribe una constraseña de al menos 6 caracteres';
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

function validateMail(input) {
    const emailInput = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailError = document.getElementById('email-error');

    // Validate email
    if (!emailRegex.test(emailInput.value)) {
        emailError.innerText = 'Escribe un correo electrónico válido';
        throw Error('invalid mail');
    } else {
        emailError.innerText = '';
    }
}

function mailProblem(message) {
    const emailError = document.getElementById('email-error');
    emailError.innerText = message;
}

async function validateForm(event) {
    event.preventDefault();
    const overlayBlock = document.getElementById('loading-overlay');
    const registrationFormElement = document.getElementById('registrationForm');

    async function hashPassword(password) {
        const sha256 = window.crypto.subtle.digest('SHA-256', new TextEncoder().encode(password));

        return new Promise((resolve, reject) => {
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
        Array.from(registrationFormElement.elements).forEach((element) => {
            element.disabled = true;
        });
        overlayBlock.style.display = 'block';

        const pswInput = document.getElementById('password');
        validatePassword(pswInput);

        const pswConfirmation = document.getElementById('confirmPassword');
        validateMatchingPasswords(pswInput, pswConfirmation);

        const phNumber = document.getElementById('phone');
        validatePhoneNumber(phNumber);

        const email = document.getElementById('email');
        validateMail(email);


        const formDataJson = JSON.stringify({
            name: document.getElementById('firstname').value,
            lastname: document.getElementById('lastname').value,
            password: await hashPassword(pswInput.value),
            phone: phNumber.value,
            email: email.value
        });

        fetch('http://localhost:5000/api/signup', {
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
            console.log(data);
            console.log(data.code);
            console.log(typeof data.code);
            switch (data.code) {
                case 0:
                    break;
                case 3001:
                    mailProblem('El correo que escribiste ya se encuentra registrado');
                    throw TypeError('api');
                case 3002:
                    mailProblem('No se puede enviar el correo a la dirección proporcionada');
                    throw TypeError('api');
                case 3000:
                    break;
                default:
                    break;
            }
        }).catch(error => {
            if (error.message !== 'api') {
                console.error('Error message:', error.message);
                window.location.href = 'http://localhost:63342/LivinWebApp/client/pages/excep/oops.html?from=registration';
            } else {
                // Enable each element
                Array.from(registrationFormElement.elements).forEach((element) => {
                    element.disabled = false;
                });
                overlayBlock.style.display = 'none';
            }
        });

    } catch (e) {
        // Enable each element
        Array.from(registrationFormElement.elements).forEach((element) => {
            element.disabled = false;
        });
        overlayBlock.style.display = 'none';
    }
}

function passwordChange(input) {
    try {
        validatePassword(input);
    } catch (e) {
        //  console.error(e);
    }
}

function confPasswordChange(input) {
    const pswError = document.getElementById('conf-psw-error');
    console.log(pswError.innerText);
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
        phoneInput.innerText = 'Número telefónico correcto';
    } else {
        phoneInput.className = 'error-message';
        phoneInput.innerText = 'Escribe un número telefónico válido';
    }
}