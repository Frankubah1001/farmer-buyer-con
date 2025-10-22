<?php 
require_once 'auth_check.php';
include 'header.php'; 
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
        <style>
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
        </style>

            <div class="container">
                <div class="card">
                    <h3>Add New Produce Listing</h3>

                    <form id="listingForm" enctype="multipart/form-data">
                        <label for="produce">Type of Produce <span class="text-danger">*</span></label>
                        <input type="text" name="produce" id="produce" placeholder="e.g., Yam, Tomatoes" required />

                        <label for="quantity">Quantity Available <span class="text-danger">*</span></label>
                        <input type="text" name="quantity" id="quantity" placeholder="e.g., 10 bags" required />

                        <label for="price">Expected Price per Unit <span class="text-danger">*</span></label>
                        <input type="text" name="price" id="price" placeholder="e.g., ₦500" required />

                        <label for="available_date">Availability Date</label>
                        <input type="date" name="available_date" id="available_date" />

                        <label for="location">Location of Farm Produce <span class="text-danger">*</span></label>
                        <input type="text" name="location" id="location" placeholder="e.g., Ibadan, Oyo" required />

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
    $(document).ready(function() {
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
        function handleImageUpload(event) {
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
            const quantity = $('#quantity').val().trim();
            const price = $('#price').val().trim();
            const location = $('#location').val().trim();
            const images = $('#images')[0].files;

            if (produce === '') {
                $('#responseMessage').addClass('alert-danger').text('Please enter the type of produce.');
                isValid = false;
            } else if (quantity === '' || isNaN(quantity)) {
                $('#responseMessage').addClass('alert-danger').text('Please enter a valid quantity.');
                isValid = false;
            } else if (price === '' || isNaN(price.replace('₦', '').replace(',', ''))) {
                $('#responseMessage').addClass('alert-danger').text('Please enter a valid price.');
                isValid = false;
            } else if (location === '') {
                $('#responseMessage').addClass('alert-danger').text('Please enter the location of the produce.');
                isValid = false;
            } else if (images.length === 0) {
                $('#responseMessage').addClass('alert-danger').text('Please upload at least one image.');
                isValid = false;
            }

            if (isValid) {
                var formData = new FormData(this);

                $.ajax({
                    url: 'views/process_add_produce.php', // Adjust this path to match your backend file location
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
                        } else {
                            $('#responseMessage').addClass('alert-danger').text(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting listing:", error);
                        $('#responseMessage').removeClass('alert-danger alert-success').addClass('alert-danger').text('An error occurred while submitting the listing.');
                    }
                });
            }
        });
    });
</script>