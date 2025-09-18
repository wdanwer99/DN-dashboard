// Load analytics data from database
async function loadAnalyticsData() {
    try {
        const response = await fetch('api/analytics-data.php');
        const data = await response.json();
        
        if (data.success) {
            displayAnalytics(data.analytics);
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

function displayAnalytics(analytics) {
    const container = document.querySelector('.EditUserRole > div:last-child');
    
    let html = `
        <div class="analytics-grid">
            <div class="analytics-card">
                <h3>Delivery Status Distribution</h3>
                <table class="analytics-table">
                    <thead><tr><th>Status</th><th>Count</th></tr></thead>
                    <tbody>`;
    
    analytics.status_distribution.forEach(item => {
        html += `<tr><td>${item.DN_Status}</td><td>${item.count}</td></tr>`;
    });
    
    html += `</tbody></table>
            </div>
            
            <div class="analytics-card">
                <h3>Top Customers</h3>
                <table class="analytics-table">
                    <thead><tr><th>Customer</th><th>Deliveries</th></tr></thead>
                    <tbody>`;
    
    analytics.top_customers.forEach(item => {
        html += `<tr><td>${item.Customer}</td><td>${item.delivery_count}</td></tr>`;
    });
    
    html += `</tbody></table>
            </div>
            
            <div class="analytics-card">
                <h3>Truck Status</h3>
                <table class="analytics-table">
                    <thead><tr><th>Status</th><th>Count</th></tr></thead>
                    <tbody>`;
    
    analytics.truck_utilization.forEach(item => {
        html += `<tr><td>${item.status}</td><td>${item.count}</td></tr>`;
    });
    
    html += `</tbody></table>
            </div>
            
            <div class="analytics-card">
                <h3>Monthly Trends</h3>
                <table class="analytics-table">
                    <thead><tr><th>Month</th><th>Deliveries</th></tr></thead>
                    <tbody>`;
    
    analytics.monthly_trends.forEach(item => {
        html += `<tr><td>${item.month}</td><td>${item.count}</td></tr>`;
    });
    
    html += `</tbody></table>
            </div>
            
            <div class="analytics-summary">
                <h3>Key Metrics</h3>
                <p><strong>Average Delivery Time:</strong> ${Math.round(analytics.avg_delivery_time)} days</p>
            </div>
        </div>`;
    
    container.innerHTML = html;
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', loadAnalyticsData);