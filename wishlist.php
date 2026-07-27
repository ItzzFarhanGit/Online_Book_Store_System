<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require "includes/book_card.php";

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT b.* FROM wishlist w JOIN books b ON w.book_id = b.id WHERE w.username = ? ORDER BY w.added_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$books = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Wishlist | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>❤ My Wishlist</h1>
  <p><a href="home.php">Home</a> &gt; Wishlist</p>
</div>

<div class="book-grid">
<?php
$i = 0;
if ($books->num_rows > 0) {
    while ($book = $books->fetch_assoc()) {
        render_book_card($book, true, $i);
        $i++;
    }
} else {
    echo '<div class="empty-state"><span class="emoji">💔</span>Your wishlist is empty. Tap the heart on any book to save it here!</div>';
}
?>
</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
