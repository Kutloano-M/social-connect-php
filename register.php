<?php
// Register Page
// Purpose: Handle new user sign-up securely

// Include configuration (database connection + session start)
require_once 'configuration.php';

// Store validation errors
$errors = [];
$success = "";

// Step 1: Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and clean submitted form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // Step 2: Validate inputs
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Step 3: If no validation errors, check if user already exists
    if (empty($errors)) {
        try {
            // Check for duplicate email
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $check->execute([$email]);
            $exists = $check->fetchColumn();

            if ($exists) {
                $errors[] = "An account with that email already exists.";
            } else {
                // Step 4: Hash password securely
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Step 5: Insert user into database
                $stmt = $pdo->prepare("
                    INSERT INTO users (full_name, email, password)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$full_name, $email, $hashedPassword]);

                // Step 6: Redirect or show success message
                $success = "Account created successfully! You can now log in.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error. Please try again later.";
            // Optionally log error: error_log($e->getMessage());
        }
    }
}

// Include header layout
include 'header.php';
?>

<!-- ---------------------------------------------
     Registration Form UI
---------------------------------------------- -->
<h2>Register</h2>

<?php if ($errors): ?>
  <!-- Display validation errors -->
  <?php foreach ($errors as $err): ?>
    <p class="errors"><?= htmlspecialchars($err) ?></p>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($success): ?>
  <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" id="registerForm" class="register-form" novalidate>
  <div class="form-group">
    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
  </div><br>

  <div class="form-group">
    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" placeholder="Enter your email" required>
  </div><br>

  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter your password" required>
  </div><br>

  <div class="form-group">
    <label for="confirm_password">Confirm Password</label>
    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
  </div><br>

  <button type="submit" class="btn-primary">Register</button>
</form>

<!-- Link to login page -->
<p>
  Already have an account? 
  <a href="login.php">Login here</a>.
</p>

<?php include 'footer.php'; ?>
