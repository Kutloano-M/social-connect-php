<?php
// profile.php

// This page displays a user's profile, including their
// personal information (name, profile picture, join date)
// and all the posts they've made on the platform.

require_once 'C:\XAMPP NOW\htdocs\dashboard\functions.php'; // Load helper functions
require_login(); // Ensure the user is logged in before accessing the profile


// Determine which profile to show

// If a profile ID is provided in the URL (?id=...), show that user’s profile.
// Otherwise, show the currently logged-in user’s profile.
$profile_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['User_ID'];

// Fetch the user's profile information from the database
$profile = get_user_by_id($pdo, $profile_id);

// If the user doesn't exist, show an error
if (!$profile) {
    die('User not found.');
}


// Retrieve all posts made by this user
$stmt = $pdo->prepare("
    SELECT * 
    FROM posts 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$profile_id]);
$posts = $stmt->fetchAll();



// Display the profile page
include 'header.php';
?>

<div class="profile">

  <!-- Profile Picture -->
  <img 
    src="<?= e($profile['profile_pic'] ? 'uploads/' . $profile['profile_pic'] : 'uploads/default.png') ?>" 
    width="150" 
    alt="profile picture"
  >

  <!-- User Details -->
  <h2><?= e($profile['full_name']) ?></h2>
  <p>Joined on <?= e($profile['created_at']) ?></p>

  <!-- Edit Profile Option -->
  <?php if ($profile_id === $_SESSION['User_ID']): ?>
    <p><a href="edit_profile.php">Edit Profile</a></p>
  <?php endif; ?>

  <!-- User's Posts -->
  <h3>Posts</h3>

  <?php if ($posts): ?>
    <?php foreach ($posts as $post): ?>
      <article class="user-post">
        <time><?= e($post['created_at']) ?></time>
        <div><?= nl2br(e($post['content'])) ?></div>

        <?php if ($post['image']): ?>
          <img 
            src="uploads/<?= e($post['image']) ?>" 
            alt="post image" 
            style="max-width:300px;"
          >
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  <?php else: ?>
    <p>This user hasn’t made any posts yet.</p>
  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
