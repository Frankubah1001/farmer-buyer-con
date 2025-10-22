<?php
require_once 'auth_check.php';
include 'header.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <h3>Change Password</h3>

            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Update Your Password</h6>
                        </div>
                        <div class="card-body">
                            <div id="alertMessage" class="alert d-none" role="alert"></div>
                            <form id="passwordChangeForm"> <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control rounded-md" id="currentPassword" name="current_password" required autocomplete="current-password">
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control rounded-md" id="newPassword" name="new_password" required autocomplete="new-password">
                                    <div id="passwordHelpBlock" class="form-text">
                                        Your new password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control rounded-md" id="confirmNewPassword" name="confirm_new_password" required autocomplete="new-password">
                                </div>
                                <button type="submit" class="btn btn-success rounded-md w-full">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('passwordChangeForm');
    const alertMessage = document.getElementById('alertMessage');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Clear previous alerts
        alertMessage.classList.add('d-none');
        alertMessage.textContent = '';
        alertMessage.classList.remove('alert-success', 'alert-danger');

        const formData = new FormData(form);

        fetch('views/change_passwd.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                // Handle HTTP errors (e.g., 500 Internal Server Error)
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            console.log('Server Response:', data); // Log the full response for debugging

            if (data.status === 'success') {
                alertMessage.classList.remove('d-none');
                alertMessage.classList.add('alert-success');
                alertMessage.textContent = data.message;
                form.reset(); // Clear form fields on success
            } else {
                alertMessage.classList.remove('d-none');
                alertMessage.classList.add('alert-danger');
                alertMessage.textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alertMessage.classList.remove('d-none');
            alertMessage.classList.add('alert-danger');
            alertMessage.textContent = 'An error occurred. Please try again later.';
        });
    });
});
</script>
