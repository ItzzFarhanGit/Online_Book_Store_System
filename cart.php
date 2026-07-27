<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT c.id, c.book_id, c.product_name, c.price, c.image, c.quantity, b.stock
                         FROM cart c LEFT JOIN books b ON c.book_id = b.id
                         WHERE c.username = ? ORDER BY c.added_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$cart_items = $stmt->get_result();

$grand_total = 0;
$rows = [];
while ($row = $cart_items->fetch_assoc()) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $grand_total += $row['line_total'];
    $rows[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.steps {
  display: flex;
  justify-content: center;
  margin: 30px 0;
  font-weight: bold;
  gap: 40px;
  flex-wrap: wrap;
}
.step { text-align: center; }
.step .circle {
  background: #ddd; color: #555; border-radius: 50%;
  width: 34px; height: 34px; line-height: 34px;
  margin: 0 auto 6px; transition: all 0.3s ease;
}
.step.current .circle { background: var(--bz-red); color: #fff; animation: bz-pulse 2s infinite; }
.step.current { color: var(--bz-red); }

.cart-table-wrap { max-width: 950px; margin: 0 auto 50px; padding: 0 20px; }
.cart-row {
  display: flex; align-items: center; gap: 18px;
  background: #fff; border-radius: 10px; padding: 16px;
  margin-bottom: 14px; box-shadow: 0 3px 10px rgba(0,0,0,0.07);
  animation: bz-slide-up 0.4s ease;
}
.cart-row img { width: 70px; height: 95px; object-fit: cover; border-radius: 6px; }
.cart-row .info { flex: 1; min-width: 140px; }
.cart-row .info h4 { margin: 0 0 4px; font-size: 15px; }
.cart-row .info .unit-price { color: #777; font-size: 13px; }
.qty-control { display: flex; align-items: center; gap: 10px; }
.qty-control button {
  width: 30px; height: 30px; border-radius: 6px; border: 1px solid #ddd;
  background: #fff; cursor: pointer; font-size: 16px; transition: all 0.2s ease;
}
.qty-control button:hover { background: var(--bz-red); color: #fff; border-color: var(--bz-red); }
.qty-control span { min-width: 24px; text-align: center; font-weight: 700; }
.line-total { font-weight: 800; color: var(--bz-red); min-width: 100px; text-align: right; }
.remove-btn {
  background: none; border: none; color: #999; font-size: 20px; cursor: pointer;
  transition: color 0.2s ease, transform 0.2s ease;
}
.remove-btn:hover { color: var(--bz-red); transform: scale(1.2); }

.cart-summary {
  background: var(--bz-black); color: #fff; border-radius: 10px;
  padding: 24px; margin-top: 10px; display: flex; justify-content: space-between;
  align-items: center; flex-wrap: wrap; gap: 14px;
}
.cart-summary .total-label { font-size: 14px; color: #aaa; }
.cart-summary .total-value { font-size: 26px; font-weight: 800; color: var(--bz-red-light); }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>🛒 Your Cart</h1>
  <p><a href="home.php">Home</a> &gt; Cart</p>
</div>

<div class="steps">
  <div class="step current"><div class="circle">1</div><div>CART</div></div>
  <div class="step"><div class="circle">2</div><div>CHECKOUT</div></div>
  <div class="step"><div class="circle">3</div><div>PAYMENT</div></div>
  <div class="step"><div class="circle">4</div><div>CONFIRMATION</div></div>
</div>

<div class="cart-table-wrap">

<?php if (count($rows) === 0): ?>

  <div class="empty-state">
    <span class="emoji">🛍️</span>
    <p>Your cart is empty. Let's find you something great to read!</p>
    <a href="products.php" class="btn-primary">Continue Shopping</a>
  </div>

<?php else: ?>

  <?php foreach ($rows as $row): ?>
    <div class="cart-row" id="cart-row-<?php echo $row['id']; ?>">
      <?php $cart_image = bz_img($row['image']); ?>
      <img src="<?php echo htmlspecialchars($cart_image); ?>" alt="">
      <div class="info">
        <h4><?php echo htmlspecialchars($row['product_name']); ?></h4>
        <p class="unit-price">Rs. <?php echo number_format($row['price'], 2); ?> each</p>
      </div>
      <div class="qty-control">
        <button onclick="changeQty(<?php echo $row['id']; ?>, -1)">−</button>
        <span id="qty-<?php echo $row['id']; ?>"><?php echo $row['quantity']; ?></span>
        <button onclick="changeQty(<?php echo $row['id']; ?>, 1)">+</button>
      </div>
      <div class="line-total" id="line-total-<?php echo $row['id']; ?>">
        Rs. <?php echo number_format($row['line_total'], 2); ?>
      </div>
      <button class="remove-btn" onclick="removeFromCart(<?php echo $row['id']; ?>)" title="Remove">🗑️</button>
    </div>
  <?php endforeach; ?>

  <div class="cart-summary">
    <div>
      <div class="total-label">Grand Total</div>
      <div class="total-value" id="cart-grand-total">Rs. <?php echo number_format($grand_total, 2); ?></div>
    </div>
    <div style="display:flex; gap:12px;">
      <a href="products.php" class="btn-secondary" style="color:#fff;border-color:#fff;">Continue Shopping</a>
      <a href="checkout.php" class="btn-primary">Proceed to Checkout →</a>
    </div>
  </div>

<?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
<script>
const quantities = {};
<?php foreach ($rows as $row): ?>
quantities[<?php echo $row['id']; ?>] = <?php echo $row['quantity']; ?>;
<?php endforeach; ?>

function changeQty(cartId, delta) {
  quantities[cartId] = Math.max(1, (quantities[cartId] || 1) + delta);
  document.getElementById('qty-' + cartId).textContent = quantities[cartId];
  updateCartQuantity(cartId, quantities[cartId]);
}
</script>
</body>
</html>
