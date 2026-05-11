<?php
// search.php

// This page allows logged-in users to search for other users
// by their name or email address. The results are displayed
// as a simple list of clickable profile links.

require_once 'C:\XAMPP NOW\htdocs\dashboard\functions.php';
require_login(); // Only logged-in users can search for others


//Capture the search query from the URL (?q=...)
$q = trim($_GET['q'] ?? '');  // Trim spaces and handle missing input
$results = [];                // Initialize empty results array

// If a search query was provided, look for matching users
if ($q !== '') {
    // Prepare the search term for SQL LIKE syntax
    $like = "%$q%";

    // Search for users by full name OR email address
    $stmt = $pdo->prepare("
        SELECT UserID, full_name, email, profile_pic
        FROM users
        WHERE full_name LIKE ? OR email LIKE ?
        LIMIT 20
    ");
    $stmt->execute([$like, $like]);
    $results = $stmt->fetchAll();
}


// Display the search results page
include 'header.php';
?>

<!-- Search form -->
<form method="get" action="search.php" class="search-form">
  <input type="text" name="q" placeholder="Search for users by name or email" value="<?= e($q) ?>" required>
  <button type="submit">Search</button>
</form>


<h2>Search results for: <em><?= e($q) ?></em></h2>

<!-- Show message if no results were found -->
<?php if (empty($results)): ?>
  <p>No users found matching your search.</p>

<!-- Display list of found users -->
<?php else: ?>
  <ul class="search-results">
    <?php foreach ($results as $user): ?>
      <li>
        <a href="profile.php?id=<?= e($user['UserID']) ?>">
          <!-- User profile picture -->
          <img 
            src="<?= e($user['profile_pic'] ? 'uploads/' . $user['profile_pic'] : 'uploads/default.png') ?>" 
            width="40" 
            alt="Profile picture of <?= e($user['full_name']) ?>"
          >
          <!-- User name and email -->
          <?= e($user['full_name']) ?> (<?= e($user['email']) ?>)
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php include 'footer.php'; ?>
