<?php

// Dashboard Page
// Purpose: Display the main feed and allow users to post updates

// Include helper functions (login check, user fetching, etc.)
require_once 'functions.php';

// Make sure the user is logged in — if not, they’ll be redirected to login.php
require_login();

// Step 1: Get details of the currently logged-in user
$current = get_user_by_id($pdo, $_SESSION['User_ID']);


// Step 2: Fetch recent posts (latest first)
// - Each post shows who posted it, their profile picture, and content
$stmt = $pdo->prepare("
    SELECT p.*, u.full_name, u.profile_pic
    FROM posts p
    JOIN users u ON p.User_ID = u.UserID
    ORDER BY p.created_at DESC
    LIMIT 50
");
$stmt->execute();
$posts = $stmt->fetchAll();

// Include header (navigation bar and top section)
include 'header.php';
?>

<!-- ---------------------------------------------
     Dashboard Interface
---------------------------------------------- -->
<h2>Welcome, <?= e($current['full_name']) ?></h2>

<!-- ---------------------------------------------
     Section: Create a new post
---------------------------------------------- -->
<section class="create-post">
  <form action="create_post.php" method="post" enctype="multipart/form-data" id="postForm">
    <!-- Post content -->
    <textarea name="content" placeholder="What's on your mind?" required></textarea>

    <!-- Optional image upload -->
    <input type="file" name="image" id="postImage" accept="image/*">

    <!-- Preview image before posting -->
    <img id="postPreview" style="display:none; max-width: 200px;" alt="Preview">

    <!-- Submit the post -->
    <button type="submit">Post</button>
  </form>
</section>


<!-- ---------------------------------------------
     Section: Display all posts (the timeline)
---------------------------------------------- -->
<section class="timeline">
  <?php foreach ($posts as $p): ?>
    <article class="post">
      <header>
        <!-- User profile picture -->
        <img 
          class="avatar" 
          src="<?= e($p['profile_pic'] ? 'uploads/' . $p['profile_pic'] : 'uploads/default.png') ?>" 
          alt="avatar" 
          width="48"
        >

        <!-- User’s full name -->
        <strong><?= e($p['full_name']) ?></strong>

        <!-- Post date/time -->
        <time><?= e($p['created_at']) ?></time>
      </header>

      <!-- Post text content -->
      <div class="content">
        <?= nl2br(e($p['content'])) ?>
      </div>

      <!-- If a post has an image, show it -->
      <?php if ($p['image']): ?>
        <div class="post-image">
          <img src="uploads/<?= e($p['image']) ?>" alt="post image" style="max-width:350px;">
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
</section>

<?php include 'footer.php'; ?>
