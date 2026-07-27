<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require "includes/book_card.php";

$query    = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$categories = ['Adventure', 'Biography', 'Fantasy', 'Children', 'Thriller', 'Romantic', 'Comady'];

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];
$types = '';

if ($query !== '') {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $like = '%' . $query . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($category !== '' && in_array($category, $categories)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books = $stmt->get_result();

// wishlist ids
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
<title>Search Results | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>🔍 Search Results</h1>
  <p><a href="home.php">Home</a> &gt; Search<?php echo $query !== '' ? ': "' . htmlspecialchars($query) . '"' : ''; ?></p>
</div>

<div class="filter-bar">
  <a href="search.php?q=<?php echo urlencode($query); ?>" class="filter-chip <?php echo $category === '' ? 'active' : ''; ?>">All</a>
  <?php foreach ($categories as $cat): ?>
    <a href="search.php?q=<?php echo urlencode($query); ?>&category=<?php echo urlencode($cat); ?>"
       class="filter-chip <?php echo $category === $cat ? 'active' : ''; ?>">
       <?php echo $cat === 'Comady' ? 'Comedy' : $cat; ?>
    </a>
  <?php endforeach; ?>
</div>

<h2 class="section-title"><span><?php echo $books->num_rows; ?></span> result<?php echo $books->num_rows == 1 ? '' : 's'; ?> found</h2>

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
    echo '<div class="empty-state"><span class="emoji">🔎</span>No books matched your search. Try a different keyword.</div>';
}
?>
</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
