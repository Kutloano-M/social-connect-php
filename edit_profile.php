<?php
// edit_profile.php

// This page allows a logged-in user to edit their profile
// information — specifically their display name and profile picture.


require_once 'C:\XAMPP NOW\htdocs\dashboard\functions.php';
require_login(); // Only logged-in users can access this page


// Fetch the currently logged-in user's information

$user = get_user_by_id($pdo, $_SESSION['User_ID']);
$errors = [];


// Handle form submission when user updates their profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and clean the submitted full name
    $full_name = trim($_POST['full_name'] ?? '');

    // Validation: Make sure the name is not empty
    if ($full_name === '') {
        $errors[] = 'Display name cannot be empty.';
    }

    // Start with the current profile picture name (in case the user doesn’t upload a new one)
    $profilePicName = $user['profile_pic'];

    //  Handle profile picture upload (if provided)
    if (!empty($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_pic'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        // Validate the uploaded image type
        if (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
            $errors[] = 'Invalid profile image type. Please upload a JPEG, PNG, GIF, or WEBP file.';
        }
        // Check file size (maximum 2MB)
        elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Profile image too large (maximum size is 2MB).';
        }
        else {
            // Generate a unique file name to avoid overwriting other images
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $profilePicName = uniqid('prof_', true) . '.' . $extension;

            // Move uploaded file into the "uploads" directory
            move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $profilePicName);
        }
    }

    // Update the user's information in the database
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET full_name = ?, profile_pic = ? 
            WHERE UserID = ?
        ");
        $stmt->execute([$full_name, $profilePicName, $user['UserID']]);

        // Redirect to the user's profile page after saving changes
        header('Location: profile.php?id=' . $user['UserID']);
        exit;
    }
}


// Display the Edit Profile form
include 'header.php';
?>

<h2>Edit Profile</h2>

<!-- Display validation errors, if any -->
<?php if ($errors): ?>
  <div class="errors">
    <?php foreach ($errors as $err): ?>
      <p><?= e($err) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Profile Edit Form -->
<form method="post" enctype="multipart/form-data">
  <label>
    Display Name:
    <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required>
  </label>

  <label>
    Profile Picture:
    <input type="file" name="profile_pic" accept="image/*">
  </label>

  <button type="submit">Save Changes</button>
</form>

<?php include 'footer.php'; ?>
