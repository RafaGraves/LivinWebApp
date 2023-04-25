// Create JSON error from input

function JSONError(code, message) {
   const response = {
        code: code,
        message: message
    };
    return JSON.stringify(response);
}

module.exports = {JSONError};