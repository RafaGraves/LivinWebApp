function validateForm(event) {

    event.preventDefault(); // Prevent default form submission behavior

    const emailInput = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailError = document.getElementById('email-error');

    // Validate email
    if (!emailRegex.test(emailInput.value)) {
        emailError.innerText = 'Escribe un correo electrónico válido';
        return; // Stop execution if email is invalid
    } else {
        emailError.innerText = '';
    }
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