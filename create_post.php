<?php
// creating a posting page
// Handles creating and uploading new posts for the logged-in user.

require_once 'C:\XAMPP NOW\htdocs\dashboard\functions.php';

// Make sure the user is logged in before allowing post creation.
require_login();

// Only run this code when the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the content of the post and the logged-in user's ID
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['User_ID'];

    // Check if the post content is empty
    if ($content === '') {
        $_SESSION['error'] = 'Post content cannot be empty.';
        header('Location: dashboard.php');
        exit;
    }

    // Initialize image name as null (for posts without an image)
    $imageName = null;

    // Check if an image file was uploaded
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        // Allow only specific image file types
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = 'Invalid image type. Please upload a JPEG, PNG, GIF, or WEBP file.';
            header('Location: dashboard.php');
            exit;
        }

        // Restrict file size to 5MB maximum
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Image too large (maximum size is 5MB).';
            header('Location: dashboard.php');
            exit;
        }

        // Generate a unique name for the uploaded image to prevent filename conflicts
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('post_', true) . '.' . $extension;

        // Move the uploaded file to the uploads folder
        move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $imageName);
    }

    // Insert the new post (with or without image) into the database
    $stmt = $pdo->prepare("INSERT INTO posts (User_ID, content, image) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $content, $imageName]);

    // Redirect back to the dashboard after posting
    header('Location: dashboard.php');
    exit;
}

// If the request wasn’t POST, redirect back to the dashboard
header('Location: dashboard.php');
exit;
?>
