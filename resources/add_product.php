<?php
require_once 'auth_check.php';
include 'header.php';

// Database connection and data fetching
include 'DBcon.php'; // Make sure this path is correct

$farmer_id = $_SESSION['user_id'];

// Fetch the farmer's default details from the users table
// Using a simpler query to directly get the details from the users table.
$user_details_query = "SELECT farm_full_address, crops_produced FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $user_details_query);
mysqli_stmt_bind_param($stmt, "i", $farmer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_details = mysqli_fetch_assoc($result);

// Set default values. These fields are guaranteed to exist in the users table.
$default_produce = $user_details['crops_produced'] ?? '';
$default_location = $user_details['farm_full_address'] ?? '';

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include 'topbar.php'; ?>

<div class="container-fluid">
<style>
/* Your existing CSS styles remain the same */
body {
    font-family: Arial, sans-serif;
    background: #f2f2f2;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 700px;
    margin: 30px auto;
    padding: 20px;
}

.card {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h3 {
    text-align: center;
    color: #4CAF50;
    margin-bottom: 20px;
}

label {
    display: block;
    margin: 12px 0 6px;
    font-weight: bold;
}

input, select, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

input[type="file"] {
    padding: 4px;
}

.submit-btn {
    background-color: #4CAF50;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    margin-top: 20px;
    cursor: pointer;
    width: 100%;
}

.submit-btn:hover {
    background-color: #45a049;
}

.preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
}

.preview-container img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    margin-top: 20px;
}

.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

.hidden {
    display: none;
}

/* Style for disabled inputs */
input:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}
</style>

<div class="container">
<div class="card">
<h3>Add New Produce Listing</h3>

<form id="listingForm" enctype="multipart/form-data">
    <label for="produce">Type of Produce <span class="text-danger">*</span></label>
    <input type="text" name="produce" id="produce" 
           value="<?php echo htmlspecialchars($default_produce); ?>" 
           <?php echo !empty($default_produce) ? 'disabled' : ''; ?> 
           placeholder="e.g., Yam, Tomatoes" required />

  <div style="display: flex; gap: 10px; align-items: flex-end;">
        <div style="flex-grow: 1;">
            <label for="quantity_number">Quantity Available <span class="text-danger">*</span></label>
            <input type="text" name="quantity_number" id="quantity_number" placeholder="e.g., 100" required />
        </div>
        <div style="width: 150px;">
            <label for="unit">Units <span class="text-danger">*</span></label>
            <select name="unit" id="unit" required>
                <option value="">Select Unit</option>
                </select>
        </div>
    </div>

    <label for="price">Expected Price per Unit <span class="text-danger">*</span></label>
    <input type="text" name="price" id="price" placeholder="e.g., ₦500" required />

    <label for="available_date">Availability Date</label>
    <input type="date" name="available_date" id="available_date" />

    <label for="location">Location of Farm Produce <span class="text-danger">*</span></label>
    <input type="text" name="location" id="location" 
           value="<?php echo htmlspecialchars($default_location); ?>" 
           <?php echo !empty($default_location) ? 'disabled' : ''; ?> 
           placeholder="e.g., Ibadan, Oyo" required />

    <!-- Rest of your form remains the same -->
    <label for="condition">Condition of Produce</label>
    <select name="condition" id="condition">
        <option value="">Select</option>
        <option value="Fresh">Fresh</option>
        <option value="Dry">Dry</option>
        <option value="Processed">Processed</option>
    </select>

    <label for="allow_visit">Can Buyers Visit the Farm?</label>
    <select name="allow_visit" id="allow_visit">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
    </select>

    <div id="visit_time_field" class="hidden">
        <label for="visit_time">Best Time/Days to Visit</label>
        <input type="text" name="visit_time" id="visit_time" placeholder="e.g., Mon-Fri, 9am-3pm" />
    </div>

    <label for="offer_delivery">Do You Offer Delivery?</label>
    <select name="offer_delivery" id="offer_delivery">
        <option value="No">No</option>
        <option value="Yes">Yes</option>
    </select>

    <div id="delivery_location_field" class="hidden">
        <label for="delivery_location">Delivery Areas (if yes)</label>
        <input type="text" name="delivery_location" id="delivery_location" placeholder="e.g., Within Ibadan" />
    </div>

    <label for="image">Upload Farm Produce Image(s)</label>
    <div class="preview-container" id="imagePreview"></div>
    <input type="file" name="images[]" id="images" multiple accept="image/*" onchange="handleImageUpload(event)" required/>

    <label for="comment">Additional Comments</label>
    <textarea name="comment" id="comment" rows="3" placeholder="Optional notes for buyers"></textarea>

    <button type="submit" class="submit-btn">Submit Listing</button>
