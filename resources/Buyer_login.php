<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buyer – Login | FarmConnect</title>
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* --- Root Variables --- */
        :root{
            --farm-green:#2E7D32;--harvest-gold:#FF8F00;--soil-brown:#5D4037;
            --light-field:#F1F8E9;--wheat:#FFCA28;--sky:#81D4FA;
            --shadow:rgba(46,125,50,.25);--glow:0 0 15px rgba(255,143,0,.5);
            --spacing-sm: 0.8rem;
            --spacing-md: 1.2rem;
            --spacing-lg: 1.6rem;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        
        /* --- Body --- */
        body{
            font-family:'Nunito',sans-serif;
            background:linear-gradient(135deg,#E8F5E9 0%,#C8E6C9 100%);
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:1.5rem 1rem;
            position:relative;
            overflow-y:auto; /* Allows scroll if needed */
        }
        body::before{
            content:'';position:absolute;top:0;left:0;right:0;bottom:0;
            background:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"><path fill="%23C8E6C9" opacity="0.2" d="M0 60 Q30 40,60 60 T120 60 V120 H0 Z"/></svg>') repeat;
            opacity:.15;z-index:-1;
        }

        /* --- Login Card – Height is now 100% content-driven --- */
        .login-card{
            background:#fff;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 20px 50px var(--shadow);
            max-width:420px;
            width:100%;
            transition:transform .4s, box-shadow .4s;
        }
        .login-card:hover{
            transform:translateY(-8px);
            box-shadow:0 30px 70px rgba(46,125,50,.35);
        }

        /* --- Header (Compact) --- */
        .card-header{
            background:linear-gradient(rgba(46,125,50,.85),rgba(46,125,50,.95)),
                        url('https://images.unsplash.com/photo-1500595046743-cd271d694d30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') center/cover;
            padding:1.8rem 1.5rem;
            text-align:center;
            color:#fff;
            position:relative;
        }
        .card-header::after{
            content:'';position:absolute;bottom:0;left:0;right:0;height:60px;
            background:linear-gradient(transparent,#fff);
        }
        .logo{font-size:2.8rem;color:var(--harvest-gold);animation:float 3s ease-in-out infinite;}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        .card-header h2{font-weight:800;font-size:1.7rem;margin-top:.6rem;}
        .card-header p{font-size:.9rem;opacity:.9;max-width:240px;margin:0 auto;}

        /* --- Card Body (Reduced vertical padding) --- */
        .card-body{
            padding:1.8rem 1.6rem;
        }
        .form-title{color:var(--soil-brown);font-weight:800;font-size:1.6rem;text-align:center;margin-bottom:.3rem;}
        .form-subtitle{color:#666;text-align:center;margin-bottom:var(--spacing-lg);font-size:.92rem;}

        /* --- Form Fields --- */
        .form-group{position:relative;margin-bottom:var(--spacing-md);}
        .form-group label{
            position:absolute;top:50%;left:48px;transform:translateY(-50%);
            background:#fff;padding:0 6px;color:#888;font-size:.94rem;
            pointer-events:none;transition:.3s;
        }
        .opposites input:focus~label,
        .form-group input:valid~label{
            top:0;font-size:.76rem;color:var(--farm-green);font-weight:600;
        }
        .form-group .form-control{
            width:100%;height:52px;padding:0 1rem 0 48px;
            border:2px solid #e0e0e0;border-radius:12px;
            font-size:1rem;background:#fafafa;transition:.3s;
        }
        .form-group .form-control:focus{
            border-color:var(--farm-green);box-shadow:var(--glow);background:#fff;
        }
        .form-group i{
            position:absolute;top:50%;left:16px;transform:translateY(-50%);
            color:#aaa;font-size:1.2rem;transition:.3s;
        }
        .form-group input:focus~i{color:var(--farm-green);}

        /* --- Checkbox --- */
        .form-check{
            display:flex;align-items:center;
            margin-bottom:var(--spacing-md);
        }
        .form-check-input{
            width:1.3em;height:1.3em;margin-right:.6rem;
            border:2px solid #ccc;border-radius:6px;cursor:pointer;
        }
        .form-check-input:checked{
            background:var(--farm-green);border-color:var(--farm-green);
        }

        /* --- Login Button --- */
        #loginBtn{
            background:linear-gradient(135deg,var(--farm-green),#1B5E20);
            color:#fff;border:none;border-radius:12px;
            padding:.8rem 1.5rem;font-size:1.05rem;font-weight:700;
            width:100%;cursor:pointer;position:relative;overflow:hidden;
            box-shadow:0 6px 15px rgba(46,125,50,.3);transition:.4s;
            margin-bottom:var(--spacing-md);
        }
        #loginBtn::before{
            content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent);
            transition:.6s;
        }
        #loginBtn:hover::before{left:100%;}
        #loginBtn:hover{transform:translateY(-3px) scale(1.03);box-shadow:0 12px 25px rgba(46,125,50,.4);}
        #loginBtn:disabled{background:#ccc;transform:none;box-shadow:none;cursor:not-allowed;}

        /* --- Alerts --- */
        .alert{
            padding:.8rem 1rem;border-radius:12px;margin:0.8rem 0;font-size:.88rem;
            opacity:0;transform:translateY(-8px);transition:.3s;
            box-shadow:0 2px 6px rgba(0,0,0,.1);display:none;
        }
        .alert.show{opacity:1;transform:translateY(0);}
        .alert-danger{background:#ffebee;color:#c62828;border-left:5px solid #e53935;}
        .alert-success{background:#e8f5e9;color:#2e7d32;border-left:5px solid var(--farm-green);}

        /* --- Links & Divider --- */
        .text-link{color:var(--farm-green);font-weight:600;text-decoration:none;transition:.3s;}
        .text-link:hover{color:var(--harvest-gold);text-decoration:underline;}
        .divider{
            text-align:center;margin:var(--spacing-md) 0;
            position:relative;color:#888;font-size:.82rem;
        }
        .divider::before{
            content:'';position:absolute;top:50%;left:0;right:0;height:1px;
            background:#e0e0e0;z-index:1;
        }
        .divider span{background:#fff;padding:0 1rem;z-index:2;position:relative;}

        /* --- Responsive --- */
        @media(max-width:576px){
            .card-header{padding:1.6rem 1.2rem;}
            .card-body{padding:1.6rem 1.2rem;}
            .form-title{font-size:1.5rem;}
            body{padding:1rem;}
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="card-header">
        <i class="fas fa-tractor logo"></i>
        <h2>FarmConnect</h2>
        <p>Connecting farmers and buyers with trust and ease.</p>
    </div>

    <!-- Form Body -->
    <div class="card-body">
        <div class="text-center mb-3">
            <i class="fas fa-seedling" style="font-size:2.2rem;color:var(--farm-green);"></i>
        </div>
        <h1 class="form-title">Welcome Back!</h1>
        <p class="form-subtitle">Log in to access fresh farm produce</p>

        <form id="loginForm">
            <div class="form-group">
                <input type="email" class="form-control" id="email" name="email" required>
                <i class="fas fa-envelope"></i>
                <label for="email">Email Address</label>
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <i class="fas fa-lock"></i>
                <label for="password">Password</label>
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe" value="1">
                <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>

            <button type="submit" id="loginBtn">
                <span id="btnText">Login</span>
                <span id="spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
            </button>

            <div id="loginError" class="alert alert-danger"></div>

            <div id="resend-activation" style="display:none;">
                <p class="text-center">
                    Didn't receive activation email?
                    <a href="#" id="resend-link" class="text-link">Resend Link</a>
                </p>
                <input type="hidden" id="resend-email">
                <div id="resend-message"></div>
            </div>

            <div class="divider"><span>OR</span></div>

            <div class="text-center">
                <a class="text-link small" href="forgot-password.html">Forgot Password?</a>
            </div>
            <div class="text-center mt-2">
                <a class="text-link" href="Buyer_register.php">Create an Account!</a>
            </div>
        </form>
    </div>
</div>

<script src="asset/vendor/jquery/jquery.min.js"></script>
<script>
function showAlert(el){
    el.style.display='block';
    setTimeout(()=>el.classList.add('show'),10);
}

document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('loginBtn');
    const txt = document.getElementById('btnText');
    const spin = document.getElementById('spinner');
    const err = document.getElementById('loginError');
    const resend = document.getElementById('resend-activation');
    const msg = document.getElementById('resend-message');

    err.style.display='none'; err.classList.remove('show'); err.innerHTML='';
    resend.style.display='none'; msg.innerHTML='';

    btn.disabled=true; txt.style.display='none'; spin.style.display='inline-block';

    fetch('views/buyer_login.php', {method:'POST', body:new FormData(this)})
    .then(r=>r.json())
    .then(d=>{
        if(d.status==='success'){
            window.location.href='buyersDashboard.php';
        }else if(d.status==='unverified'){
            err.innerHTML=d.message; showAlert(err);
            resend.style.display='block';
            document.getElementById('resend-email').value=d.email;
        }else{
            err.innerHTML=d.message||'Login failed.'; showAlert(err);
        }
    })
    .catch(()=>{ err.innerHTML='Network error.'; showAlert(err); })
    .finally(()=>{
        btn.disabled=false; txt.style.display='inline'; spin.style.display='none';
    });
});

document.getElementById('resend-link').addEventListener('click', e=>{
    e.preventDefault();
    const email=document.getElementById('resend-email').value;
    const box=document.getElementById('resend-message');
    fetch('views/buyer_login.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`resend_activation=1&email=${encodeURIComponent(email)}`
    })
    .then(r=>r.json())
    .then(d=>{
        const html = d.status==='success'
            ? `<div class="alert alert-success show">${d.message}</div>`
            : `<div class="alert alert-danger show">${d.message}</div>`;
        box.innerHTML=html;
    })
    .catch(()=>{ box.innerHTML='<div class="alert alert-danger show">Failed to send.</div>'; });
});
</script>
</body>
</html>