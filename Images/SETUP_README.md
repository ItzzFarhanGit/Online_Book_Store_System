# 📚 Online Book Zone — Full Project Setup Guide

This is your complete Bookstore project — built on top of your original files —
with a database-driven product catalog, cart, wishlist, order history, search/filter,
and a full Admin Panel. Theme is your original **black & red** design, polished and animated.

---

## 1. Requirements

- XAMPP / WAMP / LAMP (PHP 7.4+ and MySQL/MariaDB)
- A browser

---

## 2. Folder Setup

1. Copy the entire `bookstore` folder into your server's web root:
   - XAMPP (Windows): `C:\xampp\htdocs\bookstore`
   - XAMPP (Linux): `/opt/lampp/htdocs/bookstore`
   - WAMP: `C:\wamp64\www\bookstore`

2. Start **Apache** and **MySQL** from your XAMPP/WAMP control panel.

---

## 3. Database Setup

1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **Import**
3. Choose the file: `DataBase Backup File/bookstore.sql`
4. Click **Go**

This single file will:
- Create the `bookstore` database
- Create all tables: `users`, `admins`, `books`, `cart`, `wishlist`, `orders`, `order_items`, `contact`
- Insert a default **Admin account**
- Insert **47 sample books** (using the cover images already in your project folder)

> If you already have an older `bookstore` database from before, drop it first
> (right-click the database in phpMyAdmin → Drop), then import this new file fresh.

---

## 4. Running the Project

Visit: `http://localhost/bookstore/home.php`

You'll be redirected to the login page since you're not logged in yet.

### Customer Account
- Click **Sign Up** to create a new customer account, or
- Use any account you previously created (old `users` table data carries over if you didn't drop it)

### Admin Panel
- URL: `http://localhost/bookstore/admin_login.php`
- **Username:** `admin`
- **Password:** `admin123`

⚠️ Change this password after logging in for the first time in a real deployment
(there's no "change password" UI yet — you can update it directly in phpMyAdmin
using PHP's `password_hash()` if needed, or ask for that feature to be added).

---

## 5. What's Included

### Customer-facing pages
| Page | Description |
|---|---|
| `home.php` | Hero banner, Top Rated & New Arrivals (from DB) |
| `products.php` | Full catalog with category filter chips |
| `search.php` | Live search by title/author + category filter |
| `book_details.php` | Single book page with related books |
| `cart.php` | Cart with quantity +/− controls, live AJAX updates |
| `checkout.php` | Delivery details form → creates a real Order |
| `order_history.php` | Customer's past orders with status |
| `wishlist.php` | Saved books (heart icon toggles instantly via AJAX) |
| `about.php` / `contact.php` | Polished versions of your original pages |
| `login.php` / `signup.php` | Secure auth (password hashing + prepared statements) |

### Admin Panel (`/admin/`)
| Page | Description |
|---|---|
| `dashboard.php` | Stats: total books, users, orders, revenue, low stock, messages |
| `books.php` | Add / Edit / Delete books (full CRUD) |
| `orders.php` | View all orders, update order status |
| `messages.php` | View contact form submissions |

### Behind the scenes
- `cart_action.php` / `wishlist_action.php` — AJAX endpoints (no page reload needed)
- `includes/header.php`, `includes/footer.php`, `includes/book_card.php` — shared components
- `assets/css/style.css` — your black/red theme, polished, with animations
- `assets/js/main.js` — Add-to-cart, wishlist toggle, quantity updates

---

## 6. Security Notes (already handled for you)

- All passwords are hashed with `password_hash()` / verified with `password_verify()`
- All SQL queries use **prepared statements** (no SQL injection risk)
- Every protected page checks `$_SESSION['username']` before showing content
- Session ID is regenerated on login/signup to prevent session fixation

---

## 7. Adding More Books

Easiest way: Admin Panel → Manage Books → fill the "Add New Book" form.
Just type the **exact image filename** (e.g. `hobbit.jpeg`) — make sure that file
is sitting in your main `bookstore/` folder.

---

## 8. Common Issues

| Problem | Fix |
|---|---|
| "Connection failed" error | Make sure MySQL is running and `db.php` credentials match your setup |
| Images not showing | Check the filename in the `books` table matches exactly (case-sensitive on Linux) |
| Blank white page | Turn on PHP errors temporarily: add `error_reporting(E_ALL); ini_set('display_errors',1);` at the top of `db.php` |
| Admin login not working | Re-import `bookstore.sql` — it resets the admin password to `admin123` |

---

Built with ❤ on top of your original Book Zone project.
