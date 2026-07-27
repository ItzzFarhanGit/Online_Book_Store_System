<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require "includes/book_card.php";

// Featured books = top rated
$featured = $conn->query("SELECT * FROM books ORDER BY rating DESC LIMIT 8");

// New arrivals
$new_arrivals = $conn->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 8");

// wishlist ids for the user
$wishlisted_ids = [];
if (isset($_SESSION['username'])) {
    $wstmt = $conn->prepare("SELECT book_id FROM wishlist WHERE username = ?");
    $wstmt->bind_param("s", $_SESSION['username']);
    $wstmt->execute();
    $wres = $wstmt->get_result();
    while ($wrow = $wres->fetch_assoc()) {
        $wishlisted_ids[] = (int) $wrow['book_id'];
    }
    $wstmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.hero {
  height: 480px;
  background-image: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('<?php echo bz_img("bk.jpg"); ?>');
  background-size: cover;
  background-position: center;
  border-radius: 10px;
  margin: 24px 24px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
  overflow: hidden;
  animation: bz-fade-in 0.8s ease;
}
.hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(120deg, rgba(229,9,20,0.15), transparent 60%);
}
.hero h1 {
  color: #fff;
  font-size: 52px;
  font-weight: 800;
  margin: 0 0 10px;
  text-shadow: 0 4px 18px rgba(0,0,0,0.5);
  animation: bz-slide-up 0.7s ease;
}
.hero h1 span { color: var(--bz-red); }
.hero p {
  color: #ddd;
  font-size: 18px;
  margin: 0 0 26px;
  animation: bz-slide-up 0.9s ease;
}
.hero .hero-actions { display: flex; gap: 14px; animation: bz-slide-up 1.1s ease; }

.welcome-banner {
  background: linear-gradient(120deg, var(--bz-black), #2a0a0a);
  color: #fff;
  text-align: center;
  padding: 16px;
  margin: 20px 24px 0;
  border-radius: 8px;
  font-size: 15px;
  animation: bz-slide-down 0.5s ease;
}
.welcome-banner strong { color: var(--bz-red-light); }

.cat-strip {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  padding: 30px 24px 10px;
}
.cat-strip a {
  flex: 0 0 auto;
  background: #fff;
  border-radius: 10px;
  padding: 18px 26px;
  text-align: center;
  text-decoration: none;
  color: #222;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  min-width: 110px;
}
.cat-strip a:hover {
  background: var(--bz-red);
  color: #fff;
  transform: translateY(-4px);
}
.cat-strip .emoji { font-size: 26px; display: block; margin-bottom: 6px; }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="welcome-banner">
  🎉 You are successfully logged in — Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Reader'); ?></strong>!
</div>

<div class="hero">
  <h1>Welcome to <span>Book Zone</span></h1>
  <p>Discover your next favorite story from our curated collection</p>
  <div class="hero-actions">
    <a href="products.php" class="btn-primary">Browse Books</a>
    <a href="about.php" class="btn-secondary" style="color:#fff;border-color:#fff;">Learn More</a>
  </div>
</div>

<div class="cat-strip">
  <a href="products.php?category=Adventure"><span class="emoji">🗺️</span>Adventure</a>
  <a href="products.php?category=Biography"><span class="emoji">📔</span>Biography</a>
  <a href="products.php?category=Fantasy"><span class="emoji">🐉</span>Fantasy</a>
  <a href="products.php?category=Children"><span class="emoji">🧸</span>Children</a>
  <a href="products.php?category=Thriller"><span class="emoji">🔪</span>Thriller</a>
  <a href="products.php?category=Romantic"><span class="emoji">💞</span>Romantic</a>
  <a href="products.php?category=Comady"><span class="emoji">😂</span>Comedy</a>
</div>

<h2 class="section-title">Top <span>Rated</span> Books</h2>
<p class="section-subtitle">Loved by readers, recommended by us</p>
<div class="book-grid">
<?php
$i = 0;
while ($book = $featured->fetch_assoc()) {
    $wishlisted = in_array((int) $book['id'], $wishlisted_ids);
    render_book_card($book, $wishlisted, $i);
    $i++;
}
?>
</div>

<h2 class="section-title">New <span>Arrivals</span></h2>
<p class="section-subtitle">Fresh additions to our shelves</p>
<div class="book-grid">
<?php
$i = 0;
while ($book = $new_arrivals->fetch_assoc()) {
    $wishlisted = in_array((int) $book['id'], $wishlisted_ids);
    render_book_card($book, $wishlisted, $i);
    $i++;
}
?>
</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
