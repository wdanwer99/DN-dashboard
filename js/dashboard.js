// Load dashboard statistics
async function loadDashboardStats() {
    try {
        console.log('Loading dashboard stats...');
        const response = await fetch('api/dashboard-stats.php');
        const result = await response.json();
        
        console.log('Dashboard stats response:', result);
        
        if (result.success) {
            updateCounter('delivery-count', result.data.delivery_notes);
            updateCounter('sites-count', result.data.sites);
            updateCounter('items-count', result.data.items);
            updateCounter('projects-count', result.data.projects);
            updateCounter('batches-count', result.data.batches);
            console.log('Dashboard stats loaded successfully');
        } else {
            console.error('Error loading stats:', result.error);
            updateCounter('delivery-count', 'Error');
            updateCounter('sites-count', 'Error');
            updateCounter('items-count', 'Error');
            updateCounter('projects-count', 'Error');
            updateCounter('batches-count', 'Error');
        }
    } catch (error) {
        console.error('Error fetching stats:', error);
        updateCounter('delivery-count', 'Error');
        updateCounter('sites-count', 'Error');
        updateCounter('items-count', 'Error');
        updateCounter('projects-count', 'Error');
        updateCounter('batches-count', 'Error');
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