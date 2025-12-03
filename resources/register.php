<?php include 'DBcon.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer! Sign Up</title>
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #2f855a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .step-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-progress::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: bold;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .step.active .step-circle {
            background: #2f855a;
            color: white;
            border-color: #2f855a;
        }
        .step.completed .step-circle {
            background: #38a169;
            color: white;
            border-color: #38a169;
        }
        .step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }
        .step.active .step-label {
            color: #2f855a;
            font-weight: bold;
        }
        .form-step {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        .form-step.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .btn-navigation {
            background: #2f855a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-navigation:hover {
            background: #276749;
        }
        .btn-navigation:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
        }
        .btn-prev {
            background: #6c757d;
        }
        .btn-prev:hover {
            background: #5a6268;
        }
        .file-upload-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .section-title {
            color: #2f855a;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .progress-bar-container {
            background: #e9ecef;
            border-radius: 10px;
            height: 6px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: #2f855a;
            border-radius: 10px;
            transition: width 0.3s ease;
            width: 0%;
        }
        /* Validation Styles */
        .is-invalid {
            border-color: #e74a3b !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23e74a3b' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5zM6 8.2a.3.3 0 110-.6.3.3 0 010 .6z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .is-valid {
            border-color: #1cc88a !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%231cc88a' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #e74a3b;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
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
                                <h1 class="h4 text-gray-900 mb-4">Create Farmer Account</h1>
                                <p class="text-muted">Complete your registration in a few simple steps</p>
                            </div>

                            <div class="progress-bar-container">
                                <div class="progress-bar" id="progressBar"></div>
                            </div>

                            <div class="step-progress">
                                <div class="step active" data-step="1">
                                    <div class="step-circle">1</div>
                                    <div class="step-label">Personal Info</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-circle">2</div>
                                    <div class="step-label">Farm Details</div>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-circle">3</div>
                                    <div class="step-label">Documents</div>
                                </div>
                                <div class="step" data-step="4">
                                    <div class="step-circle">4</div>
                                    <div class="step-label">Security</div>
                                </div>
                            </div>

                            <form id="registerForm" class="user" enctype="multipart/form-data" novalidate>
                                <div class="form-step active" id="step1">
                                    <div class="form-section">
                                        <h5 class="section-title"><i class="fas fa-user me-2 mr-2"></i>Personal Information</h5>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                                                <div class="invalid-feedback">First name is required (min 2 chars).</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                                                <div class="invalid-feedback">Last name is required (min 2 chars).</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="text" class="form-control" name="phone" placeholder="Phone Number" required>
                                                <div class="invalid-feedback">Please enter a valid 11-digit phone number.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" name="gender" required>
                                                    <option value="" disabled selected>Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                                <div class="invalid-feedback">Please select your gender.</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <textarea class="form-control" name="address" placeholder="Enter Your Contact Address" required rows="3"></textarea>
                                            <div class="invalid-feedback">Address is required.</div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm-6">
                                                <select name="state_id" id="state" class="form-control" required>
                                                    <option selected disabled value="">Choose State</option>
                                                    <?php
                                                    $query = "SELECT state_id, state_name FROM states";
                                                    $result = mysqli_query($conn, $query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo "<option value='{$row['state_id']}'>{$row['state_name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a state.</div>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <select id="city" name="city_id" class="form-control" required>
                                                    <option value="">Select LGA</option>
                                                </select>
                                                <div class="invalid-feedback">Please select a Local Government Area.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-navigation">
                                        <div></div> <button type="button" class="btn-navigation" onclick="nextStep()">Next <i class="fas fa-arrow-right ms-2"></i></button>
                                    </div>
                                </div>

                                <div class="form-step" id="step2">
                                    <div class="form-section">
                                        <h5 class="section-title"><i class="fas fa-tractor me-2 mr-2"></i>Farm Information</h5>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="text" class="form-control" name="farm_name" placeholder="Farm Name">
                                                <div class="invalid-feedback">Farm name is required.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="number" step="0.01" class="form-control" name="farm_size" placeholder="Farm Size (hectares)">
                                                <div class="invalid-feedback">Please enter a valid farm size.</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <textarea class="form-control" name="farm_full_address" placeholder="Farm Full Address" rows="3"></textarea>
                                            <div class="invalid-feedback">Farm address is required.</div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <select class="form-control" name="land_ownership_type">
                                                    <option value="" disabled selected>Land Ownership Type</option>
                                                    <option value="Owned">Owned</option>
                                                    <option value="Leased">Leased</option>
                                                    <option value="Rented">Rented</option>
                                                </select>
                                                <div class="invalid-feedback">Please select ownership type.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="number" class="form-control" name="farming_experience" placeholder="Farming Experience (years)">
                                                <div class="invalid-feedback">Please enter years of experience.</div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <select class="form-control" name="farmer_type" id="farmer_type" required>
                                                    <option value="" disabled selected>Select Farmer Type</option>
                                                    <option value="Arable/Crop">Arable/Crop Farmer</option>
                                                    <option value="Horticultural">Horticultural Farmer (Fruits/Veg)</option>
                                                    <option value="Livestock/Pastoral">Livestock/Pastoral Farmer</option>
                                                    <option value="Aquaculture">Aquaculture Farmer</option>
                                                    <option value="Agroforestry">Agroforestry Farmer</option>
                                                    <option value="Mixed">Mixed Crop-Livestock Farmer</option>
                                                </select>
                                                <div class="invalid-feedback">Please select farmer type.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <select class="form-control" name="primary_produce" id="primary_produce" required>
                                                    <option value="" disabled selected>Select Primary Produce</option>
                                                </select>
                                                <div class="invalid-feedback">Please select primary produce.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-section">
                                        <h5 class="section-title"><i class="fas fa-id-card me-2 mr-2"></i>Identification</h5>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="text" class="form-control" name="cac_number" placeholder="CAC Number (if applicable)">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="nin" placeholder="NIN" required> 
                                                <div class="invalid-feedback">NIN is required.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-navigation">
                                        <button type="button" class="btn-navigation btn-prev" onclick="prevStep()"><i class="fas fa-arrow-left me-2 mr-2"></i>Previous</button>
                                        <button type="button" class="btn-navigation" onclick="nextStep()">Next <i class="fas fa-arrow-right ms-2"></i></button>
                                    </div>
                                </div>

                                <div class="form-step" id="step3">
                                    <div class="form-section">
                                        <h5 class="section-title"><i class="fas fa-file-upload me-2 mr-2"></i>Document Uploads</h5>
                                        <div class="form-group">
                                            <label class="form-label">Profile Picture <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="profile_picture" accept="image/jpeg,image/png" required>
                                            <small class="file-upload-info">JPG or PNG, max 2MB</small>
                                            <div class="invalid-feedback">Profile picture is required (JPG/PNG).</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">NIN Document <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="national_id_doc" accept="image/jpeg,image/png,application/pdf" required>
                                            <small class="file-upload-info">JPG, PNG or PDF, max 5MB</small>
                                            <div class="invalid-feedback">NIN document is required.</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">CAC Document</label>
                                            <input type="file" class="form-control" name="other_document" accept="image/jpeg,image/png,application/pdf">
                                            <small class="file-upload-info">JPG, PNG or PDF, max 5MB</small>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Land Ownership Document</label>
                                            <input type="file" class="form-control" name="landDocument" accept="image/jpeg,image/png,application/pdf">
                                            <small class="file-upload-info">JPG, PNG or PDF, max 5MB</small>
                                        </div>
                                    </div>
                                    <div class="form-navigation">
                                        <button type="button" class="btn-navigation btn-prev" onclick="prevStep()"><i class="fas fa-arrow-left me-2 mr-2"></i>Previous</button>
                                        <button type="button" class="btn-navigation" onclick="nextStep()">Next <i class="fas fa-arrow-right ms-2"></i></button>
                                    </div>
                                </div>

                                <div class="form-step" id="step4">
                                    <div class="form-section">
                                        <h5 class="section-title"><i class="fas fa-shield-alt me-2 mr-2"></i>Account Security</h5>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                                                <div class="invalid-feedback">Password must be at least 8 characters, include uppercase, lowercase, number, and symbol.</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password" required>
                                                <div class="invalid-feedback">Passwords do not match.</div>
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle me-2"></i>
                                                Your password should be at least 8 characters long and include uppercase letters, lowercase letters, numbers, and special characters.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-navigation">
                                        <button type="button" class="btn-navigation btn-prev" onclick="prevStep()"><i class="fas fa-arrow-left me-2 mr-2"></i>Previous</button>
                                        <button type="submit" class="btn-navigation" id="regBtn">
                                            <span id="submitText">Create Account</span>
                                            <span id="spinner" style="display: none;">
                                                <i class="fas fa-spinner fa-spin ms-2"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <div id="responseMsg" class="mt-3 text-center"></div>
                            </form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        // MAPPING OBJECT for Farmer Type to Primary Produce
        const produceMapping = {
            'Arable/Crop': [
                "Cereals (Maize, Rice, Sorghum)",
                "Roots/Tubers (Yam, Cassava, Potato)",
                "Legumes (Beans, Groundnut)",
                "Oilseeds (Soybean, Sesame)"
            ],
            'Horticultural': [
                "Fruits (Citrus, Mango, Pineapple)",
                "Vegetables (Tomato, Pepper, Okra)",
                "Spices (Ginger, Turmeric)"
            ],
            'Livestock/Pastoral': [
                "Poultry (Broilers, Layers)",
                "Ruminants (Cattle, Sheep, Goats - Meat)",
                "Dairy (Milk, Cheese)",
                "Pigs"
            ],
            'Aquaculture': [
                "Catfish",
                "Tilapia",
                "Prawns/Shrimp",
                "Ornamentals"
            ],
            'Agroforestry': [
                "Cocoa",
                "Coffee",
                "Cashew",
                "Palm Oil",
                "Rubber",
                "Timber/Wood"
            ],
            'Mixed': [
                "General Mixed Crops",
                "General Mixed Livestock",
                "Both"
            ]
        };

        // Function to update the Primary Produce dropdown
        function updateProduceDropdown(farmerType) {
            const $produceSelect = $('#primary_produce');
            $produceSelect.empty();
            $produceSelect.append('<option value="" disabled selected>Select Primary Produce</option>');

            if (farmerType && produceMapping[farmerType]) {
                produceMapping[farmerType].forEach(produce => {
                    $produceSelect.append('<option value="' + produce + '">' + produce + '</option>');
                });
            }
        }

        // Event listener for Farmer Type change
        $('#farmer_type').on('change', function() {
            const selectedType = $(this).val();
            updateProduceDropdown(selectedType);
            validateField(this); // Re-validate
        });

        // Update progress bar
        function updateProgressBar() {
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        // Update step indicators
        function updateStepIndicators() {
            document.querySelectorAll('.step').forEach((step, index) => {
                const stepNumber = index + 1;
                if (stepNumber < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (stepNumber === currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            });
        }

        // Show current step
        function showStep(stepNumber) {
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });
            document.getElementById('step' + stepNumber).classList.add('active');
        }

        // --- VALIDATION LOGIC ---

        function validateField(input) {
            const name = input.name;
            const value = input.value.trim();
            const type = input.type;
            let isValid = true;

            // Reset status
            input.classList.remove('is-invalid', 'is-valid');
            
            // Restore original error message if exists
            let feedback = input.nextElementSibling;
            while (feedback && !feedback.classList.contains('invalid-feedback')) {
                feedback = feedback.nextElementSibling;
            }
            if (feedback && feedback.hasAttribute('data-original-text')) {
                feedback.textContent = feedback.getAttribute('data-original-text');
            }

            // Required check
            if (input.hasAttribute('required') && !value && type !== 'file') {
                isValid = false;
            }

            // Specific checks
            if (isValid) {
                switch (name) {
                    case 'first_name':
                    case 'last_name':
                        if (value.length < 2) isValid = false;
                        break;
                    case 'email':
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) isValid = false;
                        break;
                    case 'phone':
                        const phoneRegex = /^\d{11}$/;
                        if (!phoneRegex.test(value)) isValid = false;
                        break;
                    case 'password':
                        // Min 8 chars, 1 upper, 1 lower, 1 number, 1 symbol
                        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                        if (!passRegex.test(value)) isValid = false;
                        break;
                    case 'repeat_password':
                        const pass = document.querySelector('input[name="password"]').value;
                        if (value !== pass) isValid = false;
                        break;
                }
            }
            
            // File validation
            if (type === 'file' && input.hasAttribute('required')) {
                if (input.files.length === 0) isValid = false;
            }

            if (isValid) {
                input.classList.add('is-valid');
            } else {
                input.classList.add('is-invalid');
            }
            return isValid;
        }

        function showFieldError(inputName, message) {
            const input = document.querySelector(`[name="${inputName}"]`);
            if (input) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                let feedback = input.nextElementSibling;
                while (feedback && !feedback.classList.contains('invalid-feedback')) {
                    feedback = feedback.nextElementSibling;
                }
                if (feedback) {
                    // Save original text if not saved yet
                    if (!feedback.hasAttribute('data-original-text')) {
                        feedback.setAttribute('data-original-text', feedback.textContent);
                    }
                    feedback.textContent = message;
                }
            }
        }

        function checkAvailability(field, value) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: 'check_availability.php',
                    method: 'POST',
                    data: { field: field, value: value },
                    dataType: 'json',
                    success: function(response) {
                        resolve(response.exists);
                    },
                    error: function() {
                        console.error('Availability check failed');
                        resolve(false); // Assume false on error to avoid blocking
                    }
                });
            });
        }

        // Attach listeners to all inputs
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', () => validateField(input));
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('change', () => validateField(input));
            
            // Initialize data-original-text
            let feedback = input.nextElementSibling;
            while (feedback && !feedback.classList.contains('invalid-feedback')) {
                feedback = feedback.nextElementSibling;
            }
            if (feedback) {
                feedback.setAttribute('data-original-text', feedback.textContent);
            }
        });

        // Next step
        async function nextStep() {
            // 1. Client-side validation
            if (!validateStep(currentStep)) {
                return;
            }

            // 2. Database validation (Async)
            const nextBtns = document.querySelectorAll('.btn-navigation');
            nextBtns.forEach(btn => btn.disabled = true);
            document.body.style.cursor = 'wait';

            let dbValid = true;

            try {
                if (currentStep === 1) {
                    const email = $('input[name="email"]').val();
                    const phone = $('input[name="phone"]').val();

                    // Check Email
                    if (await checkAvailability('email', email)) {
                        showFieldError('email', 'Email is already registered.');
                        dbValid = false;
                    }

                    // Check Phone
                    if (await checkAvailability('phone', phone)) {
                        showFieldError('phone', 'Phone number is already registered.');
                        dbValid = false;
                    }
                } else if (currentStep === 2) {
                    const nin = $('input[name="nin"]').val();
                    const cac = $('input[name="cac_number"]').val();

                    // Check NIN
                    if (await checkAvailability('nin', nin)) {
                        showFieldError('nin', 'NIN is already registered.');
                        dbValid = false;
                    }

                    // Check CAC (if provided)
                    if (cac && await checkAvailability('cac_number', cac)) {
                        showFieldError('cac_number', 'CAC Number is already registered.');
                        dbValid = false;
                    }
                }
            } catch (e) {
                console.error(e);
            } finally {
                nextBtns.forEach(btn => btn.disabled = false);
                document.body.style.cursor = 'default';
            }

            if (!dbValid) {
                const firstInvalid = document.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            // 3. Proceed
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                updateStepIndicators();
                updateProgressBar();
            }
        }

        // Previous step
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                updateStepIndicators();
                updateProgressBar();
            }
        }

        // Validate current step
        function validateStep(step) {
            const currentStepElement = document.getElementById('step' + step);
            const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
            
            let isStepValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isStepValid = false;
                }
            });

            if (!isStepValid) {
                // Focus first invalid element
                const firstInvalid = currentStepElement.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
            }

            return isStepValid;
        }

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

        // Form submission
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                // Validate all steps before submission
                for (let step = 1; step <= totalSteps; step++) {
                    if (!validateStep(step)) {
                        currentStep = step;
                        showStep(currentStep);
                        updateStepIndicators();
                        updateProgressBar();
                        alert('Please fix errors in step ' + step + ' before submitting.');
                        return;
                    }
                }

                // Disable button and show spinner
                var $btn = $('#regBtn');
                var $spinner = $('#spinner');
                var $submitText = $('#submitText');
                $btn.prop('disabled', true);
                $spinner.show();
                $submitText.text('Creating Account...');

                // Clear previous messages
                $('#responseMsg').html('');

                // Create FormData object to handle file uploads
                var formData = new FormData(this);

                $.ajax({
                    url: 'views/register.view.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#responseMsg').html(response);

                        if (response.indexOf('success') !== -1 || response.indexOf('Success') !== -1) {
                            $('#registerForm')[0].reset();
                            $('#city').html('<option value="">Select LGA</option>');
                            // Reset produce dropdown
                            updateProduceDropdown(null); 
                            // Remove validation classes
                            $('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                            // Reset to first step
                            currentStep = 1;
                            showStep(currentStep);
                            updateStepIndicators();
                            updateProgressBar();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#responseMsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                        console.error("AJAX Error:", status, error);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.hide();
                        $submitText.text('Create Account');
                    }
                });
            });
        });

        // Initialize
        updateProgressBar();
        updateStepIndicators();
        // Initialize the produce dropdown to be empty
        updateProduceDropdown(null);
    </script>
</body>
</html>