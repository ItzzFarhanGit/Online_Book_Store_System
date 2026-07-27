<?php
require "admin_guard.php";
require "../db.php";

$edit_book = null;
$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: books.php?msg=deleted");
    exit();
}

// Handle Add / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $price       = (float) $_POST['price'];
    $image       = trim($_POST['image']);
    $category    = trim($_POST['category']);
    $description = trim($_POST['description']);
    $stock       = (int) $_POST['stock'];
    $rating      = (float) $_POST['rating'];
    $book_id     = (int) ($_POST['book_id'] ?? 0);

    if ($title === '' || $author === '' || $image === '' || $price <= 0) {
        $error = 'Please fill in all required fields correctly.';
    } else {
        if ($book_id > 0) {
            $stmt = $conn->prepare("UPDATE books SET title=?, author=?, price=?, image=?, category=?, description=?, stock=?, rating=? WHERE id=?");
            $stmt->bind_param("ssdsssidi", $title, $author, $price, $image, $category, $description, $stock, $rating, $book_id);
            $stmt->execute();
            $stmt->close();
            $success = 'Book updated successfully!';
        } else {
            $stmt = $conn->prepare("INSERT INTO books (title, author, price, image, category, description, stock, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsssid", $title, $author, $price, $image, $category, $description, $stock, $rating);
            $stmt->execute();
            $stmt->close();
            $success = 'Book added successfully!';
        }
    }
}

// Handle Edit load
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_book = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$books = $conn->query("SELECT * FROM books ORDER BY id DESC");
$categories = ['Adventure', 'Biography', 'Fantasy', 'Children', 'Thriller', 'Romantic', 'Comady'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Books | Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { background: #f0f0f3; }
.admin-shell { display: flex; min-height: 100vh; }
.admin-sidebar { width: 230px; background: var(--bz-black); color: #fff; padding: 24px 0; flex-shrink: 0; }
.admin-sidebar .brand2 { color: var(--bz-red); font-weight: 800; font-size: 19px; padding: 0 24px 24px; display: block; }
.admin-sidebar a { display: block; color: #ccc; text-decoration: none; padding: 12px 24px; font-size: 14px; border-left: 3px solid transparent; transition: all 0.25s ease; }
.admin-sidebar a:hover, .admin-sidebar a.active { background: #1a1a1a; color: var(--bz-red-light); border-left-color: var(--bz-red); }
.admin-main { flex: 1; padding: 30px 36px; }
.book-form-card { background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); animation: bz-pop 0.4s ease; }
.book-form-card h3 { margin-top: 0; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.form-grid .full { grid-column: span 2; }
.form-grid label { display: block; font-size: 12.5px; color: #777; margin-bottom: 5px; }
.form-grid input, .form-grid select, .form-grid textarea {
  width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; outline: none;
}
.form-grid input:focus, .form-grid select:focus, .form-grid textarea:focus { border-color: var(--bz-red); box-shadow: 0 0 0 3px rgba(229,9,20,0.12); }
.admin-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-collapse: collapse; }
.admin-table th, .admin-table td { padding: 10px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.admin-table th { background: #fafafa; color: #666; font-weight: 700; text-transform: uppercase; font-size: 11px; }
.admin-table img { width: 36px; height: 50px; object-fit: cover; border-radius: 4px; }
.row-actions a { font-size: 12px; margin-right: 10px; text-decoration: none; font-weight: 700; }
.row-actions .edit { color: #0a6fc2; }
.row-actions .delete { color: #e74c3c; }
.alert-success { background: #d4f7dc; color: #1d8a3a; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
.alert-error { background: #f8d7da; color: #a32834; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
</style>
</head>
<body>

<div class="admin-shell">
  <div class="admin-sidebar">
    <span class="brand2">📚 Book Zone Admin</span>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="books.php" class="active">📘 Manage Books</a>
    <a href="orders.php">📦 Orders</a>
    <a href="messages.php">✉️ Messages</a>
    <a href="../home.php">🌐 View Site</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="admin-main">
    <h1>Manage Books</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert-success">Book deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success): ?><div class="alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="book-form-card">
      <h3><?php echo $edit_book ? '✏️ Edit Book' : '➕ Add New Book'; ?></h3>
      <form method="POST">
        <input type="hidden" name="book_id" value="<?php echo $edit_book['id'] ?? ''; ?>">
        <div class="form-grid">
          <div>
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($edit_book['title'] ?? ''); ?>" required>
          </div>
          <div>
            <label>Author</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($edit_book['author'] ?? ''); ?>" required>
          </div>
          <div>
            <label>Price (Rs.)</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($edit_book['price'] ?? ''); ?>" required>
          </div>
          <div>
            <label>Image filename (e.g. hobbit.jpeg)</label>
            <input type="text" name="image" value="<?php echo htmlspecialchars($edit_book['image'] ?? ''); ?>" required>
          </div>
          <div>
            <label>Category</label>
            <select name="category">
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo (isset($edit_book['category']) && $edit_book['category'] === $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label>Stock Quantity</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($edit_book['stock'] ?? 20); ?>" required>
          </div>
          <div>
            <label>Rating (0–5)</label>
            <input type="number" step="0.1" min="0" max="5" name="rating" value="<?php echo htmlspecialchars($edit_book['rating'] ?? 4.0); ?>">
          </div>
          <div class="full">
            <label>Description</label>
            <textarea name="description" rows="2"><?php echo htmlspecialchars($edit_book['description'] ?? ''); ?></textarea>
          </div>
          <div class="full" style="display:flex; gap:10px;">
            <button type="submit" class="btn-primary"><?php echo $edit_book ? 'Update Book' : 'Add Book'; ?></button>
            <?php if ($edit_book): ?><a href="books.php" class="btn-secondary">Cancel</a><?php endif; ?>
          </div>
        </div>
      </form>
    </div>

    <table class="admin-table">
      <tr><th>Cover</th><th>Title</th><th>Author</th><th>Category</th><th>Price</th><th>Stock</th><th>Rating</th><th>Actions</th></tr>
      <?php while ($b = $books->fetch_assoc()): ?>
        <tr>
          <?php $admin_image = bz_img($b['image']); ?>
          <td><img src="<?php echo htmlspecialchars($admin_image); ?>" alt=""></td>
          <td><?php echo htmlspecialchars($b['title']); ?></td>
          <td><?php echo htmlspecialchars($b['author']); ?></td>
          <td><?php echo htmlspecialchars($b['category']); ?></td>
          <td>Rs. <?php echo number_format($b['price'], 2); ?></td>
          <td><?php echo $b['stock'] <= 5 ? '<span style="color:#e67e22;font-weight:700;">' . $b['stock'] . '</span>' : $b['stock']; ?></td>
          <td>⭐ <?php echo number_format($b['rating'], 1); ?></td>
          <td class="row-actions">
            <a href="books.php?edit=<?php echo $b['id']; ?>" class="edit">Edit</a>
            <a href="books.php?delete=<?php echo $b['id']; ?>" class="delete" onclick="return confirm('Delete this book?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>

</body>
</html>
