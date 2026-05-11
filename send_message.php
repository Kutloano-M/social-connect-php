<?php
// send_message.php

// This script handles sending a new message from one user
// to another. It validates the input and saves the message 
// to the database before redirecting back to the chat.

require_once 'C:\XAMPP NOW\htdocs\dashboard\functions.php';
require_login(); // Ensure only logged-in users can send messages


//Make sure the form was submitted using POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the logged-in user's ID (the sender)
    $sender = $_SESSION['User_ID'];

    // Get and validate the receiver's ID
    $receiver = (int)($_POST['receiver_id'] ?? 0);

    // Clean up the message text
    $body = trim($_POST['body'] ?? '');

    // Validate the message before saving

    // - Receiver must be valid and not the same as the sender
    // - Message body cannot be empty
    if ($receiver <= 0 || $receiver === $sender || $body === '') {
        // If validation fails, go back to the messages page
        header('Location: messages.php');
        exit;
    }

    //Save the message to the database
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, body)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$sender, $receiver, $body]);

    //Redirect back to the conversation
    header('Location: messages.php?with=' . $receiver);
    exit;
}


//If the script is accessed directly (not via POST), just send the user back to the messages page
header('Location: messages.php');
exit;
