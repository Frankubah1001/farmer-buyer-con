<?php 
require_once 'auth_check.php';
include 'header.php'; 
?><style>
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

    .btn-container {
        margin-top: 20px;
        text-align: center;
    }

    .edit-btn, .save-btn, .cancel-btn {
        background-color: #2196F3;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        margin: 5px;
    }

    .save-btn {
        background-color: #4CAF50;
    }

    .cancel-btn {
        background-color: #f44336;
    }

    .edit-btn:hover {
        background-color: #1976D2;
    }

    .save-btn:hover {
        background-color: #45a049;
    }

    .cancel-btn:hover {
        background-color: #d32f2f;
    }

    .profile-picture-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ccc;
        margin-bottom: 10px;
    }

    .hidden {
        display: none;
    }
</style>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">

  <div class="container">
    <div class="card">
      <h3>My Profile</h3>

      <div class="profile-picture-container">
        <img src="" alt="Profile Picture" id="profilePic" class="profile-picture">
        <label for="uploadPic" class="btn-container">Upload New Picture</label>
        <input type="file" name="profile_picture" id="uploadPic" class="file hidden" accept="image/*">
      </div>

      <form id="profileForm" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6">
          <label>First Name</label>
          <input type="text" name="firstname" id="firstname" readonly />
          </div>
          <div class="col-md-6">
          <label>Last Name</label>
          <input type="text" name="lastname" id="lastname" readonly />
          </div>

        </div>
       

        <label>Phone</label>
        <input type="tel" name="phone" id="phone" readonly />

        <label>State</label>
        <select name="state_id" id="state">
          <option value="">Select State</option>
          <?php
          // Fetch states from the database and populate the options
          $statesResult = mysqli_query($conn, "SELECT state_id, state_name FROM states ORDER BY state_name ASC");
          while ($row = mysqli_fetch_assoc($statesResult)) {
            echo '<option value="' . $row['state_id'] . '">' . $row['state_name'] . '</option>';
          }
          mysqli_free_result($statesResult);
          ?>
        </select>

        <label>Local Government Area (LGA)</label>
        <select name="city_id" id="lga">
          <option value="">Select LGA</option>
          <?php
          // LGAs will likely depend on the selected state, so initially, this can be empty
          ?>
        </select>

        <label>Address</label>
        <input type="text" name="address" id="address" readonly />

        <div class="btn-container">
          <button type="button" class="edit-btn" id="editBtn">Edit</button>
          <button type="button" class="save-btn hidden" id="saveBtn">Save</button>
          <button type="button" class="cancel-btn hidden" id="cancelBtn">Cancel</button>
        </div>
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
  const firstnameInput = document.getElementById('firstname');
  const lastnameInput = document.getElementById('lastname');
  const phoneInput = document.getElementById('phone');
  const stateInput = document.getElementById('state');
  const lgaInput = document.getElementById('lga');
  const addressInput = document.getElementById('address');
  const editBtn = document.getElementById('editBtn');
  const saveBtn = document.getElementById('saveBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const profileForm = document.getElementById('profileForm');
  const uploadPicInput = document.getElementById('uploadPic');
  const profilePicImg = document.getElementById('profilePic');

  let originalData = {};
  let isEditing = false; // Track if the form is in edit mode

  window.onload = () => {
    fetch('views/get_profile_info.view.php')
        .then(response => response.json())
        .then(data => {
            if (data && !data.error) {
                firstnameInput.value = data.firstname || '';
                lastnameInput.value = data.lastname || '';
                phoneInput.value = data.phone || '';
                addressInput.value = data.address || '';
                profilePicImg.src = data.profile_picture || 'uploads/profile_pics/';
                originalData = { ...data };

          // Set selected values for state and LGA dropdowns
          if (data.state_id) {
            stateInput.value = data.state_id;
            // If you implement dynamic LGA loading, you'd trigger it here
          }
          if (data.city_id) {
            lgaInput.value = data.city_id;
          }
        } else if (data.error) {
          console.error('Error fetching profile info:', data.error);
          document.getElementById('responseMessage').innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('responseMessage').innerHTML = '<div class="alert alert-danger">Error fetching profile information.</div>';
      });
  };

  stateInput.addEventListener('change', function() {
    const selectedStateId = this.value;
    lgaInput.innerHTML = '<option value="">Loading LGAs...</option>'; // Show a loading message

    if (selectedStateId) {
        fetch(`views/get_lgas.view.php?state_id=${selectedStateId}`)
            .then(response => response.json())
            .then(lgas => {
                lgaInput.innerHTML = '<option value="">Select LGA</option>';
                if (lgas && lgas.length > 0) {
                    lgas.forEach(lga => {
                        const option = document.createElement('option');
                        option.value = lga.id;
                        option.textContent = lga.name;
                        lgaInput.appendChild(option);
                    });
                } else {
                    lgaInput.innerHTML = '<option value="">No LGAs found</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching LGAs:', error);
                lgaInput.innerHTML = '<option value="">Error loading LGAs</option>';
            });
    } else {
        lgaInput.innerHTML = '<option value="">Select LGA</option>'; // Reset LGA dropdown if no state selected
    }
});

  editBtn.addEventListener('click', () => {
    isEditing = true;
    firstnameInput.readOnly = false;
    lastnameInput.readOnly = false;
    phoneInput.readOnly = false;
    addressInput.readOnly = false;
    stateInput.disabled = false; // Enable state dropdown
    lgaInput.disabled = false;   // Enable LGA dropdown

    editBtn.classList.add('hidden');
    saveBtn.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
  });

  cancelBtn.addEventListener('click', () => {
    isEditing = false;
    firstnameInput.value = originalData.firstname || '';
    lastnameInput.value = originalData.lastname || '';
    phoneInput.value = originalData.phone || '';
    addressInput.value = originalData.address || '';
    profilePicImg.src = originalData.profile_picture || 'uploads/profile_pics/';

    // Reset state and LGA to original values (you might need to store original IDs)
    if (originalData.state_id) {
      stateInput.value = originalData.state_id;
    } else {
      stateInput.value = ''; // Or the default "Select State" value
    }
    if (originalData.city_id) {
      lgaInput.value = originalData.city_id;
    } else {
      lgaInput.value = ''; // Or the default "Select LGA" value
    }

    firstnameInput.readOnly = true;
    lastnameInput.readOnly = true;
    phoneInput.readOnly = true;
    addressInput.readOnly = true;
    stateInput.disabled = true;  // Disable state dropdown
    lgaInput.disabled = true;    // Disable LGA dropdown

    editBtn.classList.remove('hidden');
    saveBtn.classList.add('hidden');
    cancelBtn.classList.add('hidden');
  });
  
  saveBtn.addEventListener('click', function(e) {
    e.preventDefault(); // Prevent any default button behavior
    console.log('Save button DIRECTLY clicked!');

    if (isEditing) {
        console.log('isEditing is TRUE - attempting to save.');
        const formData = new FormData(profileForm);
        const profilePicture = uploadPicInput.files[0];

        if (profilePicture) {
            formData.append('profile_picture', profilePicture);
        }
        formData.append('state_id', stateInput.value);
        formData.append('city_id', lgaInput.value);

        console.log('Form Data:');
        for (const pair of formData.entries()) {
            console.log(pair[0]+ ', ' + pair[1]);
        }

        fetch('update_info.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log('Response from update_info.php:', data);
            document.getElementById('responseMessage').innerHTML = data;
            if (data.includes('success')) {
                isEditing = false;
                fetch('views/get_profile_info.view.php')
                    .then(res => res.json())
                    .then(updatedData => {
                        console.log('Updated Data:', updatedData);
                        if (updatedData && !updatedData.error) {
                            firstnameInput.value = updatedData.firstname || '';
                            lastnameInput.value = updatedData.lastname || '';
                            phoneInput.value = updatedData.phone || '';
                            addressInput.value = updatedData.address || '';
                            profilePicImg.src = updatedData.profile_picture || 'views/uploads/profile_pics/';
                            originalData = { ...updatedData };

                            if (updatedData.state_id) {
                                stateInput.value = updatedData.state_id;
                            } else {
                                stateInput.value = '';
                            }
                            if (updatedData.city_id) {
                                lgaInput.value = updatedData.city_id;
                            } else {
                                lgaInput.value = '';
                            }

                            firstnameInput.readOnly = true;
                            lastnameInput.readOnly = true;
                            phoneInput.readOnly = true;
                            addressInput.readOnly = true;
                            stateInput.disabled = true;
                            lgaInput.disabled = true;
                            editBtn.classList.remove('hidden');
                            saveBtn.classList.add('hidden');
                            cancelBtn.classList.add('hidden');
                        } else if (updatedData.error) {
                            console.error('Error fetching updated profile info:', updatedData.error);
                            document.getElementById('responseMessage').innerHTML = `<div class="alert alert-danger">${updatedData.error}</div>`;
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching updated profile:', err);
                        document.getElementById('responseMessage').innerHTML = '<div class="alert alert-danger">Error updating profile. Please try again.</div>';
                    });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('responseMessage').innerHTML = '<div class="alert alert-danger">Error updating profile. Please try again.</div>';
        });
    } else {
        console.log('isEditing is FALSE - Save should not be active.');
    }
}); 

  uploadPicInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        profilePicImg.src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  });
</script>
