// Check authentication status on every AJAX request
$(document).ajaxComplete(function(event, xhr, settings) {
    try {
        const response = JSON.parse(xhr.responseText);
        if (response && response.auth === false) {
            window.location.href = 'loginAuth.php';
        }
    } catch (e) {
        // Not a JSON response
    }
});

// Periodically check session status
setInterval(function() {
    $.ajax({
        url: 'auth_check.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.auth === false) {
                window.location.href = 'loginAuth.php';
            }
        }
    });
}, 300000); // Check every 5 minutes