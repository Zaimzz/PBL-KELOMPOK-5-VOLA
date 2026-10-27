

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Synchronize client storage state with session cookies
(function syncSessionState() {
    // When user submits any logout form, clear remembered credentials and session storage
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form');
        if (form && form.action && form.action.includes('logout')) {
            localStorage.removeItem('vola_remember');
            localStorage.removeItem('vola_remember_email');
            sessionStorage.removeItem('vola_session_active');
            document.cookie = 'vola_remember=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';
            document.cookie = 'vola_session_active=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';
        }
    });

    const isRemembered = localStorage.getItem('vola_remember') === 'true';

    if (isRemembered) {
        // Persistent cookie for 'Ingat saya'
        document.cookie = 'vola_remember=1; path=/; max-age=157680000; SameSite=Lax';
    } else if (sessionStorage.getItem('vola_session_active') === 'true') {
        // Session cookie for active tab session
        document.cookie = 'vola_session_active=1; path=/; SameSite=Lax';
    } else {
        // Tab or browser was closed and reopened without 'Ingat saya'
        // Clear active cookie so backend middleware knows the session is closed
        document.cookie = 'vola_session_active=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';
    }
})();

Alpine.start();
