// js/dashboard_data.js

function fetchDashboardData() {
    fetch('views/get_dashboard_info.php')
        .then(response => response.json())
        .then(data => {
            console.log("Dashboard Data:", data);
            updateDashboardCards(data);
            populateRecentTransactions(data.recent_transactions || []);
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
}

function updateDashboardCards(data) {
    const totalProductsElement = document.getElementById('total-products');
    const frequentProduceElement = document.getElementById('frequent-produce');
    const totalorderElement = document.getElementById('total_order');
    
    // New elements to update
    const yearlyOrdersElement = document.getElementById('numOrdersYr');
    const monthlyOrdersElement = document.getElementById('numOrdersMnth');
    const annualEarningsElement = document.getElementById('totalEarningsYr');
    const monthlyEarningsElement = document.getElementById('totalEarningsMnth');

    if (totalProductsElement) {
        totalProductsElement.textContent = data.total_products;
    }

    if (frequentProduceElement) {
        frequentProduceElement.textContent = data.frequent_produce;
    }
    
    if (totalorderElement) {
        totalorderElement.textContent = data.total_orders;
    }
    
    // Update new cards
    if (yearlyOrdersElement) {
        yearlyOrdersElement.textContent = data.yearly_orders;
    }
    
    if (monthlyOrdersElement) {
        monthlyOrdersElement.textContent = data.monthly_orders;
    }
    
    if (annualEarningsElement) {
        annualEarningsElement.textContent = '₦' + data.annual_earnings.toLocaleString();
    }
    
    if (monthlyEarningsElement) {
        monthlyEarningsElement.textContent = '₦' + data.monthly_earnings.toLocaleString();
    }
}

function populateRecentTransactions(transactions) {
    const tbody = document.getElementById('transactionsBody') || document.querySelector('#dataTable tbody');
    tbody.innerHTML = '';

    if (transactions.length === 0) {
        const row = tbody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 6;
        cell.textContent = 'No recent transactions found.';
        return;
    }

    transactions.forEach(transaction => {
        const row = tbody.insertRow();
        row.insertCell().textContent = transaction.buyer_name || 'N/A';
        row.insertCell().textContent = transaction.buyer_location || 'N/A';
        row.insertCell().textContent = transaction.produce || 'N/A';
        row.insertCell().textContent = transaction.quantity + ' Units'; // Assuming units are Tons
        row.insertCell().textContent = '₦' + parseFloat(transaction.amount).toLocaleString();
        row.insertCell().textContent = transaction.order_date;
    });
}

document.addEventListener('DOMContentLoaded', fetchDashboardData);

// Optional: Refresh data every 5 minutes
setInterval(fetchDashboardData, 300000);

// Check approval status on page load (existing code remains)
$(document).ready(function() {
    checkApprovalStatus();
    
    function checkApprovalStatus() {
        $.ajax({
            url: 'check_approval.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.approved) {
                    // User is approved, show dashboard
                    $('#dashboardContent').show();
                } else {
                    // User not approved or rejected
                    $('#dashboardContent').hide();
                    
                    if (response.rejected) {
                        // Show rejection message
                        $('#approvalMessage').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                ${response.message}
                            </div>
                        `);
                        
                        // Log user out after 5 seconds
                        setTimeout(function() {
                            window.location.href = 'logout.php';
                        }, 5000);
                    } else {
                        // Show pending approval message
                        $('#approvalMessage').html(`
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                ${response.message}
                            </div>
                        `);
                    }
                }
            },
            error: function() {
                $('#approvalMessage').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error checking approval status. Please try again later.
                    </div>
                `);
            }
        });
    }
});