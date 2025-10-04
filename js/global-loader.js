// Global Loading and Message System
class GlobalLoader {
    constructor() {
        this.createLoader();
        this.createMessageContainer();
    }

    createLoader() {
        const loader = document.createElement('div');
        loader.className = 'global-loader';
        loader.id = 'globalLoader';
        loader.innerHTML = `
            <div class="loader-content">
                <div class="loader-spinner"></div>
                <p class="loader-text" id="loaderText">Loading...</p>
            </div>
        `;
        document.body.appendChild(loader);
    }

    createMessageContainer() {
        const message = document.createElement('div');
        message.className = 'global-message';
        message.id = 'globalMessage';
        document.body.appendChild(message);
    }

    show(text = 'Loading...') {
        document.getElementById('loaderText').textContent = text;
        document.getElementById('globalLoader').classList.add('show');
    }

    hide() {
        document.getElementById('globalLoader').classList.remove('show');
    }

    showMessage(text, type = 'success', duration = 3000) {
        const messageEl = document.getElementById('globalMessage');
        messageEl.textContent = text;
        messageEl.className = `global-message ${type} show`;
        
        setTimeout(() => {
            messageEl.classList.remove('show');
        }, duration);
    }
}

// Initialize global loader
const globalLoader = new GlobalLoader();

// Export for use in other scripts
window.globalLoader = globalLoader;