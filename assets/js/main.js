/* ============================================================
   Online Book Zone — Shared JS
   Handles Add-to-Cart and Wishlist toggling via fetch/AJAX
   ============================================================ */

function showToast(message, type = 'success') {
  const existing = document.querySelector('.js-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = 'flash-message js-toast ' + type;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.remove(), 3000);
}

function addToCart(bookId, btnEl) {
  const originalText = btnEl.innerHTML;
  btnEl.innerHTML = 'Adding...';
  btnEl.disabled = true;

  fetch('cart_action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&book_id=' + encodeURIComponent(bookId)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        btnEl.innerHTML = '✔ Added!';
        showToast(data.message || 'Book added to cart!', 'success');
        updateNavBadge('cart', data.cart_count);
        setTimeout(() => {
          btnEl.innerHTML = originalText;
          btnEl.disabled = false;
        }, 1200);
      } else {
        showToast(data.message || 'Could not add to cart.', 'error');
        btnEl.innerHTML = originalText;
        btnEl.disabled = false;
        if (data.redirect) window.location.href = data.redirect;
      }
    })
    .catch(() => {
      showToast('Network error. Please try again.', 'error');
      btnEl.innerHTML = originalText;
      btnEl.disabled = false;
    });
}

function toggleWishlist(bookId, btnEl) {
  fetch('wishlist_action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'book_id=' + encodeURIComponent(bookId)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        if (data.added) {
          btnEl.classList.add('active');
          btnEl.innerHTML = '❤';
          showToast('Added to wishlist!', 'success');
        } else {
          btnEl.classList.remove('active');
          btnEl.innerHTML = '🤍';
          showToast('Removed from wishlist.', 'success');
        }
        updateNavBadge('wishlist', data.wishlist_count);
      } else {
        showToast(data.message || 'Please log in first.', 'error');
        if (data.redirect) window.location.href = data.redirect;
      }
    })
    .catch(() => showToast('Network error. Please try again.', 'error'));
}

function updateNavBadge(type, count) {
  const icon = document.querySelector(
    type === 'cart' ? 'a[title="Cart"]' : 'a[title="Wishlist"]'
  );
  if (!icon) return;
  let badge = icon.querySelector('.nav-badge');
  if (count > 0) {
    if (!badge) {
      badge = document.createElement('span');
      badge.className = 'nav-badge';
      icon.appendChild(badge);
    }
    badge.textContent = count;
  } else if (badge) {
    badge.remove();
  }
}

function updateCartQuantity(cartId, qty) {
  if (qty < 1) qty = 1;
  fetch('cart_action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=update&cart_id=' + encodeURIComponent(cartId) + '&quantity=' + encodeURIComponent(qty)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('line-total-' + cartId).textContent = 'Rs. ' + data.line_total;
        document.getElementById('cart-grand-total').textContent = 'Rs. ' + data.grand_total;
        updateNavBadge('cart', data.cart_count);
      }
    });
}

function removeFromCart(cartId) {
  fetch('cart_action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=remove&cart_id=' + encodeURIComponent(cartId)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const row = document.getElementById('cart-row-' + cartId);
        if (row) {
          row.style.transition = 'opacity 0.3s ease';
          row.style.opacity = '0';
          setTimeout(() => row.remove(), 300);
        }
        document.getElementById('cart-grand-total').textContent = 'Rs. ' + data.grand_total;
        updateNavBadge('cart', data.cart_count);
        showToast('Removed from cart.', 'success');
        if (data.cart_count === 0) {
          setTimeout(() => window.location.reload(), 500);
        }
      }
    });
}
