
function registerFarmer() {
    const data = {
        firstname: document.getElementById('firstname').value,
        lastname: document.getElementById('LastName').value,
        email: document.getElementById('Email').value,
        phone: document.getElementById('phone').value,
        gender: document.getElementById('gender').value,
        address: document.getElementById('inputAddress').value,
        city: document.getElementById('inputCity').value,
        state: document.getElementById('inputState').value,
        password: document.getElementById('Password').value
    };

    fetch('views/register.view.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data) // send as JSON
    })
    .then(response => response.text())
    .then(result => {
        alert(result); // show success or error message
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Something went wrong.");
    });
}

