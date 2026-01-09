/**
 * Verifies if the entered values are valid
 * OperativeID -> It is a username created by the user to access; it must be easy for them to remember.
 * Only the following characters are allowed: - _ letters and numbers. The rest will be considered invalid.
 * User Example: Agent_Ukulele => Alto Clef //!Mental note, generate for more users
 * Password -> Minimum 8 and maximum 64, allows all characters, therefore, the input must be sanitized otherwise. DOMPurify Use this library.
 * This code is the controller for all of this.
 */
document.getElementById('login-principal').addEventListener('submit', function (e) {
    e.preventDefault();

    let user = document.getElementById('user');
    let password = document.getElementById('password');
    let errorUser = document.getElementById('errorUser');
    let errorPassword = document.getElementById('errorPassword');

    const error = `<span class="bi bi-exclamation-triangle-fill"></span>`;

    // Clear previous errors
    errorUser.innerHTML = "";
    errorUser.hidden = true;
    errorPassword.innerHTML = "";
    errorPassword.hidden = true;

    // Correct Regex patterns
    const userVerification = /^[a-zA-Z0-9_-]+$/;
    // Note: This regex specifically forbids: " \ / < > = ( )
    const passVerification = /^(?!.*["\\\/<>=()]).{8,64}$/;

    let isValid = true;

    // User validation
    if (user.value.trim() === '') {
        errorUser.hidden = false;
        errorUser.innerHTML = error + ' The username is required.';
        isValid = false;
    } else if (!userVerification.test(user.value.trim())) {
        errorUser.hidden = false;
        errorUser.innerHTML = error + ' Only letters, numbers, "-" and "_" are allowed.';
        isValid = false;
    }

    // Password validation
    if (password.value.trim() === '') {
        errorPassword.hidden = false;
        errorPassword.innerHTML = error + ' The password is required.';
        isValid = false;
    } else if (!passVerification.test(password.value)) {
        errorPassword.hidden = false;
        // Updated message to reflect the Regex constraints better
        errorPassword.innerHTML = error + ' The password must be 8-64 characters and cannot contain special symbols like < > = ( ) " \\ /';
        isValid = false;
    }

    if (isValid) {
        this.submit();
    }
});