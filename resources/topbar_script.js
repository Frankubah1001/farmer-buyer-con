
function fetchOrderCount() {
    fetch('topbar_notification.php') // Path to your PHP script
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.orderCount !== undefined) {
                const orderCountBadge = document.querySelector('#alertsDropdown .badge-counter'); // Correct selector
                if (orderCountBadge) {
                    orderCountBadge.textContent = data.orderCount + '+';
                }
            } else if (data.error) {
                console.error('Error fetching order count:', data.error);
                // Handle error (e.g., redirect, display message)
            }
        })
        .catch(error => {
            console.error('There was a problem fetching the order count:', error);
        });
        
    }

// Call fetchOrderCount when the page loads
document.addEventListener('DOMContentLoaded', fetchOrderCount);

// Optionally, update the count periodically (e.g., every minute)
// setInterval(fetchOrderCount, 60000);
