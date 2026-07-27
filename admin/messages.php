<?php
require "admin_guard.php";
require "../db.php";

$messages = $conn->query("SELECT * FROM contact ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages | Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { background: #f0f0f3; }
.admin-shell { display: flex; min-height: 100vh; }
.admin-sidebar { width: 230px; background: var(--bz-black); color: #fff; padding: 24px 0; flex-shrink: 0; }
.admin-sidebar .brand2 { color: var(--bz-red); font-weight: 800; font-size: 19px; padding: 0 24px 24px; display: block; }
.admin-sidebar a { display: block; color: #ccc; text-decoration: none; padding: 12px 24px; font-size: 14px; border-left: 3px solid transparent; transition: all 0.25s ease; }
.admin-sidebar a:hover, .admin-sidebar a.active { background: #1a1a1a; color: var(--bz-red-light); border-left-color: var(--bz-red); }
.admin-main { flex: 1; padding: 30px 36px; }
.msg-card { background: #fff; border-radius: 10px; padding: 18px 20px; margin-bottom: 14px; box-shadow: 0 3px 10px rgba(0,0,0,0.06); animation: bz-slide-up 0.35s ease; }
.msg-card .msg-head { display: flex; justify-content: space-between; font-size: 13px; color: #888; margin-bottom: 8px; flex-wrap: wrap; gap: 6px; }
.msg-card h4 { margin: 0 0 6px; }
.msg-card p { margin: 0; color: #333; line-height: 1.5; font-size: 14px; }
</style>
</head>
<body>

<div class="admin-shell">
  <div class="admin-sidebar">
    <span class="brand2">📚 Book Zone Admin</span>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="books.php">📘 Manage Books</a>
    <a href="orders.php">📦 Orders</a>
    <a href="messages.php" class="active">✉️ Messages</a>
    <a href="../home.php">🌐 View Site</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="admin-main">
    <h1>Contact Messages</h1>

    <?php if ($messages->num_rows > 0): while ($m = $messages->fetch_assoc()): ?>
      <div class="msg-card">
        <div class="msg-head">
          <span><strong><?php echo htmlspecialchars($m['full_name']); ?></strong> · <?php echo htmlspecialchars($m['email']); ?> · <?php echo htmlspecialchars($m['phone']); ?></span>
          <span><?php echo date('d M Y, h:i A', strtotime($m['created_at'])); ?></span>
        </div>
        <h4><?php echo htmlspecialchars($m['subject'] ?: '(No subject)'); ?></h4>
        <p><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
      </div>
    <?php endwhile; else: ?>
      <div class="empty-state"><span class="emoji">✉️</span>No messages received yet.</div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
