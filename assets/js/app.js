(() => {
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const label = document.getElementById('themeLabel');
    const icon = document.getElementById('themeIcon');

    function setTheme(theme) {
        root.dataset.theme = theme;
        localStorage.setItem('attendly-theme', theme);
        if (label) label.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
        if (icon) icon.textContent = theme === 'dark' ? '☾' : '☼';
    }
    setTheme(localStorage.getItem('attendly-theme') || 'light');
    toggle?.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));

    const menu = document.getElementById('mobileMenu');
    const sidebar = document.getElementById('sidebar');
    menu?.addEventListener('click', () => sidebar?.classList.toggle('open'));

    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', e => {
            let valid = true;
            form.querySelectorAll('[required]').forEach(input => {
                input.classList.remove('invalid');
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('invalid');
                }
            });
            if (!valid) {
                e.preventDefault();
                form.querySelector('[required].invalid')?.focus();
            }
        });
    });

    setTimeout(() => document.querySelector('.toast')?.remove(), 5000);
})();
