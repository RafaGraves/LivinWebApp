interface ServerSessionResponse {
    status: number;
    message: string;
    token: string;
}

interface ServerUserNameResponse {
    name: string;
    lastname: string;
}

interface ServerUserImageResponse {
    data: string;
}

async function signInForm(event: Event) {

    event.preventDefault();
    const overlayBlock = document.getElementById('loading-overlay') as HTMLDivElement;
    const signInFormElement = document.getElementById('signInForm') as HTMLFormElement;

    try {
        // Disable each form element
        Array.from(signInFormElement.elements).forEach((element: Element) => {
            (element as HTMLInputElement).disabled = true;
        });

        overlayBlock.style.display = 'block';

        const pswInput = document.getElementById('signin-password') as HTMLInputElement;
        const pswInputError = document.getElementById('psw-session-error') as HTMLInputElement;
        validatePassword(pswInput, pswInputError);

        const email = document.getElementById('signin-email') as HTMLInputElement;
        const emailError = document.getElementById('email-session-error') as HTMLInputElement;
        validateMail(email, emailError);


        const formDataJson = JSON.stringify({
            email: email.value,
            password: await hashPassword(pswInput.value)
        });

        await fetch('http://livin.test:4000/api/session/signin', {
            method: 'POST',
            body: formDataJson,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + window.__server
            }
        }).then(async (response) => {

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                // const json = await response.json();
                if (response.ok) {
                    window.__validate = response.headers.get('X-CSRF-Token') ?? '';
                    return response.json();
                } else
                    throw new LivinRequestExceptionHandler(response.status, (await response.json()) as ServerErrorResponse);
            }

        }).then(async (data: ServerSessionResponse) => {
            // Change the current session token
            window.__server = data.token ?? window.__server; // If session is started the token element will not be returned

            try {
                // Remove the unnecessary elements
                await prepareWebPageForUserInfo();
                // Access username and picture
                await accessUserName();
                // Access user photograph
                await accessUserImage();
            } catch (e: any) {
                console.log(e.message);
            }

            terminateRegistrationModalFromOK(document.getElementById("session-modal") as HTMLDivElement);

        }).catch(error => {
            const registrationModalContainer = document.getElementById("session-modal");
            if (error instanceof LivinRequestExceptionHandler) {
                console.error(error.response);
                switch (error.Response.status) {
                    case 2000:
                        // User does not exist
                        emailError!.innerText = 'El correo no está registrado';
                        break;
                    case 2001:
                        // Wrong password
                        const pswError = document.getElementById('psw-session-error');
                        pswError!.innerText = 'Verifica tu contraseña';
                        break;
                    case 2002:
                        // Not verified
                        if (registrationModalContainer != null) {
                            // Clear its HTML Contents
                            registrationModalContainer.innerHTML = '';

                            // Change the class to the info modal
                            registrationModalContainer.removeAttribute('class');
                            registrationModalContainer.classList.add('refresh-modal');
                            registrationModalContainer.innerHTML = '<div class=\"ok-image\">' +
                                '<img src=\"Images/email-verification-icon.png\" alt=\"email verification image\"/></div>' +
                                '<h1>Verifica tu cuenta</h1>' +
                                '<h2>Revisa tu correo y verifica tu cuenta para que puedas ingresar</h2>' +
                                '<div id=\"ok-button-success-form\" class=\"ok-button\" onclick=\"terminateRegistrationModalFromOK(registrationModalContainer);\">' +
                                '<h3>Cerrar</h3></div>';
                        }
                        break;
                    case 4001:
                    case 4002:
                    case 4003:
                    case 4004:
                    case 4005:
                    case 4006:
                        // These are authentication errors that will issue because the local token is invalid, either it is corrupted
                        // from the client side or unrecognized by the server side
                        console.error(error.Response.message);
                        // This issue will be more likely to be fixed by a page refresh
                        if (registrationModalContainer != null) {
                            // Clear its HTML Contents
                            registrationModalContainer.innerHTML = '';

                            // Change the class to the error modal
                            registrationModalContainer.removeAttribute('class');
                            registrationModalContainer.classList.add('refresh-modal');

                            registrationModalContainer.innerHTML = '<div class=\"ok-image\">' +
                                '<img src=\"Images/refresh.png\" alt=\"refresh image\"/></div>' +
                                '<h1>Ha ocurrido un problema</h1>' +
                                '<h2>Recarga la página para solucionar el problema</h2>' +
                                '<div id=\"ok-button-success-form\" class=\"ok-button\" onclick=\"location.reload();\">' +
                                '<h3>Recargar</h3></div>';
                        }
                        break;

                }

            } else {
                // THIS IS CONNECTION REFUSED ERROR //
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

            // Enable each element
            Array.from(signInFormElement.elements).forEach((element) => {
                (element as HTMLInputElement).disabled = false;
            });
            // Hide the 'loading' overlay
            overlayBlock.style.display = 'none';
        });

    } catch (e: any) {
        // Enable each element
        Array.from(signInFormElement.elements).forEach((element) => {
            (element as HTMLInputElement).disabled = false;
        });
        overlayBlock.style.display = 'none';
    }
}

