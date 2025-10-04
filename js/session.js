
const SESSION_TIMEOUT = 5 * 60 * 1000; // 5 minutes in milliseconds

function initSession() {
    if (!sessionStorage.getItem('userRole')) {
        window.location.replace('index.html');
        return;
    }
    
    // Prevent back navigation to login page after successful login
    if (window.location.pathname !== '/index.html') {
        history.pushState(null, null, window.location.href);
        window.addEventListener('popstate', function() {
            if (!sessionStorage.getItem('userRole')) {
                window.location.replace('index.html');
            } else {
                history.pushState(null, null, window.location.href);
            }
        });
    }
    
    updateLastActivity();
    startSessionTimer();
    addActivityListeners();
}

function updateLastActivity() {
    sessionStorage.setItem('lastActivity', Date.now().toString());
}

function checkSession() {
    const lastActivity = parseInt(sessionStorage.getItem('lastActivity') || '0');
    const now = Date.now();
    
    if (now - lastActivity > SESSION_TIMEOUT) {
        logout();
    }
}

function startSessionTimer() {
    setInterval(checkSession, 60000); // Check every minute
}

function addActivityListeners() {
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
        document.addEventListener(event, updateLastActivity, true);
    });
}

function logout() {
    // Clear all session data
    sessionStorage.clear();
    localStorage.clear();
    
    // Clear browser history to prevent back navigation
    history.pushState(null, null, 'index.html');
    
    // Redirect to login page
    window.location.replace('index.html');
    
    // Prevent back navigation after logout
    window.addEventListener('popstate', function() {
        history.pushState(null, null, 'index.html');
        window.location.replace('index.html');
    });
}

// Initialize session on page load
document.addEventListener('DOMContentLoaded', initSession);