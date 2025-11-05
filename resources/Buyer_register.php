<?php include 'DBcon.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buyer – Register | FarmConnect</title>

    <!-- Font Awesome + Google Font -->
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --farm-green:#2E7D32;--harvest-gold:#FF8F00;--soil-brown:#5D4037;
            --light-field:#F1F8E9;--wheat:#FFCA28;--sky:#81D4FA;
            --shadow:rgba(46,125,50,.25);--glow:0 0 15px rgba(255,143,0,.5);
            --spacing-sm: 0.8rem;--spacing-md: 1.2rem;--spacing-lg: 1.8rem;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{
            font-family:'Nunito',sans-serif;
            background:linear-gradient(135deg,#E8F5E9 0%,#C8E6C9 100%);
            min-height:100vh;display:flex;align-items:center;justify-content:center;
            padding:1.5rem 1rem;position:relative;overflow-y:auto;
        }
        body::before{
            content:'';position:absolute;top:0;left:0;right:0;bottom:0;
            background:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"><path fill="%23C8E6C9" opacity="0.2" d="M0 60 Q30 40,60 60 T120 60 V120 H0 Z"/></svg>') repeat;
            opacity:.15;z-index:-1;
        }

        /* Wider Card */
        .register-card{
            background:#fff;border-radius:20px;overflow:hidden;
            box-shadow:0 20px 50px var(--shadow);
            max-width:640px;width:100%;
            transition:.4s;
        }
        .register-card:hover{transform:translateY(-8px);box-shadow:0 30px 70px rgba(46,125,50,.35);}

        .card-header{
            background:linear-gradient(rgba(46,125,50,.85),rgba(46,125,50,.95)),
                        url('https://images.unsplash.com/photo-1500595046743-cd271d694d30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') center/cover;
            padding:2rem 1.8rem;text-align:center;color:#fff;position:relative;
        }
        .card-header::after{
            content:'';position:absolute;bottom:0;left:0;right:0;height:70px;
            background:linear-gradient(transparent,#fff);
        }
        .logo{font-size:3rem;color:var(--harvest-gold);animation:float 3s ease-in-out infinite;}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        .card-header h2{font-weight:800;font-size:1.8rem;margin-top:.7rem;}
        .card-header p{font-size:.95rem;opacity:.9;max-width:280px;margin:0 auto;}

        .card-body{padding:2.2rem 2rem;}
        .form-title{color:var(--soil-brown);font-weight:800;font-size:1.7rem;text-align:center;margin-bottom:.4rem;}
        .form-subtitle{color:#666;text-align:center;margin-bottom:var(--spacing-lg);font-size:.94rem;}

        /* Inline 2-Column Layout */
        .form-row{
            display:flex;gap:var(--spacing-md);margin-bottom:var(--spacing-md);
            flex-wrap:wrap;
        }
        .ut{
            margin-top: 20px;
        }
        .form-group{
            position:relative;flex:1;min-width:250px;
        }
        .form-group label{
            position:absolute;top:50%;left:48px;transform:translateY(-50%);
            background:#fff;padding:0 6px;color:#888;font-size:.94rem;
            pointer-events:none;transition:.3s;z-index:1;
        }
        .form-group input:focus~label,
        .form-group input:not(:placeholder-shown)~label,
        .form-group select:focus~label,
        .form-group select:not([value=""])~label,
        .form-group textarea:focus~label,
        .form-group textarea:not(:placeholder-shown)~label{
            top:0;font-size:.76rem;color:var(--farm-green);font-weight:600;
        }

        .form-group .form-control,
        .form-group select,
        .form-group textarea{
            width:100%;padding:0 1rem 0 48px;
            border:2px solid #e0e0e0;border-radius:12px;
            font-size:1rem;background:#fafafa;transition:.3s;
        }
        .form-group input,
        .form-group select{height:54px;}
        .form-group textarea{min-height:94px;resize:vertical;padding-top:1rem;}

        .form-group .form-control:focus,
        .form-group select:focus,
        .form-group textarea:focus{
            border-color:var(--farm-green);box-shadow:var(--glow);background:#fff;
        }

        .form-group i{
            position:absolute;top:50%;left:16px;transform:translateY(-50%);
            color:#aaa;font-size:1.2rem;transition:.3s;z-index:1;
        }
        .form-group input:focus~i,
        .form-group select:focus~i,
        .form-group textarea:focus~i{color:var(--farm-green);}

        #regBtn{
            background:linear-gradient(135deg,var(--farm-green),#1B5E20);
            color:#fff;border:none;border-radius:12px;
            padding:.9rem 1.5rem;font-size:1.08rem;font-weight:700;
            width:100%;cursor:pointer;position:relative;overflow:hidden;
            box-shadow:0 6px 15px rgba(46,125,50,.3);transition:.4s;
            margin: var(--spacing-lg) 0 var(--spacing-md) 0;
        }
        #regBtn::before{
            content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent);
            transition:.6s;
        }
        #regBtn:hover::before{left:100%;}
        #regBtn:hover{transform:translateY(-3px) scale(1.03);box-shadow:0 12px 25px rgba(46,125,50,.4);}
        #regBtn:disabled{background:#ccc;transform:none;box-shadow:none;cursor:not-allowed;}

        .alert{
            padding:.9rem 1.1rem;border-radius:12px;margin:0.8rem 0;font-size:.9rem;
            opacity:0;transform:translateY(-8px);transition:.3s;
            box-shadow:0 2px 6px rgba(0,0,0,.1);display:none;
        }
        .alert.show{opacity:1;transform:translateY(0);}
        .alert-success{background:#e8f5e9;color:#2e7d32;border-left:5px solid var(--farm-green);}
        .alert-danger{background:#ffebee;color:#c62828;border-left:5px solid #e53935;}

        .text-link{color:var(--farm-green);font-weight:600;text-decoration:none;transition:.3s;}
        .text-link:hover{color:var(--harvest-gold);text-decoration:underline;}

        /* Responsive */
        @media(max-width:576px){
            .form-row{flex-direction:column;gap:0;}
            .form-group{min-width:100%;}
            .card-header,.card-body{padding:1.8rem 1.4rem;}
            .form-title{font-size:1.5rem;}
        }
    </style>
</head>
<body>

<div class="register-card">
    <!-- Header -->
    <div class="card-header">
        <i class="fas fa-tractor logo"></i>
        <h2>FarmConnect</h2>
        <p>Connecting farmers and buyers with trust and ease.</p>
    </div>

    <!-- Form -->
    <div class="card-body">
        <div class="text-center mb-3">
            <i class="fas fa-user-plus" style="font-size:2.3rem;color:var(--farm-green);"></i>
        </div>
        <h1 class="form-title">Create an Account!</h1>
        <p class="form-subtitle">Join to access fresh farm produce</p>

        <form id="registerForm">
            <!-- Name -->
            <div class="form-row">
                <div class="form-group">
                    <input type="text" class="form-control" name="firstname" required placeholder=" ">
                    <i class="fas fa-user"></i>
                    <label>First Name</label>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="lastname" required placeholder=" ">
                    <i class="fas fa-user"></i>
                    <label>Last Name</label>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <input type="email" class="form-control" name="email" required placeholder=" ">
                <i class="fas fa-envelope"></i>
                <label>Email Address</label>
            </div>

            <!-- Phone & Gender -->
            <div class="form-row ut">
                <div class="form-group">
                    <input type="text" class="form-control" name="phone" required placeholder=" ">
                    <i class="fas fa-phone"></i>
                    <label>Phone Number</label>
                </div>
                <div class="form-group">
                    <select name="gender" class="form-control" required>
                        <option value="" disabled selected></option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    <i class="fas fa-venus-mars"></i>
                    <label>Gender</label>
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <textarea name="address" required placeholder=" "></textarea>
                <i class="fas fa-map-marker-alt"></i>
                <label>Delivery Address</label>
            </div>

            <!-- State & LGA -->
            <div class="form-row ut">
                <div class="form-group">
                    <select name="state" id="state" class="form-control" required>
                        <option value="" disabled selected></option>
                        <?php
                        $q = "SELECT state_id, state_name FROM states ORDER BY state_name";
                        $r = mysqli_query($conn, $q);
                        while($row = mysqli_fetch_assoc($r))
                            echo "<option value='{$row['state_id']}'>{$row['state_name']}</option>";
                        ?>
                    </select>
                    <i class="fas fa-map"></i>
                    <label>State</label>
                </div>
                <div class="form-group">
                    <select id="city" name="city" class="form-control" required>
                        <option value="" disabled selected></option>
                    </select>
                    <i class="fas fa-city"></i>
                    <label>LGA</label>
                </div>
            </div>

            <!-- Buyer Type -->
            <div class="form-group">
                <select name="buyer_type" class="form-control" required>
                    <option value="" disabled selected></option>
                    <option value="Wholesaler">Wholesaler</option>
                    <option value="Retailer">Retailer</option>
                    <option value="Exporter">Exporter</option>
                    <option value="Processor">Processor</option>
                    <option value="Distributor">Distributor</option>
                    <option value="Restaurant">Restaurant</option>
                    <option value="Hotel">Hotel</option>
                    <option value="Individual">Individual Consumer</option>
                    <option value="Other">Other</option>
                </select>
                <i class="fas fa-store"></i>
                <label>Buyer Type</label>
            </div>

            <!-- Passwords -->
            <div class="form-row ut">
                <div class="form-group">
                    <input type="password" class="form-control" name="password" required placeholder=" ">
                    <i class="fas fa-lock"></i>
                    <label>Password</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="repeat_password" required placeholder=" ">
                    <i class="fas fa-lock"></i>
                    <label>Repeat Password</label>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" id="regBtn">
                <span id="btnText">Register</span>
                <span id="spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
            </button>

            <div id="responseMsg"></div>

            <div class="text-center">
                <a class="text-link" href="Buyer_login.php">Already have an account? Login!</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Dynamic LGA
$('#state').on('change', function(){
    const sid = $(this).val();
    const citySelect = $('#city').html('<option value="" disabled selected></option>');
    if(!sid) return;
    $.post('get-cities.php', {state_id: sid}, function(d){
        $.each(d, (_, c) => citySelect.append(`<option value="${c.city_id}">${c.city_name}</option>`));
    }, 'json');
});

// Registration
$('#registerForm').on('submit', function(e){
    e.preventDefault();
    const btn = $('#regBtn'), txt = $('#btnText'), spin = $('#spinner');
    const msg = $('#responseMsg').empty();

    btn.prop('disabled', true); txt.hide(); spin.show();

    $.ajax({
        url: 'views/buyer_reg.php',
        method: 'POST',
        data: $(this).serialize(),
        success: function(r){
            msg.html(r);
            if(r.includes('successful') || r.includes('success')){
                $('#registerForm')[0].reset();
                $('#state, #city').trigger('change');
            }
        },
        error: function(){
            msg.html('<div class="alert alert-danger show">An error occurred. Please try again.</div>');
        },
        complete: function(){
            btn.prop('disabled', false); txt.show(); spin.hide();
        }
    });
});

// Animate alerts
$(document).on('DOMNodeInserted', '#responseMsg .alert', function(){
    setTimeout(() => this.classList.add('show'), 10);
});
</script>
</body>
</html>