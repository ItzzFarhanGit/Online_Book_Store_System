<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require "includes/book_card.php";

// Category filter
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$categories = ['Adventure', 'Biography', 'Fantasy', 'Children', 'Thriller', 'Romantic', 'Comady'];

if ($category !== '' && in_array($category, $categories)) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $books = $stmt->get_result();
    $stmt->close();
} else {
    $books = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
}

// Get user's wishlist book_ids for heart highlighting
$wishlisted_ids = [];
$wstmt = $conn->prepare("SELECT book_id FROM wishlist WHERE username = ?");
$wstmt->bind_param("s", $_SESSION['username']);
$wstmt->execute();
$wres = $wstmt->get_result();
while ($wrow = $wres->fetch_assoc()) {
    $wishlisted_ids[] = (int) $wrow['book_id'];
}
$wstmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>📖 Our Products</h1>
  <p><a href="home.php">Home</a> &gt; Products</p>
</div>

<div class="filter-bar">
  <a href="products.php" class="filter-chip <?php echo $category === '' ? 'active' : ''; ?>">All Books</a>
  <?php foreach ($categories as $cat): ?>
    <a href="products.php?category=<?php echo urlencode($cat); ?>"
       class="filter-chip <?php echo $category === $cat ? 'active' : ''; ?>">
       <?php echo $cat === 'Comady' ? 'Comedy' : $cat; ?>
    </a>
  <?php endforeach; ?>
</div>

<h2 class="section-title">All <span>Books</span></h2>
<p class="section-subtitle">Browse our full collection — fresh titles added regularly</p>

<div class="book-grid">
<?php
$i = 0;
if ($books->num_rows > 0) {
    while ($book = $books->fetch_assoc()) {
        $wishlisted = in_array((int) $book['id'], $wishlisted_ids);
        render_book_card($book, $wishlisted, $i);
        $i++;
    }
} else {
    echo '<div class="empty-state"><span class="emoji">📭</span>No books found in this category yet.</div>';
}
?>
</div>

<?php include "includes/footer.php"; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
