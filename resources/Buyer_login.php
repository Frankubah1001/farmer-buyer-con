<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Buyer - Login</title>

    <!-- Custom fonts for this template-->
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        /* Alert messages */
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Resend link */
        #resend-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        #resend-link:hover {
            text-decoration: underline;
        }

        #loginBtn {
            background-color: #2f855a;
            color: white;
            padding: 10px 16px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        #loginBtn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form id="loginForm">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe" value="1">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary" id="loginBtn">Login</button>
                                        <span id="spinner" style="display: none; margin-right: 8px;">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                        
                                        <div id="loginError" class="alert alert-danger mt-3" style="display: none;"></div>
                                        
                                        <div id="resend-activation" class="mt-3" style="display: none;">
                                            <p>Didn't receive the activation email? <a href="Buyer_login.php" id="resend-link">Resend activation link</a></p>
                                            <input type="hidden" id="resend-email">
                                            <div id="resend-message"></div>
                                        </div>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="Buyer_register.php">Create an Account!</a>
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
    <!-- Core plugin JavaScript-->
    <script src="asset/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="asset/vendor/jquery/jquery.min.js"></script>
    <script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="asset/js/sb-admin-2.min.js"></script>
  
    <script>
    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('loginBtn').disabled = true;
        document.getElementById('spinner').style.display = 'inline-block';
    });
</script>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = document.getElementById('loginBtn');
        const resendDiv = document.getElementById('resend-activation');
        const resendMessage = document.getElementById('resend-message');

        // Reset messages
        resendDiv.style.display = 'none';
        resendMessage.innerHTML = '';

        fetch('views/buyer_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // **Modified Part: Always redirect to dashboard on success**
                window.location.href = 'buyersDashboard.php';
            }
            else if (data.status === 'unverified') {
                // Show resend activation option
                resendDiv.style.display = 'block';
                document.getElementById('resend-email').value = data.email;

                // Display error message
                document.getElementById('loginError').textContent = data.message;
                document.getElementById('loginError').style.display = 'block';
                document.getElementById('loginBtn').disabled = false;
                document.getElementById('spinner').style.display = 'none';
            }
            else {
                // Show other errors
                document.getElementById('loginError').textContent = data.message;
                document.getElementById('loginError').style.display = 'block';
                document.getElementById('loginBtn').disabled = false;
                document.getElementById('spinner').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loginError').textContent = 'An error occurred. Please try again.';
            document.getElementById('loginError').style.display = 'block';
            document.getElementById('loginBtn').disabled = false;
            document.getElementById('spinner').style.display = 'none';
        });
    });

    // Handle resend activation email
    document.getElementById('resend-link').addEventListener('click', function(e) {
        e.preventDefault();

        const email = document.getElementById('resend-email').value;
        const resendMessage = document.getElementById('resend-message');

        fetch('views/buyer_login.php', {
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
            resendMessage.innerHTML = '<div class="alert alert-danger mt-2">Failed to send activation email.</div>';
        });
    });
</script>
</body>
</html>