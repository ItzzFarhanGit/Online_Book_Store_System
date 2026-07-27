<?php
session_start();
require "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | Book Zone</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.about-intro { text-align: center; margin-top: 50px; padding: 0 20px; animation: bz-fade-in 0.6s ease; }
.about-intro h1 { color: var(--bz-red); font-size: 28px; }
.about-intro h2 { color: #555; font-weight: 400; font-size: 18px; max-width: 700px; margin: 10px auto; }

.about-body { display: flex; gap: 30px; align-items: center; max-width: 1100px; margin: 40px auto; padding: 0 24px; flex-wrap: wrap; }
.about-body img { flex: 0 0 380px; max-width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
.about-body p { flex: 1; min-width: 280px; line-height: 1.8; color: #333; text-align: justify; }

.vm-banner {
  background-color: var(--bz-black); border-radius: 10px; padding: 40px;
  display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px;
  max-width: 1100px; margin: 30px auto; animation: bz-slide-up 0.5s ease;
}
.vm-banner .vision, .vm-banner .mission { flex: 1; min-width: 250px; color: #fff; }
.vm-banner img.mv { width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; }
.vm-banner h2 { color: var(--bz-red); margin: 10px 0 8px; font-size: 19px; }
.vm-banner p { color: #ccc; line-height: 1.6; font-size: 14px; }

.partner-strip { display: flex; flex-wrap: wrap; justify-content: center; gap: 24px; padding: 30px 24px; max-width: 1100px; margin: 0 auto; }
.partner-strip img { max-height: 80px; object-fit: contain; filter: grayscale(20%); transition: transform 0.3s ease, filter 0.3s ease; }
.partner-strip img:hover { transform: scale(1.08); filter: none; }
</style>
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="page-banner">
  <h1>About Us</h1>
  <p><a href="home.php">Home</a> &gt; About</p>
</div>

<div class="about-intro">
  <p style="color:var(--bz-red); font-weight:700; letter-spacing:1px;">WHO WE ARE</p>
  <h1>Discover Our Journey, Vision, and Team</h1>
  <h2>Bookzone (Pvt) Ltd is truly a proper combination of tradition and modernity that brings the future to the present.</h2>
</div>

<div class="about-body">
  <img src="<?php echo bz_img('books.jpg'); ?>" alt="Books">
  <p>
    Over the last five decades, Bookzone has emerged as a prestigious and foremost network of bookshops
    in Sri Lanka, with rapid development and expansion of its business activities both in terms of quality and
    quantity. We present a huge collection of Sri Lankan and foreign titles across multiple disciplines to meet
    the diverse needs of our wide readership — from little kids to higher academics. We are trusted sellers of
    an extensive collection of educational items, stationery, and magazines through our network, and one of
    the major book importers in Sri Lanka, with long-standing relationships with renowned foreign publishers.
    At present we have 38 bookstores located in major towns across the country, and proudly claim to be the
    largest network of bookshops in Sri Lanka — now serving you online too.
  </p>
</div>

<div class="vm-banner">
  <div class="vision">
    <img src="<?php echo bz_img('vision.jpg'); ?>" class="mv">
    <h2>Our Vision</h2>
    <p>To become the most prominent institute storing educational books and titles on every subject in and out of
       Sri Lanka, building an intellectual and educated generation full of good morals.</p>
  </div>
  <div class="mission">
    <img src="<?php echo bz_img('mission.jpg'); ?>" class="mv">
    <h2>Our Mission</h2>
    <p>To satisfy our customers with quality service based on essential values, actively supporting the younger
       generation to improve their knowledge, attitudes, and talents — while safeguarding the prestigious name
       established by Online Bookzone as pioneers of this field.</p>
  </div>
</div>

<h2 class="section-title">Our <span>International Partners</span></h2>
<div class="partner-strip">
  <img src="<?php echo bz_img('marshal.jpg'); ?>" height="80">
  <img src="<?php echo bz_img('yamaha.jpg'); ?>" height="80">
  <img src="<?php echo bz_img('scholastic.png'); ?>" height="80">
  <img src="<?php echo bz_img('harper.jpg'); ?>" height="80">
  <img src="<?php echo bz_img('camb.png'); ?>" height="80">
  <img src="<?php echo bz_img('brown.png'); ?>" height="50">
  <img src="<?php echo bz_img('pearson.png'); ?>" height="50">
  <img src="<?php echo bz_img('macmillan.jpg'); ?>" height="80">
  <img src="<?php echo bz_img('kelly.jpg'); ?>" height="80">
</div>

<?php include "includes/footer.php"; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
