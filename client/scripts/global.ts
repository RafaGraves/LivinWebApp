interface ServerErrorResponse {
    status: number;
    message: string;
}

class LivinRequestExceptionHandler extends Error {

    constructor(public readonly status: number, public readonly response: ServerErrorResponse) {
        super();
    }

    public get Response() {
        return this.response;
    }

    public get Status() {
        return this.status;
    }
}

declare var __validate: string;
declare var __server: string;


window.__server = '';
window.__validate = '';

// Get the local session
fetch('http://livin.test:4000/api/session/local', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    }
}).then(response => {
    if (response.ok) {
        return response.json();
    }
}).then(data => {
    window.__server = data.token;
}).catch(error => {
    // Redirect to an error
    window.location.href = 'excep/oops.html?from=home';
});

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

function terminateRegistrationModalFromOK(_regModalContainer: HTMLDivElement) {
    const modalContainer = document.querySelector("#modal-container");
    _regModalContainer!.innerHTML = '';
    modalContainer?.classList.add("out");
    document.body.classList.remove("modal-active");
}