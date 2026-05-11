<?php
// Reset Password Page
// Purpose: Handle password reset requests and updates

// Include configuration (database connection + session start)
require_once 'configuration.php';

// Store validation errors (if any)
$errors = [];
$success = "";

// Step 1: Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which form was submitted
    if (isset($_POST['request_reset'])) {
        // Handle password reset request
        $email = strtolower(trim($_POST['email'] ?? ''));

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($errors)) {
            try {
                // Check if user exists
                $stmt = $pdo->prepare("SELECT UserID FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Generate a secure token
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

                    // Store token in database
                    $updateStmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                    $updateStmt->execute([$token, $expires, $email]);

                    // In a real application, send an email with the reset link
                    // For demo purposes, we'll display the token
                    $success = "Password reset token generated. For demo purposes, your reset token is: <strong>$token</strong><br>Use this token to reset your password.";
                } else {
                    $errors[] = 'No account found with that email address.';
                }
            } catch (PDOException $e) {
                $errors[] = 'A database error occurred. Please try again later.';
            }
        }
    } elseif (isset($_POST['reset_password'])) {
        // Handle password reset with token
        $token = trim($_POST['token'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($token)) {
            $errors[] = 'Reset token is required.';
        }

        if (empty($newPassword)) {
            $errors[] = 'New password is required.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            try {
                // Find user with valid token
                $stmt = $pdo->prepare("SELECT UserID FROM users WHERE reset_token = ? AND reset_expires > NOW()");
                $stmt->execute([$token]);
                $user = $stmt->fetch();

                if ($user) {
                    // Hash new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update password and clear token
                    $updateStmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE UserID = ?");
                    $updateStmt->execute([$hashedPassword, $user['UserID']]);

                    $success = 'Password reset successfully! You can now <a href="login.php">log in</a> with your new password.';
                } else {
                    $errors[] = 'Invalid or expired reset token.';
                }
            } catch (PDOException $e) {
                $errors[] = 'A database error occurred. Please try again later.';
            }
        }
    }
}

// Include header layout
include 'header.php';
?>

<!-- ---------------------------------------------
     Password Reset UI
---------------------------------------------- -->
<h2>Reset Password</h2>

<?php if ($errors): ?>
  <!-- Display error messages -->
  <?php foreach ($errors as $err): ?>
    <p class="errors"><?= htmlspecialchars($err) ?></p>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($success): ?>
  <p class="success"><?= $success ?></p>
<?php endif; ?>

<!-- Step 1: Request Password Reset -->
<h3>Step 1: Request Password Reset</h3>
<form method="post" id="requestResetForm" novalidate>
  <label>
    Email Address
    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
  </label>
  <button type="submit" name="request_reset">Send Reset Token</button>
</form>

<!-- Step 2: Reset Password with Token -->
<h3>Step 2: Reset Password</h3>
<form method="post" id="resetPasswordForm" novalidate>
  <label>
    Reset Token
    <input type="text" name="token" required value="<?= htmlspecialchars($_POST['token'] ?? '') ?>">
  </label>

  <label>
    New Password
    <input type="password" name="new_password" required>
  </label>

  <label>
    Confirm New Password
    <input type="password" name="confirm_password" required>
  </label>

  <button type="submit" name="reset_password">Reset Password</button>
</form>

<!-- Link back to login -->
<p>
  Remember your password? <a href="login.php">Login here</a>.
</p>

<?php include 'footer.php'; ?>
