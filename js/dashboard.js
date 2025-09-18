// Load dashboard data from database
async function loadDashboardData() {
    try {
        const [dashboardResponse, countResponse] = await Promise.all([
            fetch('api/dashboard-data.php'),
            fetch('api/delivery-count.php')
        ]);
        
        const dashboardData = await dashboardResponse.json();
        const countData = await countResponse.json();
        
        if (dashboardData.success) {
            updateDashboardStats(dashboardData.stats, countData.count);
            updateRecentActivities(dashboardData.recent_activities);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function updateDashboardStats(stats, deliveryCount) {
    document.getElementById('delivery-count').textContent = deliveryCount || 0;
    document.querySelector('.box2 .number').textContent = stats.active_delivery_notes || 0;
    document.querySelector('.box3 .number').textContent = stats.total_trucks || 0;
}

function updateRecentActivities(activities) {
    const container = document.querySelector('.activityTable > div:last-child');
    let html = '<table class="activity"><thead><tr><th>DN No</th><th>Customer</th><th>Status</th><th>Date</th></tr></thead><tbody>';
    
    activities.forEach((activity, index) => {
        const rowClass = index % 2 === 1 ? 'odd' : '';
        html += `<tr class="${rowClass}">
            <td>${activity.dn_no}</td>
            <td>${activity.Customer}</td>
            <td>${activity.DN_Status}</td>
            <td>${new Date(activity.created_at).toLocaleDateString()}</td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', loadDashboardData);