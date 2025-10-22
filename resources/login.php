<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Farmer - Login</title>

    <!-- Custom fonts for this template-->
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        /* Modern styling */
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #2f855a 0%, #38a169 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: none;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #2f855a 0%, #38a169 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .login-body {
            padding: 2.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #2f855a;
            box-shadow: 0 0 0 3px rgba(47, 133, 90, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, #2f855a 0%, #38a169 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(47, 133, 90, 0.4);
        }

        .btn-login:disabled {
            background: #cbd5e0;
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-login:disabled:hover {
            transform: none;
        }

        .spinner {
            display: none;
            margin-right: 8px;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-bottom: 1rem;
        }

        .alert-danger {
            background-color: #fed7d7;
            color: #c53030;
            border-left: 4px solid #e53e3e;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #276749;
            border-left: 4px solid #38a169;
        }

        .alert-info {
            background-color: #bee3f8;
            color: #2c5aa0;
            border-left: 4px solid #3182ce;
        }

        .alert-warning {
            background-color: #feebc8;
            color: #744210;
            border-left: 4px solid #ed8936;
        }

        .form-check-input:checked {
            background-color: #2f855a;
            border-color: #2f855a;
        }

        .form-check-input:focus {
            border-color: #2f855a;
            box-shadow: 0 0 0 0.2rem rgba(47, 133, 90, 0.25);
        }

        .links-section {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .links-section a {
            color: #2f855a;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .links-section a:hover {
            color: #276749;
            text-decoration: underline;
        }

        .welcome-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: white;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            color: #4a5568;
        }

        .feature-list li i {
            color: #2f855a;
            margin-right: 0.5rem;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #718096;
            cursor: pointer;
        }

        .password-container {
            position: relative;
        }

        /* Animation for alerts */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            animation: slideIn 0.3s ease-out;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .login-header {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12">
                    <div class="card login-card">
                        <div class="row no-gutters">
                            <!-- Left Side - Login Form -->
                            <div class="col-lg-6">
                                <div class="login-body">
                                    <div class="text-center mb-4">
                                        <h3 class="font-weight-bold text-gray-900">Welcome Back!</h3>
                                        <p class="text-muted">Sign in to your farmer account</p>
                                    </div>

                                    <form id="loginForm">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   placeholder="Enter your email" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="password-container">
                                                <input type="password" class="form-control" id="password" 
                                                       name="password" placeholder="Enter your password" required>
                                                <button type="button" class="password-toggle" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="rememberMe" 
                                                       name="rememberMe" value="1">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                            <a href="forgot-password.html" class="text-sm text-primary">Forgot Password?</a>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-login" id="loginBtn">
                                            <span class="spinner" id="spinner">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                            <span id="loginText">Sign In</span>
                                        </button>
                                        
                                        <!-- Alert Messages -->
                                        <div id="loginError" class="alert alert-danger mt-3" style="display: none;"></div>
                                        <div id="loginSuccess" class="alert alert-success mt-3" style="display: none;"></div>
                                        <div id="loginWarning" class="alert alert-warning mt-3" style="display: none;"></div>
                                        <div id="loginInfo" class="alert alert-info mt-3" style="display: none;"></div>
                                        
                                        <!-- Resend Activation Section -->
                                        <div id="resend-activation" class="mt-3" style="display: none;">
                                            <div class="alert alert-info">
                                                <p class="mb-2">Didn't receive the activation email?</p>
                                                <a href="#" id="resend-link" class="font-weight-bold">Resend activation link</a>
                                            </div>
                                            <input type="hidden" id="resend-email">
                                            <div id="resend-message"></div>
                                        </div>
                                    </form>

                                    <div class="links-section">
                                        <p class="mb-2">Don't have an account? 
                                            <a href="register.php" class="font-weight-bold">Create one here</a>
                                        </p>
                                        <p class="mb-0 small text-muted">
                                            By signing in, you agree to our Terms of Service and Privacy Policy
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side - Welcome Message -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="login-header h-100 d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        <div class="welcome-icon">
                                            <i class="fas fa-tractor"></i>
                                        </div>
                                        <h2>FarmerBuyerCon</h2>
                                        <p class="mb-4">Your Gateway to Agricultural Success</p>
                                        
                                        <ul class="feature-list text-left">
                                            <li><i class="fas fa-check-circle"></i> Manage your farm profile</li>
                                            <li><i class="fas fa-check-circle"></i> Connect with buyers</li>
                                            <li><i class="fas fa-check-circle"></i> Track your produce</li>
                                            <li><i class="fas fa-check-circle"></i> Access market insights</li>
                                        </ul>
                                        
                                        <div class="mt-4">
                                            <small class="opacity-75">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Your data is securely protected
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="asset/vendor/jquery/jquery.min.js"></script>
    <script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="asset/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="asset/js/sb-admin-2.min.js"></script>
  
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form submission handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('loginBtn');
            const spinner = document.getElementById('spinner');
            const loginText = document.getElementById('loginText');
            const resendDiv = document.getElementById('resend-activation');
            const resendMessage = document.getElementById('resend-message');
            const loginError = document.getElementById('loginError');
            const loginSuccess = document.getElementById('loginSuccess');
            const loginWarning = document.getElementById('loginWarning');
            const loginInfo = document.getElementById('loginInfo');

            // Reset messages
            resendDiv.style.display = 'none';
            resendMessage.innerHTML = '';
            loginError.style.display = 'none';
            loginSuccess.style.display = 'none';
            loginWarning.style.display = 'none';
            loginInfo.style.display = 'none';

            // Show loading state
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            loginText.textContent = 'Signing In...';

            fetch('views/login.view.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                submitBtn.disabled = false;
                spinner.style.display = 'none';
                loginText.textContent = 'Sign In';

                if (data.status === 'success') {
                    // Show success message
                    loginSuccess.textContent = data.message || 'Login successful! Redirecting...';
                    loginSuccess.style.display = 'block';

                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = data.redirect || 'farmersDashboard.php';
                    }, 1000);

                } else if (data.status === 'unverified') {
                    resendDiv.style.display = 'block';
                    document.getElementById('resend-email').value = data.email;
                    loginError.textContent = data.message;
                    loginError.style.display = 'block';
                } else if (data.status === 'rejected') {
                    loginError.innerHTML = `<strong>Account Disabled</strong><br>${data.message}`;
                    loginError.style.display = 'block';
                } else if (data.status === 'pending') {
                    loginWarning.innerHTML = `<strong>Pending Approval</strong><br>${data.message}`;
                    loginWarning.style.display = 'block';
                } else {
                    loginError.textContent = data.message;
                    loginError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loginError.textContent = 'An error occurred. Please try again.';
                loginError.style.display = 'block';
                submitBtn.disabled = false;
                spinner.style.display = 'none';
                loginText.textContent = 'Sign In';
            });
        });

        // Handle resend activation email
        document.getElementById('resend-link').addEventListener('click', function(e) {
            e.preventDefault();

            const email = document.getElementById('resend-email').value;
            const resendMessage = document.getElementById('resend-message');
            const resendLink = this;

            // Show loading state on resend link
            const originalText = resendLink.textContent;
            resendLink.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
            resendLink.style.pointerEvents = 'none';

            fetch('views/login.view.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `resend_activation=1&email=${encodeURIComponent(email)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resendMessage.innerHTML = '<div class="alert alert-success mt-2">New activation link sent! Check your email.</div>';
                } else {
                    resendMessage.innerHTML = `<div class="alert alert-danger mt-2">${data.message}</div>`;
                }
            })
            .catch(error => {
                resendMessage.innerHTML = '<div class="alert alert-danger mt-2">Failed to send activation email. Please try again.</div>';
            })
            .finally(() => {
                // Reset resend link
                resendLink.textContent = originalText;
                resendLink.style.pointerEvents = 'auto';
            });
        });

        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Enter key support
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const focused = document.activeElement;
                if (focused && (focused.type !== 'submit' && focused.type !== 'button')) {
                    e.preventDefault();
                    document.getElementById('loginForm').dispatchEvent(new Event('submit'));
                }
            }
        });
    </script>
</body>
</html>