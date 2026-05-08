/* =============================================
   PREDIKSIKARIR — AUTH PAGE LOGIC
   login.js
   ============================================= */

'use strict';

/* =============================================
   PANEL SWITCHING
============================================= */

function switchPanel(target) {

    const loginPanel = document.getElementById('loginForm');
    const registerPanel = document.getElementById('registerForm');

    document.getElementById('loginFormElement').reset();
    document.getElementById('registerFormElement').reset();

    clearAllErrors();
    hideAllAlerts();

    if (target === 'login') {

        registerPanel.classList.remove('active');
        loginPanel.classList.add('active');

    } else {

        loginPanel.classList.remove('active');
        registerPanel.classList.add('active');
    }
}

/* =============================================
   PASSWORD TOGGLE
============================================= */

function togglePassword(fieldId, btn) {

    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');

    const hidden = input.type === 'password';

    input.type = hidden ? 'text' : 'password';

    icon.className = hidden
        ? 'bi bi-eye-slash'
        : 'bi bi-eye';
}

/* =============================================
   ERROR HELPERS
============================================= */

function setError(id, message) {

    const el = document.getElementById(id);

    if (!el) return;

    el.textContent = message;

    const group = el.closest('.field-group');

    if (group) {

        const input = group.querySelector('.field-input');

        if (input) {

            input.classList.toggle('input-error', !!message);
        }
    }
}

function clearAllErrors() {

    document.querySelectorAll('.field-error').forEach(el => {

        el.textContent = '';
    });

    document.querySelectorAll('.field-input').forEach(el => {

        el.classList.remove('input-error');
    });
}

function hideAllAlerts() {

    document.querySelectorAll('.alert').forEach(el => {

        el.classList.add('hidden');
    });
}

function showAlert(id, textId, message, type) {

    const alertEl = document.getElementById(id);
    const textEl = document.getElementById(textId);

    if (!alertEl || !textEl) return;

    alertEl.className = `alert alert-${type}`;

    textEl.textContent = message;

    alertEl.classList.remove('hidden');
}

/* =============================================
   VALIDATION
============================================= */

function isValidEmail(email) {

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {

    return /^(\+62|0)[0-9]{9,13}$/.test(
        phone.replace(/[^\d+]/g, '')
    );
}

/* =============================================
   BUTTON LOADING
============================================= */

function setLoading(btn, loading, originalHTML) {

    if (loading) {

        btn.dataset.originalHtml = btn.innerHTML;

        btn.innerHTML = `
            <div class="spinner"></div>
            <span>Memproses...</span>
        `;

        btn.classList.add('loading');

    } else {

        btn.innerHTML =
            originalHTML || btn.dataset.originalHtml;

        btn.classList.remove('loading');
    }
}

/* =============================================
   LOGIN
============================================= */

function handleLogin() {

    clearAllErrors();
    hideAllAlerts();

    const email = document
        .getElementById('loginEmail')
        .value
        .trim();

    const password = document
        .getElementById('loginPassword')
        .value;

    let isValid = true;

    /* VALIDASI EMAIL */

    if (!email) {

        setError(
            'loginEmailError',
            'Email tidak boleh kosong'
        );

        isValid = false;

    } else if (!isValidEmail(email)) {

        setError(
            'loginEmailError',
            'Format email tidak valid'
        );

        isValid = false;
    }

    /* VALIDASI PASSWORD */

    if (!password) {

        setError(
            'loginPasswordError',
            'Password tidak boleh kosong'
        );

        isValid = false;

    } else if (password.length < 6) {

        setError(
            'loginPasswordError',
            'Password minimal 6 karakter'
        );

        isValid = false;
    }

    if (!isValid) return;

    const btn = document.querySelector(
        '#loginForm .btn-primary'
    );

    setLoading(btn, true);

    showAlert(
        'loginAlert',
        'loginAlertText',
        'Memverifikasi akun...',
        'info'
    );

    /* SUBMIT FORM KE PHP */

    setTimeout(() => {

        document
            .getElementById('loginFormElement')
            .submit();

    }, 800);
}

/* =============================================
   REGISTER
============================================= */

function handleRegister() {

    clearAllErrors();
    hideAllAlerts();

    const name = document
        .getElementById('registerName')
        .value
        .trim();

    const email = document
        .getElementById('registerEmail')
        .value
        .trim();

    const phone = document
        .getElementById('registerPhone')
        .value
        .trim();

    const classValue = document
        .getElementById('registerClass')
        .value;

    const password = document
        .getElementById('registerPassword')
        .value;

    const passwordConfirm = document
        .getElementById('registerPasswordConfirm')
        .value;

    const termsAgree = document
        .getElementById('termsAgree')
        .checked;

    let isValid = true;

    /* VALIDASI NAMA */

    if (!name) {

        setError(
            'registerNameError',
            'Nama tidak boleh kosong'
        );

        isValid = false;

    } else if (name.length < 3) {

        setError(
            'registerNameError',
            'Nama minimal 3 karakter'
        );

        isValid = false;
    }

    /* VALIDASI EMAIL */

    if (!email) {

        setError(
            'registerEmailError',
            'Email tidak boleh kosong'
        );

        isValid = false;

    } else if (!isValidEmail(email)) {

        setError(
            'registerEmailError',
            'Format email tidak valid'
        );

        isValid = false;
    }

    /* VALIDASI PHONE */

    if (!phone) {

        setError(
            'registerPhoneError',
            'Nomor telepon wajib diisi'
        );

        isValid = false;

    } else if (!isValidPhone(phone)) {

        setError(
            'registerPhoneError',
            'Format nomor telepon tidak valid'
        );

        isValid = false;
    }

    /* VALIDASI KELAS */

    if (!classValue) {

        setError(
            'registerClassError',
            'Kelas wajib dipilih'
        );

        isValid = false;
    }

    /* VALIDASI PASSWORD */

    if (!password) {

        setError(
            'registerPasswordError',
            'Password tidak boleh kosong'
        );

        isValid = false;

    } else if (password.length < 6) {

        setError(
            'registerPasswordError',
            'Password minimal 6 karakter'
        );

        isValid = false;
    }

    /* VALIDASI KONFIRMASI */

    if (password !== passwordConfirm) {

        setError(
            'registerPasswordConfirmError',
            'Konfirmasi password tidak cocok'
        );

        isValid = false;
    }

    /* VALIDASI TERMS */

    if (!termsAgree) {

        setError(
            'termsError',
            'Anda harus menyetujui syarat & ketentuan'
        );

        isValid = false;
    }

    if (!isValid) return;

    const btn = document.querySelector(
        '#registerForm .btn-primary'
    );

    setLoading(btn, true);

    showAlert(
        'registerAlert',
        'registerAlertText',
        'Membuat akun...',
        'info'
    );

    /* SUBMIT FORM KE PHP */

    setTimeout(() => {

        document
            .getElementById('registerFormElement')
            .submit();

    }, 800);
}

/* =============================================
   ENTER KEY SUPPORT
============================================= */

document.addEventListener('DOMContentLoaded', () => {

    const loginPassword = document.getElementById('loginPassword');

    if (loginPassword) {

        loginPassword.addEventListener('keydown', e => {

            if (e.key === 'Enter') {

                handleLogin();
            }
        });
    }

    const registerConfirm = document.getElementById('registerPasswordConfirm');

    if (registerConfirm) {

        registerConfirm.addEventListener('keydown', e => {

            if (e.key === 'Enter') {

                handleRegister();
            }
        });
    }
});