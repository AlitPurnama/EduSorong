document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        const targetId = button.getAttribute('data-toggle-password');
        const input = document.getElementById(targetId);

        if (!input) return;

        button.addEventListener('click', () => {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            const eyeOpen = button.querySelector('[data-eye-open]');
            const eyeClosed = button.querySelector('[data-eye-closed]');

            if (eyeOpen && eyeClosed) {
                if (isPassword) {
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            }
        });
    });
});


