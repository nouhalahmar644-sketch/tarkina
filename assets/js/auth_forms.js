/**
 * Validation simple côté navigateur pour les formulaires d'authentification.
 * Utilise les classes Bootstrap "is-invalid" / "was-validated".
 *
 * @param {string} formId - attribut id du formulaire
 * @param {{ mode: 'login' | 'register' }} options
 */
function setupAuthForm(formId, options) {
    var form = document.getElementById(formId);
    if (!form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        var ok = true;

        // Réinitialise les états visuels
        form.classList.remove('was-validated');
        var fields = form.querySelectorAll('.form-control');
        fields.forEach(function (el) {
            el.classList.remove('is-invalid');
        });

        if (options.mode === 'register') {
            ok = validateRegister(form) && ok;
        } else if (options.mode === 'login') {
            ok = validateLogin(form) && ok;
        }

        if (!ok) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        }
    });
}

function validateLogin(form) {
    var ok = true;
    var email = form.querySelector('#email');
    var password = form.querySelector('#password');

    if (!email.value.trim()) {
        markInvalid(email);
        ok = false;
    } else if (!isValidEmail(email.value.trim())) {
        markInvalid(email);
        ok = false;
    }

    if (!password.value) {
        markInvalid(password);
        ok = false;
    }
    return ok;
}

function validateRegister(form) {
    var ok = true;
    var nom = form.querySelector('#nom');
    var prenom = form.querySelector('#prenom');
    var email = form.querySelector('#email');
    var password = form.querySelector('#password');

    if (!nom.value.trim()) {
        markInvalid(nom);
        ok = false;
    }
    if (!prenom.value.trim()) {
        markInvalid(prenom);
        ok = false;
    }
    if (!email.value.trim() || !isValidEmail(email.value.trim())) {
        markInvalid(email);
        ok = false;
    }
    if (!password.value || password.value.length < 6) {
        markInvalid(password);
        ok = false;
    }
    return ok;
}

function isValidEmail(value) {
    // Expression simple pour un débutant (la validation finale reste en PHP)
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function markInvalid(input) {
    if (input) {
        input.classList.add('is-invalid');
    }
}
