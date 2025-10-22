<?php
// login.php - Admin Login Page
session_start();

// Redirect if already logged in
if (isset($_SESSION['cbn_user_id'])) {
    header('Location: dashboard.php'); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBN Admin Login</title>
    <!-- Load Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Use Inter font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0fdf4; /* Light Green background */
        }
        .btn-agri {
            background-color: #16a34a; /* Green 600 */
            transition: background-color 0.3s;
        }
        .btn-agri:hover {
            background-color: #15803d; /* Green 700 */
        }
        .input-focus:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.5);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8 sm:p-10 border border-green-200">
        <div class="text-center mb-8">
            <svg class="mx-auto h-10 w-auto text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <h2 class="mt-4 text-3xl font-extrabold text-gray-900">
                CBN Admin Portal
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to manage farmer data
            </p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username or Email</label>
                <input id="username" name="username" type="text" autocomplete="username" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 input-focus focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 input-focus focus:outline-none">
            </div>

            <div id="loginMessage" class="hidden p-3 text-sm rounded-lg" role="alert"></div>

            <div>
                <button type="submit" id="loginButton"
                        class="btn-agri w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Log In
                </button>
            </div>
        </form>
    </div>

    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const API_URL = 'api/cbn_login.php';
            const $loginForm = $('#loginForm');
            const $loginMessage = $('#loginMessage');
            const $loginButton = $('#loginButton');

            $loginForm.on('submit', function(e) {
                e.preventDefault();
                
                // Disable button and show loading state
                $loginButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Logging in...');
                
                // Clear message area
                $loginMessage.removeClass('alert-success bg-green-100 text-green-800 alert-danger bg-red-100 text-red-800').addClass('hidden').text('');

                const formData = new FormData(this);
                formData.append('action', 'login');

                $.ajax({
                    url: API_URL,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $loginMessage.removeClass('hidden').addClass('alert-success bg-green-100 text-green-800').text('Login successful! Redirecting...');
                            // Redirect after a short delay
                            setTimeout(() => {
                                window.location.href = 'dashboard.php'; 
                            }, 1000);
                        } else {
                            $loginMessage.removeClass('hidden').addClass('alert-danger bg-red-100 text-red-800').text(response.message);
                            $loginButton.prop('disabled', false).text('Log In');
                        }
                    },
                    error: function(xhr, status, error) {
                        $loginMessage.removeClass('hidden').addClass('alert-danger bg-red-100 text-red-800').text('Network error. Please check your connection.');
                        console.error("AJAX Error:", status, error);
                        $loginButton.prop('disabled', false).text('Log In');
                    }
                });
            });
        });
    </script>
</body>
</html>
