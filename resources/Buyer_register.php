<?php include 'DBcon.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buyer! Sign Up</title>
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        #regBtn{background:#2f855a;color:#fff;padding:10px 16px;font-size:16px;border:none;border-radius:6px;cursor:pointer;}
        #regBtn:disabled{background:#ccc;cursor:not-allowed;}
        .alert{padding:15px 20px;border-radius:8px;margin:15px 0;font-size:1rem;line-height:1.5;box-shadow:0 2px 6px rgba(0,0,0,.1);}
        .alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
        .alert-danger{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    </style>
</head>
<body class="bg-gradient-primary">
<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center"><h1 class="h4 text-gray-900 mb-4">Create an Account!</h1></div>

                        <form id="registerForm" class="user">
                            <!-- (all form fields stay exactly the same) -->
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0"><input type="text" class="form-control" name="firstname" placeholder="First Name" required></div>
                                <div class="col-sm-6"><input type="text" class="form-control" name="lastname" placeholder="Last Name" required></div>
                            </div>
                            <div class="form-group"><input type="email" class="form-control" name="email" placeholder="Email Address" required></div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0"><input type="text" class="form-control" name="phone" placeholder="Phone Number" required></div>
                                <div class="col-sm-6">
                                    <select class="form-control" name="gender" required>
                                        <option value="" disabled selected>Gender</option>
                                        <option value="Male">Male</option><option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group"><textarea class="form-control" name="address" placeholder="Enter Your Contact Address" required></textarea></div>

                            <div class="form-row">
                                <div class="form-group col-sm-6">
                                    <select name="state" id="state" class="form-control" required>
                                        <option selected disabled>Choose State</option>
                                        <?php
                                        $q = "SELECT state_id, state_name FROM states";
                                        $r = mysqli_query($conn, $q);
                                        while($row = mysqli_fetch_assoc($r))
                                            echo "<option value='{$row['state_id']}'>{$row['state_name']}</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <select id="city" name="city" class="form-control" required><option value="">Select LGA</option></select>
                                </div>
                            </div>

                            <div class="form-group">
                                <select class="form-control" name="buyer_type" required>
                                    <option value="" disabled selected>Select Buyer Type</option>
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
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0"><input type="password" class="form-control" name="password" placeholder="Password" required></div>
                                <div class="col-sm-6"><input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password" required></div>
                            </div>

                            <button type="submit" id="regBtn" class="btn btn-primary btn-user btn-block">Register</button>
                            <span id="spinner" style="display:none;margin-left:8px;"><i class="fa fa-spinner fa-spin"></i></span>

                            <div id="responseMsg" class="mt-3 text-center"></div>
                        </form>

                        <hr>
                        <div class="text-center"><a class="small" href="Buyer_login.php">Already have an account? Login!</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
$('#state').change(function(){
    const sid = $(this).val();
    if(!sid){ $('#city').html('<option value="">Select LGA</option>'); return; }
    $.post('get-cities.php',{state_id:sid},function(d){
        let opts = '<option value="">Select LGA</option>';
        $.each(d,(_,c)=>opts+=`<option value="${c.city_id}">${c.city_name}</option>`);
        $('#city').html(opts);
    },'json');
});

$('#registerForm').on('submit',function(e){
    e.preventDefault();
    const btn = $('#regBtn'), spin = $('#spinner');
    btn.prop('disabled',true).html('Registering...'); spin.show();

    $.ajax({
        url:'views/buyer_reg.php',
        method:'POST',
        data:$(this).serialize(),
        success:function(r){
            $('#responseMsg').html(r);
            if(r.includes('successful')) $('#registerForm')[0].reset();
        },
        error:function(){
            $('#responseMsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
        },
        complete:function(){
            btn.prop('disabled',false).html('Register'); spin.hide();
        }
    });
});
</script>
</body>
</html>