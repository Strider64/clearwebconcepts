document.addEventListener('DOMContentLoaded', function() {
    const cookieBanner = document.getElementById('cookie-banner');
    const acceptCookiesButton = document.getElementById('accept-cookies');
    const rejectCookiesButton = document.getElementById('reject-cookies');

    // Check if the cookie consent has been given
    if (!getCookie('cookieConsent')) {
        cookieBanner.style.display = 'block';
    }

    acceptCookiesButton.addEventListener('click', function() {
        setCookie('cookieConsent', 'accepted', 365);
        enableFunctionality();
        cookieBanner.style.display = 'none';
    });

    rejectCookiesButton.addEventListener('click', function() {
        setCookie('cookieConsent', 'rejected', 365);
        disableFunctionality();
        cookieBanner.style.display = 'none';
    });

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
        console.log(`Cookie set: ${name}=${value}; expires=${expires}`);
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function enableFunctionality() {
        console.log('Cookies accepted, enabling additional functionality.');
        // Add code to enable functionality that requires cookies
    }

    function disableFunctionality() {
        console.log('Cookies rejected, disabling additional functionality.');
        // Add code to disable functionality that requires cookies
    }

    // Debugging
    console.log('Cookie consent status:', getCookie('cookieConsent'));
});
