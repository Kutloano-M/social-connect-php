<?php
/**
 * fetch_messages.php
 *
 * This file handles fetching chat messages between two users.
 * It expects a `receiver_id` parameter and returns all messages
 * between the logged-in user and that receiver in JSON format.
 */

session_start();
require 'C:\XAMPP NOW\htdocs\dashboard\configuration.php';

// --- Step 1: Check user authentication ---
if (!isset($_SESSION['User_ID'])) {
    // User not logged in
    http_response_code(403);
    echo json_encode(["error" => "You must be logged in to view messages."]);
    exit;
}

$current_user = $_SESSION['User_ID'];

// --- Step 2: Validate receiver_id from the request ---
$receiver_id = intval($_GET['receiver_id'] ?? 0);
if ($receiver_id <= 0) {
    // Invalid or missing receiver ID, return empty messages
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// --- Step 3: Fetch all messages between the two users ---
try {
    $query = "
        SELECT 
            m.id, 
            m.sender_id, 
            m.receiver_id, 
            m.body,
            m.created_at, 
            u.full_name AS sender_name
        FROM messages AS m
        INNER JOIN users AS u ON m.sender_id = u.UserID
        WHERE 
            (m.sender_id = :current_user AND m.receiver_id = :receiver_id)
            OR 
            (m.sender_id = :receiver_id AND m.receiver_id = :current_user)
        ORDER BY m.created_at ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':current_user' => $current_user,
        ':receiver_id' => $receiver_id
    ]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Step 4: Return messages as JSON ---
    header('Content-Type: application/json');
    echo json_encode($messages);

} catch (PDOException $e) {
    // Handle any database-related errors gracefully
    http_response_code(500);
    echo json_encode(["error" => "Database error occurred: " . $e->getMessage()]);
}
?>
