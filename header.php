<?php
// Header File - corrected
require_once __DIR__ . '/configuration.php';

// Define e() only if it’s not already declared
if (!function_exists('e')) {
  function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
}

$css_path = 'styles.css'; // Use absolute path from webroot (adjust if needed)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Mini Social</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Link to main stylesheet (cache-busting version) -->
  <link rel="stylesheet" href="<?= e($css_path) ?>?v=2">
</head>

<body>
  <nav class="topnav">
    <a href="dashboard.php">Home</a>

    <?php if (!empty($_SESSION['user_id'])): ?>
      <a href="profile.php?id=<?= e($_SESSION['user_id']) ?>">Profile</a>
      <a href="messages.php">Messages</a>

      <form action="search.php" method="get" class="search-inline" style="margin-left:auto;">
        <input type="search" id="search" name="q" placeholder="Search users...">
      </form>

      <a href="logout.php" class="right">Logout</a>
    <?php else: ?>
      <a href="login.php" class="right">Login</a>
      <a href="register.php" class="right">Register</a>
    <?php endif; ?>
  </nav>

  <main class="container">
