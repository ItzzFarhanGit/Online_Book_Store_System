<?php
require "admin_guard.php";
require "../db.php";

$total_books   = $conn->query("SELECT COUNT(*) AS c FROM books")->fetch_assoc()['c'];
$total_users   = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_orders  = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM orders")->fetch_assoc()['s'];
$low_stock     = $conn->query("SELECT COUNT(*) AS c FROM books WHERE stock <= 5")->fetch_assoc()['c'];
$messages      = $conn->query("SELECT COUNT(*) AS c FROM contact")->fetch_assoc()['c'];

$recent_orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Book Zone</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { background: #f0f0f3; }
.admin-shell { display: flex; min-height: 100vh; }
.admin-sidebar {
  width: 230px; background: var(--bz-black); color: #fff; padding: 24px 0; flex-shrink: 0;
}
.admin-sidebar .brand2 { color: var(--bz-red); font-weight: 800; font-size: 19px; padding: 0 24px 24px; display: block; }
.admin-sidebar a {
  display: block; color: #ccc; text-decoration: none; padding: 12px 24px; font-size: 14px;
  border-left: 3px solid transparent; transition: all 0.25s ease;
}
.admin-sidebar a:hover, .admin-sidebar a.active { background: #1a1a1a; color: var(--bz-red-light); border-left-color: var(--bz-red); }
.admin-main { flex: 1; padding: 30px 36px; }
.admin-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 26px; }
.admin-topbar h1 { margin: 0; font-size: 24px; }
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 18px; margin-bottom: 30px; }
.stat-card {
  background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  animation: bz-pop 0.4s ease backwards; border-left: 4px solid var(--bz-red);
}
.stat-card .label { color: #888; font-size: 13px; margin-bottom: 6px; }
.stat-card .value { font-size: 26px; font-weight: 800; color: #111; }
.stat-card .icon { font-size: 22px; float: right; opacity: 0.5; }

.admin-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-collapse: collapse; }
.admin-table th, .admin-table td { padding: 12px 16px; text-align: left; font-size: 13.5px; border-bottom: 1px solid #f0f0f0; }
.admin-table th { background: #fafafa; color: #666; font-weight: 700; text-transform: uppercase; font-size: 11px; }
.panel-title { margin: 0 0 14px; }
</style>
</head>
<body>

<div class="admin-shell">
  <div class="admin-sidebar">
    <span class="brand2">📚 Book Zone Admin</span>
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="books.php">📘 Manage Books</a>
    <a href="orders.php">📦 Orders</a>
    <a href="messages.php">✉️ Messages</a>
    <a href="../home.php">🌐 View Site</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="admin-main">
    <div class="admin-topbar">
      <h1>Dashboard Overview</h1>
      <span class="welcome-text" style="color:#555;">Hi, <strong style="color:var(--bz-red);"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
    </div>

    <div class="stat-grid">
      <div class="stat-card" style="animation-delay:0s;"><span class="icon">📘</span><div class="label">Total Books</div><div class="value"><?php echo $total_books; ?></div></div>
      <div class="stat-card" style="animation-delay:0.05s;"><span class="icon">👤</span><div class="label">Registered Users</div><div class="value"><?php echo $total_users; ?></div></div>
      <div class="stat-card" style="animation-delay:0.1s;"><span class="icon">📦</span><div class="label">Total Orders</div><div class="value"><?php echo $total_orders; ?></div></div>
      <div class="stat-card" style="animation-delay:0.15s;"><span class="icon">💰</span><div class="label">Total Revenue</div><div class="value">Rs. <?php echo number_format($total_revenue, 2); ?></div></div>
      <div class="stat-card" style="animation-delay:0.2s;"><span class="icon">⚠️</span><div class="label">Low Stock Books</div><div class="value"><?php echo $low_stock; ?></div></div>
      <div class="stat-card" style="animation-delay:0.25s;"><span class="icon">✉️</span><div class="label">Contact Messages</div><div class="value"><?php echo $messages; ?></div></div>
    </div>

    <h2 class="panel-title">Recent Orders</h2>
    <table class="admin-table">
      <tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr>
      <?php if ($recent_orders->num_rows > 0): while ($o = $recent_orders->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $o['id']; ?></td>
          <td><?php echo htmlspecialchars($o['username']); ?></td>
          <td>Rs. <?php echo number_format($o['total_amount'], 2); ?></td>
          <td><?php echo htmlspecialchars($o['status']); ?></td>
          <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="5" style="text-align:center; color:#999;">No orders yet.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>

</body>
</html>
