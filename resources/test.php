<?php
// Plain password (e.g. from a signup form)
$password = "admin123";

// Generate a hash (default algorithm is BCRYPT, cost ~10)
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Plain password: " . $password . "<br>";
echo "Hashed password: " . $hash;
?>
