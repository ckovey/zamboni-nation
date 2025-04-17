<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    
    // Process the data (e.g., save to database, send an email, etc.)
    echo "Thank you, $name. Your email address, $email, has been submitted.";
} else {
    echo "Invalid request.";
}
?>