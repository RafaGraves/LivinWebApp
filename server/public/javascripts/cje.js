// Create JSON error from input

export function JSONError(code, message) {
    const response = {
        code: code,
        message: message
    };
    return JSON.stringify(response);
}
