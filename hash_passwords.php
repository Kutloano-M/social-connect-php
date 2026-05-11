<?php
// Script to hash existing plain text passwords

require_once 'configuration.php';

try {
    // Get all users
    $stmt = $pdo->query("SELECT UserID, password FROM users");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        // Check if password is already hashed (starts with $2y$)
        if (!password_get_info($user['password'])['algo']) {
            // Password is plain text, hash it
            $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

            // Update the database
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE UserID = ?");
            $updateStmt->execute([$hashedPassword, $user['UserID']]);

            echo "Updated password for UserID: " . $user['UserID'] . "\n";
        } else {
            echo "Password for UserID: " . $user['UserID'] . " is already hashed.\n";
        }
    }

    echo "Password hashing complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
