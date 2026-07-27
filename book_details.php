<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require "includes/book_card.php";

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    header("Location: products.php");
    exit();
}

// Check wishlist status
$wstmt = $conn->prepare("SELECT id FROM wishlist WHERE username = ? AND book_id = ?");
$wstmt->bind_param("si", $_SESSION['username'], $id);
$wstmt->execute();
$is_wishlisted = $wstmt->get_result()->num_rows > 0;
$wstmt->close();

// Related books (same category, exclude current)
$rstmt = $conn->prepare("SELECT * FROM books WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 4");
$rstmt->bind_param("si", $book['category'], $id);
$rstmt->execute();
$related = $rstmt->get_result();

$image_path = bz_img($book['image'] ?? '');

$rating = (float) $book['rating'];
$full_stars = floor($rating);
$stars_html = str_repeat('★', $full_stars) . str_repeat('☆', 5 - $full_stars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($book['title']); ?> | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.detail-wrap {
  display: flex;
  gap: 40px;
  max-width: 1100px;
  margin: 30px auto;
  padding: 0 24px;
  flex-wrap: wrap;
  animation: bz-pop 0.5s ease;
}
.detail-img {
  flex: 0 0 320px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  height: 460px;
}
.detail-img img { width: 100%; height: 100%; object-fit: cover; }
.detail-info { flex: 1; min-width: 280px; }
.detail-info .cat-tag {
  display: inline-block; background: var(--bz-red); color: #fff;
  padding: 4px 14px; border-radius: 14px; font-size: 12px; font-weight: 700;
  text-transform: uppercase; margin-bottom: 12px;
}
.detail-info h1 { margin: 6px 0 4px; font-size: 28px; }
.detail-info .author { color: #666; margin-bottom: 10px; }
.detail-info .price-big { font-size: 30px; color: var(--bz-red); font-weight: 800; margin: 14px 0; }
.detail-info .desc { line-height: 1.7; color: #333; margin-bottom: 20px; }
.qty-row { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.qty-row button {
  width: 36px; height: 36px; border-radius: 50%; border: 1px solid #ccc; background: #fff;
  font-size: 18px; cursor: pointer; transition: all 0.2s ease;
}
.qty-row button:hover { background: var(--bz-red); color: #fff; border-color: var(--bz-red); }
.action-row { display: flex; gap: 12px; flex-wrap: wrap; }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>📖 Book Details</h1>
  <p><a href="home.php">Home</a> &gt; <a href="products.php">Products</a> &gt; <?php echo htmlspecialchars($book['title']); ?></p>
</div>

<div class="detail-wrap">
  <div class="detail-img">
    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
  </div>

  <div class="detail-info">
    <span class="cat-tag"><?php echo htmlspecialchars($book['category']); ?></span>
    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
    <p class="author">by <?php echo htmlspecialchars($book['author']); ?></p>
    <p class="rating" style="color:#f5a623; font-size:16px;">
      <?php echo $stars_html; ?> <span style="color:#999; font-size:14px;">(<?php echo number_format($rating, 1); ?> / 5)</span>
    </p>

    <p class="price-big">Rs. <?php echo number_format((float) $book['price'], 2); ?></p>

    <p class="desc"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>

    <?php if ($book['stock'] > 0): ?>
      <p style="color:#2ecc71; font-weight:600;">✔ In Stock (<?php echo (int) $book['stock']; ?> available)</p>
    <?php else: ?>
      <p style="color:#e74c3c; font-weight:600;">✘ Out of Stock</p>
    <?php endif; ?>

    <div class="action-row">
      <?php if ($book['stock'] > 0): ?>
        <button class="btn-primary" onclick="addToCart(<?php echo $book['id']; ?>, this)">🛒 Add to Cart</button>
      <?php else: ?>
        <button class="btn-primary" disabled style="opacity:0.5;cursor:not-allowed;">Out of Stock</button>
      <?php endif; ?>
      <button class="btn-secondary wishlist-btn-inline" onclick="toggleWishlist(<?php echo $book['id']; ?>, this)" style="position:static;width:auto;height:auto;border-radius:6px;padding:9px 20px;background:transparent;color:var(--bz-red);<?php echo $is_wishlisted ? 'background:var(--bz-red);color:#fff;' : ''; ?>">
        <?php echo $is_wishlisted ? '❤ In Wishlist' : '🤍 Add to Wishlist'; ?>
      </button>
      <a href="products.php" class="btn-dark">← Back to Products</a>
    </div>
  </div>
</div>

<?php if ($related->num_rows > 0): ?>
<h2 class="section-title">You May Also <span>Like</span></h2>
<div class="book-grid">
<?php
/* includes/book_card.php was already loaded once at the top of this file — do not require it again here, PHP does not allow a function to be declared twice. */
$i = 0;
while ($rb = $related->fetch_assoc()) {
    render_book_card($rb, false, $i);
    $i++;
}
?>
</div>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
