<?php
/* ============================================================
   wishlist_action.php
   AJAX endpoint: toggle a book in/out of the user's wishlist
   ============================================================ */

session_start();
header('Content-Type: application/json');
require "db.php";

if (!isset($_SESSION['username'])) {
    echo json_encode([
        'success'  => false,
        'message'  => 'Please log in to use your wishlist.',
        'redirect' => 'login.php'
    ]);
    exit();
}

$username = $_SESSION['username'];
$book_id  = (int) ($_POST['book_id'] ?? 0);

if ($book_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid book.']);
    exit();
}

$stmt = $conn->prepare("SELECT id FROM wishlist WHERE username = ? AND book_id = ?");
$stmt->bind_param("si", $username, $book_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ?");
    $stmt->bind_param("i", $existing['id']);
    $stmt->execute();
    $stmt->close();
    $added = false;
} else {
    $stmt = $conn->prepare("INSERT INTO wishlist (username, book_id) VALUES (?, ?)");
    $stmt->bind_param("si", $username, $book_id);
    $stmt->execute();
    $stmt->close();
    $added = true;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$count = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

echo json_encode([
    'success'        => true,
    'added'          => $added,
    'wishlist_count' => $count
]);
