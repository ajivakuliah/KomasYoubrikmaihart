/* =============================================
   PREDIKSIKARIR — AUTH PAGE LOGIC
   app.js
   ============================================= */

'use strict';

/* ---------- PANEL SWITCHING ---------- */

function switchPanel(target) {
    const loginPanel    = document.getElementById('loginForm');
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

/* ---------- PASSWORD TOGGLE ---------- */

function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
}

/* ---------- ERROR & ALERT HELPERS ---------- */

function setError(id, message) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = message;

    // Mark the parent input as errored
    const group = el.closest('.field-group');
    if (group) {
        const input = group.querySelector('.field-input');
        if (input) input.classList.toggle('input-error', !!message);
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
    document.querySelectorAll('.alert').forEach(el => el.classList.add('hidden'));
}

function showAlert(id, textId, message, type) {
    const alertEl = document.getElementById(id);
    const textEl  = document.getElementById(textId);
    if (!alertEl || !textEl) return;

    alertEl.className = `alert alert-${type}`;
    textEl.textContent = message;
    alertEl.classList.remove('hidden');
}

/* ---------- VALIDATION HELPERS ---------- */

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^(\+62|0)[0-9]{9,12}$/.test(phone.replace(/[^\d+]/g, ''));
}

/* ---------- BUTTON LOADING STATE ---------- */

function setLoading(btn, loading, originalHTML) {
    if (loading) {
        btn.dataset.originalHtml = btn.innerHTML;
        btn.innerHTML = '<div class="spinner"></div> <span>Memproses...</span>';
        btn.classList.add('loading');
    } else {
        btn.innerHTML = originalHTML || btn.dataset.originalHtml;
        btn.classList.remove('loading');
    }
}

/* ---------- LOCAL STORAGE HELPERS ---------- */

function getUsers() {
    try {
        return JSON.parse(localStorage.getItem('users') || '[]');
    } catch {
        return [];
    }
}

function saveUsers(users) {
    localStorage.setItem('users', JSON.stringify(users));
}

function setCurrentUser(user) {
    localStorage.setItem('currentUser', JSON.stringify(user));
}

/* ---------- LOGIN ---------- */

function handleLogin() {
    clearAllErrors();
    hideAllAlerts();

    const email    = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    let   isValid  = true;

    if (!email) {
        setError('loginEmailError', 'Email tidak boleh kosong');
        isValid = false;
    } else if (!isValidEmail(email)) {
        setError('loginEmailError', 'Format email tidak valid');
        isValid = false;
    }

    if (!password) {
        setError('loginPasswordError', 'Password tidak boleh kosong');
        isValid = false;
    } else if (password.length < 6) {
        setError('loginPasswordError', 'Password minimal 6 karakter');
        isValid = false;
    }

    if (!isValid) return;

    const btn = document.querySelector('#loginForm .btn-primary');
    setLoading(btn, true);
    showAlert('loginAlert', 'loginAlertText', 'Memverifikasi akun...', 'info');

    setTimeout(() => {
        const users = getUsers();
        const user  = users.find(u => u.email === email && u.password === password);

        if (user) {
            showAlert('loginAlert', 'loginAlertText', 'Login berhasil! Mengalihkan...', 'success');
            setCurrentUser(user);
            setTimeout(() => { window.location.href = 'index.html'; }, 1200);
        } else {
            setLoading(btn, false);
            showAlert('loginAlert', 'loginAlertText', 'Email atau password tidak sesuai.', 'danger');
        }
    }, 1400);
}

/* ---------- REGISTER ---------- */

function handleRegister() {
    clearAllErrors();
    hideAllAlerts();

    const name            = document.getElementById('registerName').value.trim();
    const email           = document.getElementById('registerEmail').value.trim();
    const phone           = document.getElementById('registerPhone').value.trim();
    const classValue      = document.getElementById('registerClass').value;
    const password        = document.getElementById('registerPassword').value;
    const passwordConfirm = document.getElementById('registerPasswordConfirm').value;
    const termsAgree      = document.getElementById('termsAgree').checked;
    let   isValid         = true;

    if (!name) {
        setError('registerNameError', 'Nama tidak boleh kosong');
        isValid = false;
    } else if (name.length < 3) {
        setError('registerNameError', 'Nama minimal 3 karakter');
        isValid = false;
    }

    if (!email) {
        setError('registerEmailError', 'Email tidak boleh kosong');
        isValid = false;
    } else if (!isValidEmail(email)) {
        setError('registerEmailError', 'Format email tidak valid');
        isValid = false;
    } else {
        const users = getUsers();
        if (users.find(u => u.email === email)) {
            setError('registerEmailError', 'Email ini sudah terdaftar');
            isValid = false;
        }
    }

    if (!phone) {
        setError('registerPhoneError', 'Nomor telepon tidak boleh kosong');
        isValid = false;
    } else if (!isValidPhone(phone)) {
        setError('registerPhoneError', 'Format nomor telepon tidak valid (08XXXXXXXXXX)');
        isValid = false;
    }

    if (!classValue) {
        setError('registerClassError', 'Kelas tidak boleh kosong');
        isValid = false;
    }

    if (!password) {
        setError('registerPasswordError', 'Password tidak boleh kosong');
        isValid = false;
    } else if (password.length < 6) {
        setError('registerPasswordError', 'Password minimal 6 karakter');
        isValid = false;
    }

    if (password && password !== passwordConfirm) {
        setError('registerPasswordConfirmError', 'Password tidak cocok');
        isValid = false;
    }

    if (!termsAgree) {
        setError('termsError', 'Anda harus menyetujui syarat dan ketentuan');
        isValid = false;
    }

    if (!isValid) return;

    const btn = document.querySelector('#registerForm .btn-primary');
    setLoading(btn, true);
    showAlert('registerAlert', 'registerAlertText', 'Membuat akun Anda...', 'info');

    setTimeout(() => {
        const newUser = {
            id:        Date.now(),
            name,
            email,
            phone,
            class:     classValue,
            password,
            createdAt: new Date().toISOString()
        };

        const users = getUsers();
        users.push(newUser);
        saveUsers(users);
        setCurrentUser(newUser);

        showAlert('registerAlert', 'registerAlertText', 'Akun berhasil dibuat! Mengalihkan...', 'success');
        setTimeout(() => { window.location.href = 'index.html'; }, 1200);
    }, 1400);
}

/* ---------- INIT ---------- */

document.addEventListener('DOMContentLoaded', () => {
    // Ensure users array exists
    if (!localStorage.getItem('users')) {
        saveUsers([]);
    }

    // Allow Enter key to submit login
    document.getElementById('loginPassword').addEventListener('keydown', e => {
        if (e.key === 'Enter') handleLogin();
    });

    // Allow Enter key to submit register
    document.getElementById('registerPasswordConfirm').addEventListener('keydown', e => {
        if (e.key === 'Enter') handleRegister();
    });
});
