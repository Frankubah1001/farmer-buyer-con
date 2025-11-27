// js/dashboard_data.js

function updateDashboardCards(data) {
    const totalProductsElement = document.getElementById('total-products');
    const frequentProduceElement = document.getElementById('frequent-produce');
    const totalorderElement = document.getElementById('total_order');

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
        row.insertCell().textContent = transaction.quantity + ' Units';
        row.insertCell().textContent = '₦' + parseFloat(transaction.amount).toLocaleString();
        row.insertCell().textContent = transaction.order_date;
    });
}

function populateWithdrawalHistory(withdrawals) {
    const tbody = document.getElementById('withdrawalHistoryBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (withdrawals.length === 0) {
        const row = tbody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 5;
        cell.textContent = 'No withdrawal history found.';
        cell.className = 'text-center';
        return;
    }

    withdrawals.forEach(withdrawal => {
        const row = tbody.insertRow();
        row.insertCell().textContent = withdrawal.date || 'N/A';
        row.insertCell().textContent = '₦' + parseFloat(withdrawal.amount).toLocaleString();
        row.insertCell().textContent = withdrawal.bank_name || 'N/A';

        const statusCell = row.insertCell();
        let statusBadge = '';
        if (withdrawal.status === 'Approved') {
            statusBadge = '<span class="badge badge-success">Approved</span>';
        } else if (withdrawal.status === 'Pending') {
            statusBadge = '<span class="badge badge-warning">Pending</span>';
        } else if (withdrawal.status === 'Rejected') {
            statusBadge = '<span class="badge badge-danger">Rejected</span>';
        }
        statusCell.innerHTML = statusBadge;

        const actionCell = row.insertCell();
        actionCell.innerHTML = `<button class="btn btn-sm btn-info" onclick="viewWithdrawalDetails(${withdrawal.withdrawal_id})">View</button>`;
    });
}

function updateWalletBalance(data) {
    const walletElement = document.getElementById('walletBalance');
    const modalBalanceElement = document.getElementById('modalAvailableBalance');
    const modalBalanceElement2 = document.getElementById('modalAvailableBalance2');

    const balance = data.wallet_balance || 0;
    const formattedBalance = '₦' + parseFloat(balance).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    if (walletElement) {
        walletElement.textContent = formattedBalance;
    }
    if (modalBalanceElement) {
        modalBalanceElement.textContent = formattedBalance;
    }
    if (modalBalanceElement2) {
        modalBalanceElement2.textContent = formattedBalance;
    }

    // Update deduction information in modal
    const totalEarningsEl = document.getElementById('modalTotalEarnings');
    const platformFeeEl = document.getElementById('modalPlatformFee');
    const adminFeeEl = document.getElementById('modalAdminFee');
    const totalDeductionsEl = document.getElementById('modalTotalDeductions');
    const netEarningsEl = document.getElementById('modalNetEarnings');
    const withdrawnEl = document.getElementById('modalWithdrawn');

    if (totalEarningsEl) {
        totalEarningsEl.textContent = '₦' + parseFloat(data.total_earnings_base || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (platformFeeEl) {
        platformFeeEl.textContent = parseFloat(data.platform_fee_amount || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (adminFeeEl) {
        adminFeeEl.textContent = parseFloat(data.admin_fee_amount || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (totalDeductionsEl) {
        totalDeductionsEl.textContent = parseFloat(data.total_deductions || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (netEarningsEl) {
        netEarningsEl.textContent = '₦' + parseFloat(data.net_earnings || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (withdrawnEl) {
        withdrawnEl.textContent = parseFloat(data.total_withdrawn || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}

function fetchDashboardData() {
    fetch('views/get_dashboard_info.php')
        .then(response => response.json())
        .then(data => {
            console.log("Dashboard Data:", data);
            updateDashboardCards(data);
            populateRecentTransactions(data.recent_transactions || []);
            updateWalletBalance(data);
            populateWithdrawalHistory(data.withdrawal_history || []);
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
}

// View withdrawal details
function viewWithdrawalDetails(withdrawalId) {
    fetch('views/get_withdrawal_details.php?id=' + withdrawalId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const details = data.withdrawal;
                const detailsHtml = `
                    <table class="table table-bordered">
                        <tr><th>Withdrawal ID</th><td>${details.withdrawal_id}</td></tr>
                        <tr><th>Amount</th><td>₦${parseFloat(details.amount).toLocaleString()}</td></tr>
                        <tr><th>Bank Name</th><td>${details.bank_name}</td></tr>
                        <tr><th>Account Number</th><td>${details.account_number}</td></tr>
                        <tr><th>Account Name</th><td>${details.account_name}</td></tr>
                        <tr><th>Status</th><td><span class="badge badge-${details.status === 'Approved' ? 'success' : details.status === 'Pending' ? 'warning' : 'danger'}">${details.status}</span></td></tr>
                        <tr><th>Request Date</th><td>${details.request_date}</td></tr>
                    </table>
                `;
                document.getElementById('withdrawalDetailBody').innerHTML = detailsHtml;
                $('#withdrawalDetailModal').modal('show');
            } else {
                alert('Error loading withdrawal details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching withdrawal details');
        });
}

// Handle withdrawal form submission
document.addEventListener('DOMContentLoaded', function () {
    fetchDashboardData();

    const withdrawalForm = document.getElementById('withdrawalForm');
    if (withdrawalForm) {
        withdrawalForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(withdrawalForm);
            const messageDiv = document.getElementById('withdrawalMessage');

            fetch('views/process_withdrawal.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                        withdrawalForm.reset();
                        setTimeout(() => {
                            $('#withdrawalModal').modal('hide');
                            messageDiv.innerHTML = '';
                            fetchDashboardData();
                        }, 2000);
                    } else {
                        messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                });
        });
    }
});

// Optional: Refresh data every 5 minutes
setInterval(fetchDashboardData, 300000);