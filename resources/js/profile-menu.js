document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-profile-menu]').forEach((menu) => {
        const trigger = menu.querySelector('[data-profile-trigger]');
        const dropdown = menu.querySelector('[data-profile-dropdown]');

        if (!trigger || !dropdown) return;

        const close = () => {
            dropdown.classList.add('hidden');
        };

        const open = () => {
            dropdown.classList.remove('hidden');
        };

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !dropdown.classList.contains('hidden');
            if (isOpen) {
                close();
            } else {
                open();
            }
        });

        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target)) {
                close();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                close();
            }
        });
    });
});


