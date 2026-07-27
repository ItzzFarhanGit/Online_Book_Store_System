<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';

// Load cart
$stmt = $conn->prepare("SELECT * FROM cart WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$cart_result = $stmt->get_result();
$cart_rows = [];
$grand_total = 0;
while ($r = $cart_result->fetch_assoc()) {
    $cart_rows[] = $r;
    $grand_total += $r['price'] * $r['quantity'];
}
$stmt->close();

if (count($cart_rows) === 0) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $payment   = trim($_POST['payment_method'] ?? 'Cash on Delivery');

    if ($full_name === '' || $phone === '' || $address === '') {
        $error = 'Please fill in all delivery details.';
    } else {

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (username, total_amount, full_name, address, phone, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'Processing')");
            $stmt->bind_param("sdssss", $username, $grand_total, $full_name, $address, $phone, $payment);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, product_name, image, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $stock_stmt = $conn->prepare("UPDATE books SET stock = GREATEST(stock - ?, 0) WHERE id = ?");

            foreach ($cart_rows as $item) {
                $item_stmt->bind_param("iissdi", $order_id, $item['book_id'], $item['product_name'], $item['image'], $item['price'], $item['quantity']);
                $item_stmt->execute();

                $stock_stmt->bind_param("ii", $item['quantity'], $item['book_id']);
                $stock_stmt->execute();
            }
            $item_stmt->close();
            $stock_stmt->close();

            $clear = $conn->prepare("DELETE FROM cart WHERE username = ?");
            $clear->bind_param("s", $username);
            $clear->execute();
            $clear->close();

            $conn->commit();

            $_SESSION['flash_message'] = "Order #$order_id placed successfully! Thank you for shopping with Book Zone.";
            $_SESSION['flash_type'] = 'success';

            header("Location: order_history.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Something went wrong while placing your order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.checkout-wrap {
  display: flex; gap: 30px; max-width: 1000px; margin: 30px auto; padding: 0 20px; flex-wrap: wrap;
}
.checkout-form, .order-summary { flex: 1; min-width: 300px; }
.checkout-form {
  background: var(--bz-black); color: #fff; border-radius: 12px; padding: 28px;
  border-top: 4px solid var(--bz-red); animation: bz-pop 0.5s ease;
}
.checkout-form h2 { color: var(--bz-red); margin-top: 0; }
.checkout-form label { display: block; margin: 14px 0 6px; font-size: 13px; color: #ccc; }
.checkout-form input, .checkout-form select, .checkout-form textarea {
  width: 100%; padding: 11px 14px; border-radius: 7px; border: 1px solid #333;
  background: #1a1a1a; color: #fff; font-size: 14px; outline: none;
}
.checkout-form input:focus, .checkout-form select:focus, .checkout-form textarea:focus {
  border-color: var(--bz-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.2);
}
.order-summary {
  background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 14px rgba(0,0,0,0.08);
  animation: bz-slide-up 0.5s ease; align-self: flex-start;
}
.order-summary h3 { margin-top: 0; }
.summary-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; font-size: 14px; }
.summary-total { display: flex; justify-content: space-between; padding-top: 14px; font-size: 18px; font-weight: 800; color: var(--bz-red); }
.error-box { background: #2a0d0d; color: #ff8080; border-left: 4px solid #e74c3c; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>💳 Checkout</h1>
  <p><a href="home.php">Home</a> &gt; <a href="cart.php">Cart</a> &gt; Checkout</p>
</div>

<div class="checkout-wrap">

  <div class="checkout-form">
    <h2>Delivery Details</h2>
    <?php if ($error): ?><div class="error-box"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="POST">
      <label>Full Name</label>
      <input type="text" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? $username); ?>" required>

      <label>Phone Number</label>
      <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>

      <label>Delivery Address</label>
      <textarea name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>

      <label>Payment Method</label>
      <select name="payment_method">
        <option value="Cash on Delivery">Cash on Delivery</option>
        <option value="Card Payment">Card Payment</option>
        <option value="Bank Transfer">Bank Transfer</option>
      </select>

      <button type="submit" class="auth-submit">Place Order →</button>
    </form>
  </div>

  <div class="order-summary">
    <h3>Order Summary</h3>
    <?php foreach ($cart_rows as $item): ?>
      <div class="summary-item">
        <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
        <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
      </div>
    <?php endforeach; ?>
    <div class="summary-total">
      <span>Total</span>
      <span>Rs. <?php echo number_format($grand_total, 2); ?></span>
    </div>
  </div>

</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
