<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    
    // Sanitize and validate school data
    $schoolData = [
        'name' => sanitize_input($_POST['school']['name']),
        'address' => sanitize_input($_POST['school']['address']),
        'contact' => sanitize_input($_POST['school']['contact']),
        'email' => sanitize_input($_POST['school']['email'])
    ];

    // Sanitize and validate admin data
    $adminData = [
        'first_name' => sanitize_input($_POST['admin']['first_name']),
        'last_name' => sanitize_input($_POST['admin']['last_name']),
        'email' => sanitize_input($_POST['admin']['email']),
        'username' => sanitize_input($_POST['admin']['username']),
        'password' => $_POST['admin']['password']
    ];

    // Validate email addresses
    if (!validate_email($schoolData['email']) || !validate_email($adminData['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address format'
        ]);
        exit;
    }

    // Validate phone number
    if (!is_valid_phone($schoolData['contact'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid phone number format'
        ]);
        exit;
    }

    // Attempt registration
    if ($auth->register($schoolData, $adminData)) {
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ]);
    }
}
?>
