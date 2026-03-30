const container = document.querySelector('.auth-container');
const toggleButtons = document.querySelectorAll('[data-auth-toggle]');

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