</form>
<div id="responseMessage" class="mt-3"></div>
</div>
</div>
</div>
</div>

<?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>

<script>
// Define the units based on produce categories
const produceUnits = {
    // --- General Crop Units ---
    'General': [
        "kg", "tonnes", "bags", "sacks", "baskets", "craters", "pieces", "dozens", "litres"
    ],
    // --- Specific Crop & Tuber Units ---
    'Cereals': [
        "kg", "tonnes", "bags", "sacks", "bushels", "mudu"
    ],
    'Roots/Tubers': [
        "kg", "tonnes", "baskets", "pieces", "heads", "tuber-count"
    ],
    // --- Fruit & Vegetable Units ---
    'Fruits': [
        "kg", "tonnes", "baskets", "craters", "dozens", "pieces"
    ],
    'Vegetables': [
        "kg", "bags", "baskets", "bunch", "pieces", "dozens"
    ],
    'Spices': [
        "kg", "bags", "sacks", "roots/pieces"
    ],
    // --- Livestock Units (Meat/Live Animals) ---
    'Poultry/Ruminants/Pigs': [
        "heads", "pieces", "cages", "dozens", "cartons", "kg (live weight)", "kg (dressed weight)"
    ],
    // --- Dairy/Fluid Units ---
    'Dairy/Aquaculture': [
        "litres", "gallons", "kg", "crates", "pieces/fish"
    ],
    // --- Agroforestry/Tree Crops Units ---
    'Tree Crops': [
        "kg", "tonnes", "bags", "sacks", "pods", "head (of palm)"
    ],
    'Timber/Wood': [
        "pieces", "logs", "cubic meters", "bundles"
    ],
    'Other': [
        "N/A", "Other (specify in comment)"
    ]
};

