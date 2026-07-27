<?php
session_start();
require "db.php";

$error = '';
$success = '';

if (isset($_SESSION['signup_success'])) {
    $success = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['pswd'];

    if (!empty($username) && !empty($password)) {

        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                header("Location: home.php");
                exit();

            } else {
                $error = 'Incorrect password. Please try again.';
            }

        } else {
            $error = 'Username not found. Please sign up first.';
        }

        $stmt->close();
    } else {
        $error = 'Please fill in both fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="navbar">
  <a href="login.php" class="brand">📚 Book Zone</a>
  <div class="nav-links">
    <a href="login.php" class="nav-link active">Login</a>
    <a href="signup.php" class="nav-link">Sign Up</a>
  </div>
</div>

<div class="auth-wrapper">
  <div class="auth-card">
    <h2>Welcome Back</h2>
    <p class="subtitle">Login to continue browsing Book Zone</p>

    <?php if ($success): ?>
      <div class="flash-message success" style="position:static; animation:bz-pop 0.3s ease; margin-bottom:10px;">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="flash-message error" style="position:static; animation:bz-pop 0.3s ease; margin-bottom:10px;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label>Username</label>
      <input type="text" name="username" required autofocus>

      <label>Password</label>
      <input type="password" name="pswd" required>

      <button type="submit" class="auth-submit">Log In</button>
    </form>

    <p class="auth-switch">Don't have an account? <a href="signup.php">Sign Up</a></p>
  </div>
</div>

</body>
</html>
