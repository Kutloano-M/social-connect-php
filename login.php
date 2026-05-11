<?php 
// Login Page
// Purpose: Handle user authentication (signing in)

// Include configuration (database connection + session start)
require_once 'configuration.php';

// Store validation errors (if any)
$errors = [];

// Step 1: Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and clean the submitted form data
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Step 2: Validate inputs
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    // Step 3: If no validation errors, check user credentials
    if (empty($errors)) {
        try {
            // Fetch the user's record from the database by email
            $stmt = $pdo->prepare("
                SELECT UserID, password 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            //  Verify that the user exists and password matches
            if ($user && password_verify($password, $user['password'])) {
                // Save user ID to session (keeps them logged in)
                $_SESSION['User_ID'] = $user['UserID'];

                // Redirect to dashboard after successful login
                header('Location: dashboard.php');
                exit;
            } else {
                // Wrong email or password
                $errors[] = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            // Handle database errors gracefully
            $errors[] = 'A database error occurred. Please try again later.';
            // Optionally log the error for debugging
            // error_log($e->getMessage());
        }
    }
}

// Include header layout
include 'header.php';
?>

<!-- ---------------------------------------------
     Login Form UI
---------------------------------------------- -->
<h2>Login</h2>

<?php if ($errors): ?>
  <!-- Display error messages -->
  <?php foreach ($errors as $err): ?>
    <p class="errors"><?= htmlspecialchars($err) ?></p>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Login form -->
<form method="post" id="loginForm" novalidate>
  <label>
    Email
    <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
  </label>

  <label>
    Password
    <input type="password" name="password" required>
  </label>

  <button type="submit">Login</button>
</form>

<!-- Link to password reset page -->
<p>
  <a href="reset_password.php">Forgot password?</a> 
  <small>(Use a token-based reset system in production for security.)</small>
</p>

<?php include 'footer.php'; ?>
