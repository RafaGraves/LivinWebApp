// Create JSON error from input

function JSONError(code, message) {
    return {
        code: code,
        message: message
    };
}

module.exports = {JSONError};