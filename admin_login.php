<?php
session_start();
require "db.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['pswd'];

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = 'Incorrect password.';
        }
    } else {
        $error = 'Admin not found.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="navbar">
  <a href="home.php" class="brand">📚 Book Zone <span style="color:#999; font-size:13px; font-weight:400;">Admin</span></a>
  <div class="nav-links">
    <a href="login.php" class="nav-link">Customer Login</a>
  </div>
</div>

<div class="auth-wrapper">
  <div class="auth-card" style="border-top-color:#444;">
    <h2 style="color:#ddd;">🔐 Admin Panel</h2>
    <p class="subtitle">Restricted access — staff only</p>

    <?php if ($error): ?>
      <div class="flash-message error" style="position:static; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Admin Username</label>
      <input type="text" name="username" required autofocus>

      <label>Password</label>
      <input type="password" name="pswd" required>

      <button type="submit" class="auth-submit">Login to Dashboard</button>
    </form>

    <p class="auth-switch">Default: admin / admin123</p>
  </div>
</div>

</body>
</html>
