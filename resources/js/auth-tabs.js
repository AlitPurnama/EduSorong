document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('auth-tabs-root');
    if (!root) return;

    const initialMode = root.getAttribute('data-initial-mode') || 'login';
    const tabs = Array.from(root.querySelectorAll('[data-auth-tab]'));
    const panels = Array.from(root.querySelectorAll('[data-auth-panel]'));

    const activeClasses = ['text-[#2E3242]', 'font-semibold', 'border-[#2E3242]'];
    const inactiveClasses = ['border-transparent', 'text-[#B5B7C0]'];

    const setMode = (mode) => {
        // Toggle panels
        panels.forEach((panel) => {
            if (panel.getAttribute('data-auth-panel') === mode) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        });

        // Toggle tabs
        tabs.forEach((tab) => {
            const isActive = tab.getAttribute('data-auth-tab') === mode;
            activeClasses.forEach((cls) => tab.classList.toggle(cls, isActive));
            inactiveClasses.forEach((cls) => tab.classList.toggle(cls, !isActive));
        });

        // Update URL query without reload
        const url = new URL(window.location.href);
        url.searchParams.set('mode', mode);
        window.history.replaceState({}, '', url.toString());
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const mode = tab.getAttribute('data-auth-tab');
            if (mode) setMode(mode);
        });
    });

    setMode(initialMode);
});


