<?php

// Logout Page
// Purpose: Log the user out and end their session

// Include configuration (starts session and connects to database)
require_once 'C:\XAMPP NOW\htdocs\dashboard\configuration.php';


// Step 1: Clear all session variables
session_unset();  // Removes all data stored in the current session

// Step 2: Completely destroy the session
session_destroy();  // Ends the session and deletes the session file on the server


// Step 3: Redirect the user back to the login page
header('Location: login.php');
exit;  // Always call exit after a redirect to stop further execution
