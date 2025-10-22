<?php include 'DBcon.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buyer! Sign Up</title>
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
  #regBtn {
    background-color: #2f855a;
    color: white;
    padding: 10px 16px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  #regBtn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
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
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form id="registerForm" class="user">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control" name="phone" placeholder="Phone Number" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control" name="gender" required>
                                            <option value="" disabled selected>Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="address" placeholder="Enter Your Contact Address" required></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-6">
                                        <select name="state" id="state" class="form-control" required>
                                            <option selected disabled>Choose State</option>
                                            <?php
                                            $query = "SELECT state_id, state_name FROM states";
                                            $result = mysqli_query($conn, $query);
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='{$row['state_id']}'>{$row['state_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <select id="city" name="city" class="form-control" required>
                                            <option value="">Select LGA</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- New Buyer Type Field -->
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
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block" id="regBtn">Register</button>
                                <span id="spinner" style="display: none; margin-right: 8px;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                <div id="responseMsg" class="mt-3 text-center"></div>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="Buyer_login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
    // Populate city dropdown
    $('#state').change(function(){
        var state_id = $(this).val();
        if(state_id){
            $.ajax({
                url: 'get-cities.php',
                method: 'POST',
                data: { state_id: state_id },
                dataType: 'json',
                success: function(data){
                    if (data.error) {
                        console.error(data.error);
                        $('#city').html('<option value="">Select LGA</option>');
                    } else {
                        var options = '<option value="">Select LGA</option>';
                        $.each(data, function(index, city){
                            options += '<option value="' + city.city_id + '">' + city.city_name + '</option>';
                        });
                        $('#city').html(options);
                    }
                },
                error: function(xhr, status, error){
                    console.error("AJAX Error:", status, error);
                    $('#city').html('<option value="">Select LGA</option>');
                }
            });
        } else {
            $('#city').html('<option value="">Select LGA</option>');
        }
    });

    $(document).ready(function() {
        $('#registerForm').on('submit', function(e) {
            e.preventDefault(); // prevent default form submission
            
            // Show loading state
            $('#regBtn').prop('disabled', true).html('Registering...');
            $('#spinner').show();

            $.ajax({
                url: 'views/buyer_reg.php', // backend logic
                method: 'POST',
                data: $(this).serialize(), // serialize form data
                success: function(response) {
                    $('#responseMsg').html(response); // show response message
                    if (response.includes('successful')) {
                        $('#registerForm')[0].reset(); // reset form on success
                    }
                },
                error: function(xhr, status, error) {
                    $('#responseMsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    console.error("AJAX Error:", status, error);
                },
                complete: function() {
                    // Re-enable button
                    $('#regBtn').prop('disabled', false).html('Register');
                    $('#spinner').hide();
                }
            });
        });
    });
    </script>
</body>
</html>