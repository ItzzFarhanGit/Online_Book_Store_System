<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$orders = $stmt->get_result();

$order_list = [];
while ($o = $orders->fetch_assoc()) {
    $istmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $istmt->bind_param("i", $o['id']);
    $istmt->execute();
    $o['items'] = $istmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $istmt->close();
    $order_list[] = $o;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.orders-wrap { max-width: 850px; margin: 30px auto 60px; padding: 0 20px; }
.order-card {
  background: #fff; border-radius: 12px; padding: 22px; margin-bottom: 20px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.08); animation: bz-slide-up 0.4s ease;
}
.order-head {
  display: flex; justify-content: space-between; align-items: center;
  border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;
}
.order-head h3 { margin: 0; font-size: 16px; }
.order-head .date { color: #888; font-size: 13px; }
.status-badge {
  padding: 5px 14px; border-radius: 14px; font-size: 12px; font-weight: 700; text-transform: uppercase;
}
.status-Processing { background: #fff3cd; color: #946c00; }
.status-Shipped { background: #cfe2ff; color: #0a4dab; }
.status-Delivered { background: #d4f7dc; color: #1d8a3a; }
.status-Cancelled { background: #f8d7da; color: #a32834; }
.order-item-row { display: flex; align-items: center; gap: 14px; padding: 8px 0; }
.order-item-row img { width: 44px; height: 60px; object-fit: cover; border-radius: 4px; }
.order-item-row .name { flex: 1; font-size: 14px; }
.order-foot { display: flex; justify-content: space-between; margin-top: 14px; padding-top: 12px; border-top: 1px solid #eee; font-weight: 700; }
.order-foot .total { color: var(--bz-red); }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>📦 My Orders</h1>
  <p><a href="home.php">Home</a> &gt; My Orders</p>
</div>

<div class="orders-wrap">
<?php if (count($order_list) === 0): ?>
  <div class="empty-state">
    <span class="emoji">📦</span>
    <p>You haven't placed any orders yet.</p>
    <a href="products.php" class="btn-primary">Start Shopping</a>
  </div>
<?php else: ?>
  <?php foreach ($order_list as $order): ?>
    <div class="order-card">
      <div class="order-head">
        <h3>Order #<?php echo $order['id']; ?></h3>
        <span class="date"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></span>
        <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span>
      </div>

      <?php foreach ($order['items'] as $item): ?>
        <?php $history_image = bz_img($item['image']); ?>
        <div class="order-item-row">
          <img src="<?php echo htmlspecialchars($history_image); ?>" alt="">
          <span class="name"><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
          <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
        </div>
      <?php endforeach; ?>

      <div class="order-foot">
        <span>Deliver to: <?php echo htmlspecialchars($order['full_name']); ?> · <?php echo htmlspecialchars($order['payment_method']); ?></span>
        <span class="total">Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
