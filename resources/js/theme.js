const STORAGE_KEY = 'em-theme';

function getTheme() {
    try {
        return localStorage.getItem(STORAGE_KEY) || 'dark';
    } catch {
        return 'dark';
    }
}

function applyTheme(theme) {
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

    try {
        localStorage.setItem(STORAGE_KEY, theme);
    } catch {
        // ignore quota / private mode
    }

    window.dispatchEvent(new CustomEvent('em-theme-changed', { detail: { theme } }));
}

function toggleTheme() {
    applyTheme(getTheme() === 'dark' ? 'light' : 'dark');
}

window.emTheme = { getTheme, applyTheme, toggleTheme };

export { getTheme, applyTheme, toggleTheme };
