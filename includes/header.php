<?php
/* ============================================================
   includes/header.php
   Reusable Navbar — included by every protected page.
   Expects: $conn (db connection), session already started.
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Figure out current page for "active" nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Cart count badge
$cart_count = 0;
$wishlist_count = 0;

if (isset($_SESSION['username']) && isset($conn)) {
    $u = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) AS total FROM cart WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $cart_count = (int) $res['total'];
    $stmt->close();

    $stmt2 = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE username = ?");
    $stmt2->bind_param("s", $u);
    $stmt2->execute();
    $res2 = $stmt2->get_result()->fetch_assoc();
    $wishlist_count = (int) $res2['total'];
    $stmt2->close();
}
?>
<link rel="stylesheet" href="assets/css/style.css">

<div class="navbar">
  <a href="home.php" class="brand">📚 Book Zone</a>

  <form action="search.php" method="GET" class="search-form">
    <input type="text" name="q" class="search-input" placeholder="Search by title or author..."
           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
    <button type="submit" class="search-btn">🔍</button>
  </form>

  <div class="nav-links">
    <a href="home.php" class="nav-link <?php echo $current_page === 'home.php' ? 'active' : ''; ?>">Home</a>
    <a href="products.php" class="nav-link <?php echo $current_page === 'products.php' ? 'active' : ''; ?>">Products</a>
    <a href="about.php" class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>">About</a>
    <a href="contact.php" class="nav-link <?php echo $current_page === 'contact.php' ? 'active' : ''; ?>">Contact</a>
    <a href="order_history.php" class="nav-link <?php echo $current_page === 'order_history.php' ? 'active' : ''; ?>">My Orders</a>

    <a href="wishlist.php" class="nav-icon-link" title="Wishlist">
      ❤
      <?php if ($wishlist_count > 0): ?>
        <span class="nav-badge"><?php echo $wishlist_count; ?></span>
      <?php endif; ?>
    </a>

    <a href="cart.php" class="nav-icon-link" title="Cart">
      🛒
      <?php if ($cart_count > 0): ?>
        <span class="nav-badge"><?php echo $cart_count; ?></span>
      <?php endif; ?>
    </a>

    <?php if (isset($_SESSION['username'])): ?>
      <span class="welcome-text">Hi, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
      <a href="logout.php" class="nav-link">Logout</a>
    <?php else: ?>
      <a href="login.php" class="nav-link">Login</a>
      <a href="signup.php" class="nav-link">Sign Up</a>
    <?php endif; ?>
  </div>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
  <div class="flash-message <?php echo $_SESSION['flash_type'] ?? ''; ?>">
    <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
  </div>
  <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
<?php endif; ?>
