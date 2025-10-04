// Load dashboard statistics
async function loadDashboardStats() {
    try {
        const response = await fetch('api/dashboard-stats.php');
        const result = await response.json();
        
        if (result.success) {
            updateCounter('delivery-count', result.data.delivery_notes);
            updateCounter('sites-count', result.data.sites);
            // updateCounter('trucks-count', result.data.trucks);
            updateCounter('items-count', result.data.items);
        } else {
            console.error('Error loading stats:', result.error);
            updateCounter('delivery-count', 'Error');
            updateCounter('sites-count', 'Error');
            // updateCounter('trucks-count', 'Error');
            updateCounter('items-count', 'Error');
        }
    } catch (error) {
        console.error('Error:', error);
        updateCounter('delivery-count', 'Error');
        updateCounter('sites-count', 'Error');
        // updateCounter('trucks-count', 'Error');
        updateCounter('items-count', 'Error');
    }
}

function updateCounter(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.remove('loading');
        element.textContent = typeof value === 'number' ? value.toLocaleString() : value;
    }
}


// Load stats when page loads
document.addEventListener('DOMContentLoaded', loadDashboardStats);