<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'DBcon.php'; // Adjust path if necessary

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

$userId = $_SESSION['user_id'];

// Redirect if info_completed is already 1 (meaning they've filled this out)
if (isset($_SESSION['info_completed']) && $_SESSION['info_completed'] == 1) {
    // If info is completed but not approved, direct to awaiting approval
    if (isset($_SESSION['cbn_approved']) && $_SESSION['cbn_approved'] == 0) { // Using cbn_approved == 0 for pending
        header('Location: awaiting_approval.php');
        exit;
    }
    // Otherwise, direct to dashboard (meaning info_completed == 1 AND cbn_approved == 1)
    header('Location: farmersDashboard.php');
    exit;
}

// Fetch user data to pre-fill the form
$userFullName = '';
$userPhone = '';
$userEmail = '';

$stmt = $conn->prepare("SELECT first_name, last_name, phone, email FROM users WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();

    if ($userData) {
        $userFullName = htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']);
        $userPhone = htmlspecialchars($userData['phone']);
        $userEmail = htmlspecialchars($userData['email']);
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Registration Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c8a47;
            --secondary-color: #f8f9fa;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .form-header {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .file-upload {
            border: 2px dashed #ced4da;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload:hover {
            border-color: var(--primary-color);
            background-color: rgba(44, 138, 71, 0.05);
        }

        .file-upload i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #247a3d;
            border-color: #247a3d;
        }

        .progress {
            height: 10px;
            margin-top: 10px;
        }

        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <h2 class="form-header text-center"><i class="fas fa-user-shield me-2"></i>Farmer Registration Information</h2>

                    <form id="farmerInfoForm" enctype="multipart/form-data">
                        <h4 class="section-title"><i class="fas fa-user-circle me-2"></i>Personal Information</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label required-field">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo $userFullName; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phoneNumber" class="form-label required-field">Phone Number</label>
                                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $userPhone; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $userEmail; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label required-field">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                            </div>
                        </div>

                        <h4 class="section-title"><i class="fas fa-id-card me-2"></i>Identification</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nin" class="form-label required-field">National Identification Number (NIN)</label>
                                <input type="text" class="form-control" id="nin" name="nin" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ninDocument" class="form-label required-field">NIN Document</label>
                                <div class="file-upload" onclick="document.getElementById('ninDocument').click()">
                                    <input type="file" id="ninDocument" name="ninDocument" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" required>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p id="ninDocumentText">Click to upload NIN document (PDF or image)</p>
                                </div>
                                <small class="text-muted">Max file size: 5MB</small>
                            </div>
                        </div>

                        <h4 class="section-title"><i class="fas fa-briefcase me-2"></i>Business Registration</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cacNumber" class="form-label">C.A.C Registration Number</label>
                                <input type="text" class="form-control" id="cacNumber" name="cacNumber">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cacDocument" class="form-label">C.A.C Registration Document</label>
                                <div class="file-upload" onclick="document.getElementById('cacDocument').click()">
                                    <input type="file" id="cacDocument" name="cacDocument" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p id="cacDocumentText">Click to upload C.A.C document (PDF or image)</p>
                                </div>
                                <small class="text-muted">Max file size: 5MB</small>
                            </div>
                        </div>

                        <h4 class="section-title"><i class="fas fa-tractor me-2"></i>Farm Information</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="farmName" class="form-label required-field">Farm Name</label>
                                <input type="text" class="form-control" id="farmName" name="farmName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="farmSize" class="form-label required-field">Farm Size (in hectares)</label>
                                <input type="number" step="0.01" class="form-control" id="farmSize" name="farmSize" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="farmLocation" class="form-label required-field">Farm Location (e.g., Village, LGA)</label>
                                <input type="text" class="form-control" id="farmLocation" name="farmLocation" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="farmState" class="form-label required-field">State of Farm</label>
                                <select class="form-select" id="farmState" name="farmState" required>
                                    <option value="" selected disabled>Select State</option>
                                    <option value="Abia">Abia</option>
                                    <option value="Adamawa">Adamawa</option>
                                    <option value="Akwa Ibom">Akwa Ibom</option>
                                    <option value="Anambra">Anambra</option>
                                    <option value="Bauchi">Bauchi</option>
                                    <option value="Bayelsa">Bayelsa</option>
                                    <option value="Benue">Benue</option>
                                    <option value="Borno">Borno</option>
                                    <option value="Cross River">Cross River</option>
                                    <option value="Delta">Delta</option>
                                    <option value="Ebonyi">Ebonyi</option>
                                    <option value="Edo">Edo</option>
                                    <option value="Ekiti">Ekiti</option>
                                    <option value="Enugu">Enugu</option>
                                    <option value="Gombe">Gombe</option>
                                    <option value="Imo">Imo</option>
                                    <option value="Jigawa">Jigawa</option>
                                    <option value="Kaduna">Kaduna</option>
                                    <option value="Kano">Kano</option>
                                    <option value="Katsina">Katsina</option>
                                    <option value="Kebbi">Kebbi</option>
                                    <option value="Kogi">Kogi</option>
                                    <option value="Kwara">Kwara</option>
                                    <option value="Lagos">Lagos</option>
                                    <option value="Nasarawa">Nasarawa</option>
                                    <option value="Niger">Niger</option>
                                    <option value="Ogun">Ogun</option>
                                    <option value="Ondo">Ondo</option>
                                    <option value="Osun">Osun</option>
                                    <option value="Oyo">Oyo</option>
                                    <option value="Plateau">Plateau</option>
                                    <option value="Rivers">Rivers</option>
                                    <option value="Sokoto">Sokoto</option>
                                    <option value="Taraba">Taraba</option>
                                    <option value="Yobe">Yobe</option>
                                    <option value="Zamfara">Zamfara</option>
                                    <option value="FCT">FCT</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="farmAddress" class="form-label required-field">Full Farm Address</label>
                            <textarea class="form-control" id="farmAddress" name="farmAddress" rows="3" required></textarea>
                        </div>

                        <h4 class="section-title"><i class="fas fa-landmark me-2"></i>Land Ownership</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Land Ownership Type</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="landOwnership" id="owned" value="owned" required>
                                    <label class="form-check-label" for="owned">Owned</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="landOwnership" id="leased" value="leased">
                                    <label class="form-check-label" for="leased">Leased</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="landOwnership" id="rented" value="rented">
                                    <label class="form-check-label" for="rented">Rented</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="landOwnership" id="inherited" value="inherited">
                                    <label class="form-check-label" for="inherited">Inherited</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="landDocument" class="form-label required-field">Land Ownership Document</label>
                                <div class="file-upload" onclick="document.getElementById('landDocument').click()">
                                    <input type="file" id="landDocument" name="landDocument" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" required>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p id="landDocumentText">Click to upload land document (PDF or image)</p>
                                </div>
                                <small class="text-muted">Max file size: 5MB</small>
                            </div>
                        </div>

                        <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Additional Information</h4>
                        <div class="mb-3">
                            <label for="cropsProduced" class="form-label">Main Crops Produced (comma separated)</label>
                            <input type="text" class="form-control" id="cropsProduced" name="cropsProduced">
                        </div>

                        <div class="mb-3">
                            <label for="farmingExperience" class="form-label">Years of Farming Experience</label>
                            <input type="number" class="form-control" id="farmingExperience" name="farmingExperience">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="organicCertified" name="organicCertified">
                            <label class="form-check-label" for="organicCertified">Are you organic certified?</label>
                        </div>

                        <div class="mb-3">
                            <label for="additionalInfo" class="form-label">Additional Information</label>
                            <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3"></textarea>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="termsAgreement" name="termsAgreement" required>
                            <label class="form-check-label required-field" for="termsAgreement">I certify that the information provided is accurate and complete</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <span id="submitText">Submit Information</span>
                                <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            </button>
                        </div>

                        <div class="progress mt-3" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>

                        <div id="formMessage" class="mt-3"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // File upload display
        document.getElementById('ninDocument').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.getElementById('ninDocumentText').textContent = fileName;
        });

        document.getElementById('cacDocument').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.getElementById('cacDocumentText').textContent = fileName;
        });

        document.getElementById('landDocument').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.getElementById('landDocumentText').textContent = fileName;
        });

        // Form submission
        $('#farmerInfoForm').submit(function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = $('#submitBtn');
            const spinner = $('#spinner');
            const submitText = $('#submitText');
            const progressBar = $('.progress');
            const progressBarInner = $('.progress-bar');
            const formMessage = $('#formMessage');

            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.show();
            submitText.text('Submitting...');
            progressBar.show();
            formMessage.empty().removeClass('alert alert-success alert-danger'); // Clear message and styles

            // Create FormData object
            const formData = new FormData(form);

            // AJAX submission
            $.ajax({
                url: 'views/submit_farmer_doc.php', // This is the new backend endpoint
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();

                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            progressBarInner.css('width', percentComplete + '%');
                        }
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);

                        if (data.success) {
                            formMessage.html(`
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ${data.message || 'Information submitted successfully!'}
                                </div>
                            `);

                            // Redirect to awaiting_approval page
                            setTimeout(() => {
                                window.location.href = 'awaiting_approval.php';
                            }, 3000); // Redirect after 3 seconds
                        } else {
                            formMessage.html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    ${data.message || 'Error submitting information. Please try again.'}
                                </div>
                            `);
                        }
                    } catch (e) {
                        console.error("JSON Parse Error:", e);
                        console.error("Response:", response);
                        formMessage.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error processing response from server. Please try again.
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    formMessage.html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error: ${error || 'Unable to connect to server. Please try again later.'}
                        </div>
                    `);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.hide();
                    submitText.text('Submit Information');
                    progressBar.hide();
                    progressBarInner.css('width', '0%');

                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: formMessage.offset().top - 100
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>