<?php
session_start();
require "db.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['mail']);
    $password = $_POST['pswd'];
    $confirm  = $_POST['confirm'];

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match!';
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = 'Username or email already registered!';
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {

                session_regenerate_id(true);
                $_SESSION['signup_success'] = 'Account created successfully. Please log in.';

                header("Location: login.php");
                exit();

            } else {
                $error = 'Something went wrong. Please try again.';
            }

            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="navbar">
  <a href="signup.php" class="brand">📚 Book Zone</a>
  <div class="nav-links">
    <a href="login.php" class="nav-link">Login</a>
    <a href="signup.php" class="nav-link active">Sign Up</a>
  </div>
</div>

<div class="auth-wrapper">
  <div class="auth-card">
    <h2>Create Account</h2>
    <p class="subtitle">Join Book Zone and start your reading journey</p>

    <?php if ($error): ?>
      <div class="flash-message error" style="position:static; animation:bz-pop 0.3s ease; margin-bottom:10px;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label>Username</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>

      <label>E-mail</label>
      <input type="email" name="mail" value="<?php echo htmlspecialchars($_POST['mail'] ?? ''); ?>" required>

      <label>Password</label>
      <input type="password" name="pswd" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm" required>

      <button type="submit" class="auth-submit">Sign Up</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>

</body>
</html>
