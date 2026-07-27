<?php
/* ============================================================
   cart_action.php
   AJAX endpoint: add / update / remove cart items
   ============================================================ */

session_start();
header('Content-Type: application/json');
require "db.php";

if (!isset($_SESSION['username'])) {
    echo json_encode([
        'success'  => false,
        'message'  => 'Please log in to manage your cart.',
        'redirect' => 'login.php'
    ]);
    exit();
}

$username = $_SESSION['username'];
$action   = $_POST['action'] ?? '';

function get_cart_count($conn, $username) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) AS total FROM cart WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $total = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    return $total;
}

function get_grand_total($conn, $username) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(price * quantity),0) AS total FROM cart WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $total = (float) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    return $total;
}

if ($action === 'add') {

    $book_id = (int) ($_POST['book_id'] ?? 0);

    $stmt = $conn->prepare("SELECT id, title, price, image, stock FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$book) {
        echo json_encode(['success' => false, 'message' => 'Book not found.']);
        exit();
    }

    if ($book['stock'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'This book is out of stock.']);
        exit();
    }

    // Already in cart? increase quantity (capped by stock)
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE username = ? AND book_id = ?");
    $stmt->bind_param("si", $username, $book_id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $new_qty = min($existing['quantity'] + 1, $book['stock']);
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_qty, $existing['id']);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO cart (username, book_id, product_name, price, image, quantity) VALUES (?, ?, ?, ?, ?, 1)"
        );
        $stmt->bind_param("sisds", $username, $book_id, $book['title'], $book['price'], $book['image']);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode([
        'success'    => true,
        'message'    => '"' . $book['title'] . '" added to cart!',
        'cart_count' => get_cart_count($conn, $username)
    ]);
    exit();

} elseif ($action === 'update') {

    $cart_id  = (int) ($_POST['cart_id'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

    // Make sure this cart row belongs to the logged in user, and respect stock
    $stmt = $conn->prepare("SELECT c.id, c.book_id, c.price, b.stock FROM cart c JOIN books b ON c.book_id = b.id WHERE c.id = ? AND c.username = ?");
    $stmt->bind_param("is", $cart_id, $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
        exit();
    }

    $quantity = min($quantity, max(1, (int) $row['stock']));

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();
    $stmt->close();

    $line_total = number_format($row['price'] * $quantity, 2);

    echo json_encode([
        'success'     => true,
        'line_total'  => $line_total,
        'grand_total' => number_format(get_grand_total($conn, $username), 2),
        'cart_count'  => get_cart_count($conn, $username)
    ]);
    exit();

} elseif ($action === 'remove') {

    $cart_id = (int) ($_POST['cart_id'] ?? 0);

    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND username = ?");
    $stmt->bind_param("is", $cart_id, $username);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success'     => true,
        'grand_total' => number_format(get_grand_total($conn, $username), 2),
        'cart_count'  => get_cart_count($conn, $username)
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