$(document).ready(function() {
    
    // Function to populate the Units dropdown
    function populateUnitsDropdown(units) {
        const $unitDropdown = $('#unit');
        $unitDropdown.empty().append('<option value="">Select Unit</option>'); // Clear and add default

        // Combine all units from the relevant categories (simplified for this general list)
        // For a simple start, we can just use the 'General' list, or combine all.
        // Let's combine all unique units for maximum flexibility.
        const allUnits = new Set();
        for (const category in produceUnits) {
            produceUnits[category].forEach(unit => allUnits.add(unit));
        }

        // Convert Set back to Array and sort alphabetically
        const sortedUnits = Array.from(allUnits).sort();

        sortedUnits.forEach(unit => {
            $unitDropdown.append(`<option value="${unit}">${unit}</option>`);
        });
    }

    // Populate the dropdown on page load
    populateUnitsDropdown();


    // Toggle visibility of visit time field
    $('#allow_visit').change(function() {
        if ($(this).val() === 'Yes') {
            $('#visit_time_field').removeClass('hidden');
        } else {
            $('#visit_time_field').addClass('hidden');
            $('#visit_time').val(''); // Clear value if hidden
        }
    });

    // Toggle visibility of delivery location field
    $('#offer_delivery').change(function() {
        if ($(this).val() === 'Yes') {
            $('#delivery_location_field').removeClass('hidden');
        } else {
            $('#delivery_location_field').addClass('hidden');
            $('#delivery_location').val(''); // Clear value if hidden
        }
    });

    // Image preview function
    window.handleImageUpload = function(event) {
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = '';
        const files = event.target.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    }

    $('#listingForm').submit(function(e) {
        e.preventDefault();
        $('#responseMessage').removeClass('alert-danger alert-success').text('');

        let isValid = true;
        const produce = $('#produce').val().trim();
        // --- UPDATED: Get number and unit separately ---
        const quantityNumber = $('#quantity_number').val().trim();
        const unit = $('#unit').val();
        const price = $('#price').val().trim();
        const location = $('#location').val().trim();
        const images = $('#images')[0].files;

        // --- Core Logic to Submit Disabled Fields ---
        const isProduceDisabled = $('#produce').prop('disabled');
        const isLocationDisabled = $('#location').prop('disabled');

        if (isProduceDisabled) {
            $('#produce').prop('disabled', false);
        }
        if (isLocationDisabled) {
            $('#location').prop('disabled', false);
        }
        // ------------------------------------------

        // --- Updated Validation ---
        if (produce === '') {
            $('#responseMessage').addClass('alert-danger').text('Please enter the type of produce.');
            isValid = false;
        } 
        // Validation now checks the separate Quantity Number and Unit fields
        else if (quantityNumber === '' || isNaN(quantityNumber) || Number(quantityNumber) <= 0) { 
            $('#responseMessage').addClass('alert-danger').text('Please enter a valid numerical quantity greater than 0.');
            isValid = false;
        }
        else if (unit === '') {
            $('#responseMessage').addClass('alert-danger').text('Please select a unit of measure for the quantity.');
            isValid = false;
        } 
        else if (price === '' || isNaN(price.replace('₦', '').replace(',', '').replace(/\s/g, ''))) {
            $('#responseMessage').addClass('alert-danger').text('Please enter a valid numerical price (e.g., 500).');
            isValid = false;
        } else if (location === '') {
            $('#responseMessage').addClass('alert-danger').text('Please enter the location of the produce.');
            isValid = false;
        } else if (images.length === 0) {
            $('#responseMessage').addClass('alert-danger').text('Please upload at least one image.');
            isValid = false;
        }

        if (isValid) {
            // --- NEW: Combine quantity_number and unit into one field for backend (if necessary) ---
            // If your backend expects a single 'quantity' field, you'll need to create it in the formData.
            // Assuming your backend is updated to expect 'quantity_number' and 'unit', the following is NOT needed:
            // formData.append('quantity', quantityNumber + ' ' + unit);
            // If you DO need a single field named 'quantity', you must *manually* add it:

            var formData = new FormData(this);
            // Important: Remove the original 'quantity' field if it was empty and causing issues
            formData.delete('quantity'); 
            // Append the combined quantity and unit to simulate the old 'quantity' field if your backend requires it
            formData.append('quantity', quantityNumber + ' ' + unit); 
            // The original 'quantity_number' and 'unit' fields are still sent because they are in the form, 
            // unless your PHP logic only accepts the first one named 'quantity'. Adjust as needed for your backend.

            $.ajax({
                url: 'views/process_add_produce.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#responseMessage').removeClass('alert-danger alert-success');
                    if (response.status === 'success') {
                        $('#responseMessage').addClass('alert-success').text(response.message);
                        $('#listingForm')[0].reset();
                        $('#imagePreview').empty();
                        $('#visit_time_field').addClass('hidden');
                        $('#delivery_location_field').addClass('hidden');
                        
                        // Re-disable fields after successful submission
                        if (isProduceDisabled) {
                            $('#produce').prop('disabled', true);
                        }
                        if (isLocationDisabled) {
                            $('#location').prop('disabled', true);
                        }
                    } else {
                        $('#responseMessage').addClass('alert-danger').text(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error submitting listing:", error);
                    $('#responseMessage').removeClass('alert-danger alert-success').addClass('alert-danger').text('An error occurred while submitting the listing.');
                },
                complete: function() {
                    // Always re-disable fields in the complete handler
                    if (isProduceDisabled) {
                        $('#produce').prop('disabled', true);
                    }
                    if (isLocationDisabled) {
                        $('#location').prop('disabled', true);
                    }
                }
            });
        } else {
            // Re-disable fields if validation failed
            if (isProduceDisabled) {
                $('#produce').prop('disabled', true);
            }
            if (isLocationDisabled) {
                $('#location').prop('disabled', true);
            }
        }
    });
});
</script>