async function prepareWebPageForUserInfo() {
    const userInformationContainer = document.getElementById('session-information') as HTMLDivElement;
    if (userInformationContainer != null) {
        userInformationContainer.innerHTML =
            '<button class="btn btn-light" id="user-info-button"><img id="user-picture" src="" width="24" alt=""/></button>' +
            '<button class="btn btn-info" id="sign-out-button" type="button">Cerrar sesión</button>';

        // Install the sign-out listener
        const signOutButton = document.getElementById('sign-out-button') as HTMLButtonElement;
        if (signOutButton != null) {
            signOutButton.addEventListener('click', await signOut);
        }
    }
}

async function accessUserName() {
    await fetch('http://livin.test:4000/api/session/names', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + window.__server, // Session
            'X-CSRF-Token': window.__validate // CSRF
        }
    }).then(async (response) => {
        // Reacquire the token, even in case of error, specially, when the error do not belong to the 4xxx class
        // The CSRF is resent in the headers
        window.__validate = response.headers.get('X-CSRF-Token') ?? window.__validate;

        const contentType = response.headers.get('content-type');

        if (contentType && contentType.indexOf('application/json') !== -1) {
            if (response.ok) {
                return response.json();
            } else
                throw new LivinRequestExceptionHandler(response.status, (await response.json()) as ServerErrorResponse);
        }

    }).then((data: ServerUserNameResponse) => {
        const userInfoButtonContainer = document.getElementById('user-info-button') as HTMLButtonElement;
        userInfoButtonContainer.innerHTML = '<img id="user-picture" src="" width="24" alt=""/>' + data.name + ' ' + data.lastname;
    }).catch(error => {

        const as = error as LivinRequestExceptionHandler;
        console.error(as);

        // Treat any error AS A CONNECTION REFUSED ERROR

        // THIS IS CONNECTION REFUSED ERROR //
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
    });
}

async function accessUserImage() {
    await fetch('http://livin.test:4000/api/images/user?command=resize&w=24&h=24', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + window.__server, // Session
            'X-CSRF-Token': window.__validate // CSRF
        }
    }).then(async (response) => {
        const contentType = response.headers.get('content-type');
        // Reacquire the token, even in case of error, specially, when the error do not belong to the 4xxx class
        // The CSRF is resent in the headers
        window.__validate = response.headers.get('X-CSRF-Token') ?? window.__validate;
        if (contentType && contentType.indexOf('application/json') !== -1) {
            if (response.ok) {
                return response.json();
            } else
                throw new LivinRequestExceptionHandler(response.status, (await response.json()) as ServerErrorResponse);
        }

    }).then((data: ServerUserImageResponse) => {
        const userImageContainer = document.getElementById('user-picture') as HTMLImageElement;
        if (userImageContainer != null)
            userImageContainer.src = data.data;
    }).catch(error => {

        const as = error as LivinRequestExceptionHandler;
        console.error(as.response.message);

        // Treat any error AS A CONNECTION REFUSED ERROR

        // THIS IS CONNECTION REFUSED ERROR //
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
    });
}

async function signOut() {
    await fetch('http://livin.test:4000/api/session/signout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + window.__server, // Session
            'X-CSRF-Token': window.__validate // CSRF
        }
    }).then(async (response) => {
        const contentType = response.headers.get('content-type');
        // We no longer need the CSRF token
        window.__validate = '';
        // However, we need a new token for a local session, which is received in the server response
        if (contentType && contentType.indexOf('application/json') !== -1) {
            if (response.ok) {
                return response.json();
            } else
                throw new LivinRequestExceptionHandler(response.status, (await response.json()) as ServerErrorResponse);
        }

    }).then(async (data: ServerSessionResponse) => {
        // Set the new token for the local session
        window.__server = data.token;

        // Reset the initial HTML in the nav bar
        const userInformationContainer = document.getElementById('session-information') as HTMLDivElement;
        if (userInformationContainer != null) {
            userInformationContainer.innerHTML =
                '<button class="btn btn-light" id=\'signInButton\' type=\"button\">Iniciar sesion</button>' +
                '<button class="btn btn-dark" id="registrationButton" type="button">Registrarse</button>';
        }
        installNavSessionButtons();

    }).catch(error => {

        const as = error as LivinRequestExceptionHandler;
        console.error(as.response.message);

        // Treat any error AS A CONNECTION REFUSED ERROR

        // THIS IS CONNECTION REFUSED ERROR //
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
    });
}