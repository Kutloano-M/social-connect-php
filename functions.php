<?php
// functions file
// Purpose: Store reusable functions for the app

// Include the database configuration (so we can use $pdo)
require_once 'configuration.php';


// Check if a user is currently logged in
// Returns true if 'user_id' exists in the session
function is_logged_in() {
    return isset($_SESSION['User_ID']);
}


// Redirect the user to the login page if not logged in
// Use this on pages that require authentication
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php'); // Send user to login page
        exit; // Stop script execution after redirect
    }
}


// Get user details by their ID
// Parameters:
//   - $pdo: Database connection object
//   - $id:  User ID to look up
// Returns: An associative array of user info or false if not found
function get_user_by_id($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT UserID, full_name, email, profile_pic, created_at 
        FROM users 
        WHERE UserID = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}


// Get user details by their email address
// Parameters:
//   - $pdo: Database connection object
//   - $email: Email to search for
// Returns: User data (if exists) or false if no match
function get_user_by_email($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}
?>
