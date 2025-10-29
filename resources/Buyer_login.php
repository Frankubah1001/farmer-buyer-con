<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buyer - Login</title>

    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1rem;
            line-height: 1.5;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
        }
        .alert-danger { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }

        #loginBtn {
            background:#2f855a; color:#fff; padding:10px 16px; font-size:16px;
            border:none; border-radius:6px; cursor:pointer;
        }
        #loginBtn:disabled { background:#ccc; cursor:not-allowed; }

        #resend-link { color:#007bff; font-weight:500; }
        #resend-link:hover { text-decoration:underline; }
    </style>
</head>
<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center"><h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1></div>

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

                                    <button type="submit" id="loginBtn" class="btn btn-primary">Login</button>
                                    <span id="spinner" style="display:none;margin-left:8px;"><i class="fa fa-spinner fa-spin"></i></span>

                                    <div id="loginError" class="alert alert-danger mt-3" style="display:none;"></div>

                                    <div id="resend-activation" class="mt-3" style="display:none;">
                                        <p>Didn't receive the activation email? <a href="#" id="resend-link">Resend activation link</a></p>
                                        <input type="hidden" id="resend-email">
                                        <div id="resend-message"></div>
                                    </div>
                                </form>

                                <hr>
                                <div class="text-center"><a class="small" href="forgot-password.html">Forgot Password?</a></div>
                                <div class="text-center"><a class="small" href="Buyer_register.php">Create an Account!</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="asset/vendor/jquery/jquery.min.js"></script>
<script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="asset/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="asset/js/sb-admin-2.min.js"></script>

<script>
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('loginBtn');
    const spin = document.getElementById('spinner');
    const errDiv = document.getElementById('loginError');
    const resendDiv = document.getElementById('resend-activation');
    const resendMsg = document.getElementById('resend-message');

    btn.disabled = true; spin.style.display = 'inline-block';
    errDiv.style.display = 'none'; resendDiv.style.display = 'none';
    resendMsg.innerHTML = '';

    fetch('views/buyer_login.php', {method:'POST', body:new FormData(this)})
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'buyersDashboard.php';
        } else if (data.status === 'unverified') {
            errDiv.innerHTML = data.message;
            errDiv.style.display = 'block';
            resendDiv.style.display = 'block';
            document.getElementById('resend-email').value = data.email;
        } else if (data.status === 'disabled') {
            errDiv.innerHTML = data.message;
            errDiv.style.display = 'block';
        } else {
            errDiv.innerHTML = data.message;
            errDiv.style.display = 'block';
        }
    })
    .catch(() => {
        errDiv.innerHTML = 'An unexpected error occurred. Please try again.';
        errDiv.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false; spin.style.display = 'none';
    });
});

document.getElementById('resend-link').addEventListener('click', function (e) {
    e.preventDefault();
    const email = document.getElementById('resend-email').value;
    const msg = document.getElementById('resend-message');

    fetch('views/buyer_login.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`resend_activation=1&email=${encodeURIComponent(email)}`
    })
    .then(r => r.json())
    .then(d => {
        msg.innerHTML = d.status==='success'
            ? '<div class="alert alert-success">New activation link sent! Check your inbox.</div>'
            : `<div class="alert alert-danger">${d.message}</div>`;
    })
    .catch(() => msg.innerHTML = '<div class="alert alert-danger">Failed to send email.</div>');
});
</script>
</body>
</html>