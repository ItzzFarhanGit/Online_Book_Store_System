<?php
/* ============================================================
   includes/book_card.php
   Renders one book card. Call render_book_card($book, $wishlisted)
   $book is an assoc array/row from the `books` table.
   ============================================================ */

function render_book_card($book, $wishlisted = false, $delay_index = 0) {
    $id          = (int) $book['id'];
    $title       = htmlspecialchars($book['title'] ?? '');
    $author      = htmlspecialchars($book['author'] ?? '');
    $price       = number_format((float) $book['price'], 2);
    $image       = htmlspecialchars(bz_img($book['image'] ?? ''));
    $category    = htmlspecialchars($book['category'] ?? 'General');
    $stock       = (int) ($book['stock'] ?? 0);
    $rating      = (float) ($book['rating'] ?? 0);
    $delay       = $delay_index * 0.05;

    $full_stars  = floor($rating);
    $stars_html  = str_repeat('★', $full_stars) . str_repeat('☆', 5 - $full_stars);

    echo '<div class="book-card" style="animation-delay:' . $delay . 's">';

    echo '<div class="book-thumb">';
    echo '<span class="category-tag">' . $category . '</span>';
    echo '<button class="wishlist-btn ' . ($wishlisted ? 'active' : '') . '" data-book-id="' . $id . '" onclick="toggleWishlist(' . $id . ', this)" title="Add to Wishlist">' . ($wishlisted ? '❤' : '🤍') . '</button>';
    echo '<a href="book_details.php?id=' . $id . '"><img src="' . $image . '" alt="' . $title . '" loading="lazy"></a>';
    echo '</div>';

    echo '<div class="book-info">';
    echo '<h3><a href="book_details.php?id=' . $id . '" style="color:inherit;text-decoration:none;">' . $title . '</a></h3>';
    echo '<p class="author">by ' . $author . '</p>';
    echo '<p class="rating">' . $stars_html . ' <span style="color:#999;">(' . number_format($rating, 1) . ')</span></p>';

    echo '<div class="price-row">';
    echo '<span class="price">Rs. ' . $price . '</span>';
    if ($stock <= 0) {
        echo '<span class="stock-out">Out of stock</span>';
    } elseif ($stock <= 5) {
        echo '<span class="stock-low">Only ' . $stock . ' left</span>';
    }
    echo '</div>';

    if ($stock > 0) {
        echo '<button class="add-cart-btn" onclick="addToCart(' . $id . ', this)">🛒 Add to Cart</button>';
    } else {
        echo '<button class="add-cart-btn" disabled>Out of Stock</button>';
    }

    echo '</div>'; // book-info
    echo '</div>'; // book-card
}
?>
