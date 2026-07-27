<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);
    $message = trim($_POST['message']);
    $subject = trim($_POST['subject']);

    if ($name === '' || $phone === '' || $email === '' || $message === '') {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO contact (full_name, phone, email, address, message, subject) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $name, $phone, $email, $address, $message, $subject);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.contact-wrap { display: flex; gap: 30px; max-width: 1000px; margin: 30px auto 60px; padding: 0 20px; flex-wrap: wrap; }
.contact-info { flex: 1; min-width: 280px; }
.contact-info .info-box {
  background: var(--bz-black); color: var(--bz-red-light); padding: 16px 18px; border-radius: 8px;
  margin-bottom: 14px; font-size: 14px; transition: transform 0.3s ease;
}
.contact-info .info-box:hover { transform: translateX(4px); }
.contact-info iframe { width: 100%; height: 260px; border-radius: 8px; border: 0; margin-top: 10px; }
.success-box { background: #d4f7dc; color: #1d8a3a; padding: 14px 18px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>Contact</h1>
  <p><a href="home.php">Home</a> &gt; Contact</p>
</div>

<h2 class="section-title">Message <span>Us</span></h2>

<div class="contact-wrap">

  <div class="auth-card" style="flex:1; min-width:300px;">
    <h2>Get in Touch</h2>
    <p class="subtitle">We'd love to hear from you</p>

    <?php if ($success): ?>
      <div class="success-box">✔ Thank you! Your message has been sent — we'll get back to you soon.</div>
    <?php elseif ($error): ?>
      <div class="flash-message error" style="position:static; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Full Name</label>
      <input type="text" name="name" required>

      <label>Contact Number</label>
      <input type="text" name="phone" required>

      <label>E-mail</label>
      <input type="email" name="email" required>

      <label>Address</label>
      <input type="text" name="address">

      <label>Subject</label>
      <input type="text" name="subject">

      <label>Message</label>
      <textarea name="message" rows="3" required style="width:100%; padding:11px 14px; border-radius:7px; border:1px solid #333; background:#1a1a1a; color:#fff; font-size:14px;"></textarea>

      <button type="submit" name="submit" class="auth-submit">Submit</button>
    </form>
  </div>

  <div class="contact-info">
    <div class="info-box">📍 No.30, Ampara Street, Sammanthurai, Sri Lanka, 10250</div>
    <div class="info-box">📞 +94 771389050 / 0112650820</div>
    <div class="info-box">✉ onlinebook@zone.lk</div>
    <iframe src="https://maps.google.com/maps?q=Sammanthurai%20Sri%20Lanka&t=&z=12&ie=UTF8&iwloc=&output=embed" loading="lazy"></iframe>
  </div>

</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
