const container = document.querySelector('.auth-container');
const toggleButtons = document.querySelectorAll('[data-auth-toggle]');
const passwordToggleButtons = document.querySelectorAll('[data-password-toggle]');

if (container && toggleButtons.length) {
    let isAnimating = false;

    const setMode = (mode) => {
        if (isAnimating) {
            return;
        }

        const isRegister = mode === 'register';
        const isAlreadyActive = container.classList.contains('active') === isRegister;

        if (isAlreadyActive) {
            return;
        }

        isAnimating = true;
        container.classList.toggle('active', isRegister);

        const url = isRegister ? '/register' : '/login';
        if (window.location.pathname !== url) {
            window.history.replaceState({}, '', url);
        }

        window.setTimeout(() => {
            isAnimating = false;
        }, 850);
    };

    toggleButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            setMode(button.dataset.authToggle);
        });
    });
}

if (passwordToggleButtons.length) {
    passwordToggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const field = button.closest('.password-field');
            const input = field?.querySelector('[data-password-input]');

            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.classList.toggle('is-visible', isHidden);
            button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
            button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
}
