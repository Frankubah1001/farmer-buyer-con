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
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
    }

    .card {
        max-width: 700px;
        margin: 0 auto;
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
      <h3>Continue Your Registration Process</h3>

      <form id="listingForm" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="name" id="fullname" readonly />

        <label>Phone / WhatsApp Number</label>
        <input type="tel" name="phone" id="phone" readonly />

        <label>State</label>
        <input type="text" name="state" id="state" readonly />

        <label>Local Government Area (LGA)</label>
        <input type="text" name="lga" id="lga" readonly />

        <label>Farm Address / Landmark</label>
        <input type="text" name="address" required />

        <label>Type of Produce</label>
        <input type="text" name="produce" placeholder="e.g., Yam, Tomatoes" required />

        <label>Quantity Available</label>
        <input type="text" name="quantity" placeholder="e.g., 10 bags" required />

        <label>Expected Price per Unit</label>
        <input type="text" name="price" placeholder="e.g., ₦500 per bag" />

        <label>Availability Date</label>
        <input type="date" name="date" />

        <label>Produce Condition</label>
        <select name="condition">
          <option value="">Select</option>
          <option value="Fresh">Fresh</option>
          <option value="Dry">Dry</option>
          <option value="Processed">Processed</option>
        </select>

        <label>Can Buyers Visit the Farm?</label>
        <select name="visit" id="visitSelect">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>

        <div id="visitTimeField">
          <label>Best Time/Days to Visit</label>
          <input type="text" name="visit_time" placeholder="e.g., Mon-Fri, 9am-3pm" />
        </div>

        <label>Do You Offer Delivery?</label>
        <select name="delivery" id="deliverySelect">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>

        <div id="deliveryAreaField">
          <label>Delivery Areas (if yes)</label>
          <input type="text" name="delivery_area" />
        </div>

        <label>Upload Passport Photo</label>
        <div class="preview-container" id="imagePreview"></div>
        <input type="file" name="photos[]" id="photos" multiple accept="image/*" onchange="handleImageUpload(event)" />

        <label>Special Offers or Notes</label>
        <textarea name="notes" rows="3" placeholder="Optional..."></textarea>

        <button type="submit" class="submit-btn">Submit Listing</button>
      </form>
      <div id="responseMessage"></div>
    </div>
  </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>
<script>
  function handleImageUpload(event) {
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const previewContainer = document.getElementById('imagePreview');
    const files = event.target.files;

    previewContainer.innerHTML = '';

    if (!files || files.length === 0) {
      previewContainer.innerHTML = '<p class="no-images">No images selected</p>';
      return;
    }

    Array.from(files).forEach((file, index) => {
      // Validate file type
      if (!file.type.match('image.*')) {
        previewContainer.innerHTML +=
            `<p class="error">File ${index+1}: ${file.name} is not an image</p>`;
        return;
      }

      // Validate file size
      if (file.size > MAX_FILE_SIZE) {
        previewContainer.innerHTML +=
            `<p class="error">File ${index+1}: ${file.name} is too large (max 5MB)</p>`;
        return;
      }

      const reader = new FileReader();

      reader.onload = function(e) {
        const previewElement = document.createElement('div');
        previewElement.className = 'preview-item';
        previewElement.innerHTML = `
            <img src="${e.target.result}" alt="Preview" />
            <span>${file.name} (${Math.round(file.size/1024)}KB)</span>
            <button onclick="removePreview(this)">×</button>
        `;
        previewContainer.appendChild(previewElement);
      };

      reader.readAsDataURL(file);
    });
}

// Add remove functionality
function removePreview(button) {
    const previewItem = button.parentElement;
    previewItem.remove();
}

// Add to your CSS:
.no-images { color: #999; font-style: italic; }
.error { color: #d9534f; }
.preview-item button {
    background: #d9534f;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    margin-top: 5px;
}
</script>
<script>
    function redirectToDashboard() {
    console.log('redirectToDashboard function called');
    window.location.href = "farmersDashboard.php";
    console.log('Attempting to redirect to farmersDashboard.php');
  }

    window.onload = () => {
      fetch('get_farmer_info.php')
        .then(res => res.json())
        .then(data => {
          document.getElementById('fullname').value = data.fullname || '';
          document.getElementById('phone').value = data.phone || '';
          document.getElementById('state').value = data.state || '';
          document.getElementById('lga').value = data.lga || '';

          // Initial check on page load
          toggleVisitTimeField();
          toggleDeliveryAreaField();
        })
        .catch(error => console.error('Error:', error));
    };

    const visitSelect = document.getElementById('visitSelect');
    const visitTimeField = document.getElementById('visitTimeField');
    const deliverySelect = document.getElementById('deliverySelect');
    const deliveryAreaField = document.getElementById('deliveryAreaField');

    function toggleVisitTimeField() {
      if (visitSelect.value === 'No') {
        visitTimeField.classList.add('hidden');
      } else {
        visitTimeField.classList.remove('hidden');
      }
    }

    function toggleDeliveryAreaField() {
      if (deliverySelect.value === 'No') {
        deliveryAreaField.classList.add('hidden');
      } else {
        deliveryAreaField.classList.remove('hidden');
      }
    }

    visitSelect.addEventListener('change', toggleVisitTimeField);
    deliverySelect.addEventListener('change', toggleDeliveryAreaField);

    document.getElementById('listingForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('views/morefarmerinfo.view.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        document.getElementById('responseMessage').innerHTML = data;
        // Optionally reset form after successful submission
      //   if (data.includes('success')) {
      //     document.getElementById('listingForm').reset();
      //     document.getElementById('imagePreview').innerHTML = '';
      //   }
      // }) // Redirect after 3 seconds
      //           //   setTimeout(() => {
      //           //     window.location.href = 'farmersDashboard.php';
      //           //   }, 3000);
      //           // })
      })
      .catch(error => {
        document.getElementById('responseMessage').innerHTML =
          '<div class="alert alert-danger">Error submitting form. Please try again.</div>';
      });
    });
</script>