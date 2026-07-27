<?php
require "admin_guard.php";
require "../db.php";

if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status   = trim($_POST['status']);
    $allowed  = ['Processing', 'Shipped', 'Delivered', 'Cancelled'];
    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: orders.php");
    exit();
}

$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders | Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { background: #f0f0f3; }
.admin-shell { display: flex; min-height: 100vh; }
.admin-sidebar { width: 230px; background: var(--bz-black); color: #fff; padding: 24px 0; flex-shrink: 0; }
.admin-sidebar .brand2 { color: var(--bz-red); font-weight: 800; font-size: 19px; padding: 0 24px 24px; display: block; }
.admin-sidebar a { display: block; color: #ccc; text-decoration: none; padding: 12px 24px; font-size: 14px; border-left: 3px solid transparent; transition: all 0.25s ease; }
.admin-sidebar a:hover, .admin-sidebar a.active { background: #1a1a1a; color: var(--bz-red-light); border-left-color: var(--bz-red); }
.admin-main { flex: 1; padding: 30px 36px; }
.admin-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-collapse: collapse; }
.admin-table th, .admin-table td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.admin-table th { background: #fafafa; color: #666; font-weight: 700; text-transform: uppercase; font-size: 11px; }
.admin-table select { padding: 6px 10px; border-radius: 6px; border: 1px solid #ddd; font-size: 12.5px; }
</style>
</head>
<body>

<div class="admin-shell">
  <div class="admin-sidebar">
    <span class="brand2">📚 Book Zone Admin</span>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="books.php">📘 Manage Books</a>
    <a href="orders.php" class="active">📦 Orders</a>
    <a href="messages.php">✉️ Messages</a>
    <a href="../home.php">🌐 View Site</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="admin-main">
    <h1>All Orders</h1>

    <table class="admin-table">
      <tr><th>Order #</th><th>Customer</th><th>Phone</th><th>Address</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th></tr>
      <?php if ($orders->num_rows > 0): while ($o = $orders->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $o['id']; ?></td>
          <td><?php echo htmlspecialchars($o['username']); ?><br><small style="color:#999;"><?php echo htmlspecialchars($o['full_name']); ?></small></td>
          <td><?php echo htmlspecialchars($o['phone']); ?></td>
          <td><?php echo htmlspecialchars($o['address']); ?></td>
          <td>Rs. <?php echo number_format($o['total_amount'], 2); ?></td>
          <td><?php echo htmlspecialchars($o['payment_method']); ?></td>
          <td>
            <form method="POST" style="margin:0;">
              <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
              <select name="status" onchange="this.form.submit()">
                <?php foreach (['Processing','Shipped','Delivered','Cancelled'] as $st): ?>
                  <option value="<?php echo $st; ?>" <?php echo $o['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
          <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="8" style="text-align:center; color:#999;">No orders placed yet.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>

</body>
</html>
