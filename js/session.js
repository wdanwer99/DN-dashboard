
const SESSION_TIMEOUT = 5 * 60 * 1000; // 5 minutes in milliseconds

function initSession() {
    if (!sessionStorage.getItem('userRole')) {
        window.location.href = 'index.html';
        return;
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
    sessionStorage.clear();
    alert('Session expired. Please login again.');
    window.location.href = 'index.html';
}

// Initialize session on page load
document.addEventListener('DOMContentLoaded', initSession);