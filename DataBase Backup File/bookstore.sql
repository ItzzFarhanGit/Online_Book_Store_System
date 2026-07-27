-- phpMyAdmin SQL Dump
-- Bookstore Full Project Database
-- Generated for: Online Book Zone
-- Import this file fully in phpMyAdmin to set up the project database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstore`
--
CREATE DATABASE IF NOT EXISTS `bookstore` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bookstore`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 20,
  `rating` decimal(2,1) NOT NULL DEFAULT 4.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `book_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `book_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `status` varchar(50) NOT NULL DEFAULT 'Processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `subject` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_book` (`username`,`book_id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Default Admin Account
-- Username: admin   Password: admin123
--
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2b$10$2tk5111fYLhhJlxPrGwoTeqA2Gegxcx.rlpxFrl5e0OmRiu5LFeyC');

--
-- Sample Book Data (uses images already in the project folder)
--
INSERT INTO `books` (`title`, `author`, `price`, `image`, `category`, `description`, `stock`, `rating`) VALUES
('Around the World in 80 Days', 'Jules Verne', 1250.00, '80.jpeg', 'Adventure', 'A classic adventure of Phileas Fogg racing around the globe against time.', 25, 4.5),
('Senlin Ascends', 'Josiah Bancroft', 1450.00, 'alchemised.jpeg', 'Fantasy', 'A man searches a vast living tower for his missing wife in this imaginative fantasy.', 18, 4.6),
('A History of Empires', 'Anonymous', 980.00, 'b.jpg', 'Biography', 'An academic look into the rise and fall of great civilizations.', 12, 4.0),
('A Heritage of Shadows', 'Madeleine Brent', 1100.00, 'brent.PNG', 'Romantic', 'A gothic romance full of mystery, secrets and old family heritage.', 15, 4.2),
('Our Darkest Night', 'Jennifer Robson', 1320.00, 'darkest.PNG', 'Biography', 'A powerful story of courage and sacrifice set during wartime.', 10, 4.4),
('Darkest Before the Dawn', 'Michael Anderle', 1190.00, 'dawn.PNG', 'Thriller', 'A gripping thriller that keeps you on the edge until the very last page.', 14, 4.1),
('A Dirty Job', 'Christopher Moore', 1050.00, 'dirty.jpeg', 'Comady', 'A hilarious supernatural comedy about death, life, and everything between.', 20, 4.3),
('Our Darkest Night', 'Michael Anderle', 1190.00, 'drk dawn.jpg', 'Thriller', 'Survival and grit in the face of overwhelming darkness.', 16, 4.0),
('The Girls Left Behind', 'Emily Gunnis', 1280.00, 'drk nght.jpg', 'Thriller', 'A haunting historical mystery spanning generations of secrets.', 13, 4.5),
('The Girls Left Behind', 'Emily Gunnis', 1280.00, 'girl left.jpg', 'Thriller', 'Two women, decades apart, connected by one terrible secret.', 11, 4.5),
('The Girl in the Letter', 'Emily Gunnis', 1240.00, 'girl.jpg', 'Romantic', 'A beautifully woven story of love letters lost and found.', 17, 4.6),
('Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 1650.00, 'harry potter.jpeg', 'Fantasy', 'The boy who lived discovers a magical world waiting for him at Hogwarts.', 30, 4.9),
('A History of Diamonds', 'Anonymous', 990.00, 'heart few.PNG', 'Biography', 'A few precious things make a heart whole — a touching collection of essays.', 9, 4.0),
('The Hitchhiker''s Guide to the Galaxy', 'Douglas Adams', 1380.00, 'hitch.jpeg', 'Comady', 'A wildly funny science-fiction classic about life, the universe, and everything.', 22, 4.7),
('The Hobbit', 'J.R.R. Tolkien', 1580.00, 'hobbit.jpeg', 'Fantasy', 'Bilbo Baggins joins a band of dwarves on an unexpected adventure to reclaim treasure.', 28, 4.8),
('Information & Communication Technology', 'ICT Press', 850.00, 'ict.jpeg', 'Children', 'A simple educational guide to modern computing for young learners.', 19, 4.0),
('Iron Flame', 'Rebecca Yarros', 1620.00, 'iron.jpeg', 'Fantasy', 'Violet Sorrengail returns to the brutal world of dragon riders in this fiery sequel.', 24, 4.6),
('The Year of Magical Thinking', 'Joan Didion', 1150.00, 'joan.jpeg', 'Biography', 'A profound memoir on grief, loss, and the search for meaning.', 8, 4.4),
('The House on the Lake', 'Nuala Ellwood', 1290.00, 'lake.PNG', 'Thriller', 'A chilling psychological thriller set beside a remote, secretive lake house.', 12, 4.3),
('The House on the Lake', 'Nuala Ellwood', 1290.00, 'lake.jpg', 'Thriller', 'Secrets rise to the surface in this tense lakeside mystery.', 12, 4.3),
('The Midnight Lamp', 'Anonymous', 1020.00, 'lamp.jpeg', 'Fantasy', 'A glowing tale of wonder, wishes, and the magic hidden in everyday objects.', 16, 4.1),
('Then She Was Gone', 'Lisa Jewell', 1340.00, 'lie.jpeg', 'Thriller', 'A missing daughter, a decade of grief, and a truth no one expected.', 14, 4.5),
('All the Light We Cannot See', 'Anthony Doerr', 1480.00, 'light.PNG', 'Biography', 'A Pulitzer Prize-winning story of two lives intertwined by war.', 21, 4.8),
('A Heritage of Shadows', 'Madeleine Brent', 1100.00, 'madeleine brent.jpg', 'Romantic', 'A young woman uncovers her family''s hidden past in Victorian England.', 15, 4.2),
('The Colour of Magic', 'Terry Pratchett', 1390.00, 'magic.jpeg', 'Comady', 'The hilarious start to the Discworld series, full of wit and satire.', 23, 4.6),
('The Adventures of Huckleberry Finn', 'Mark Twain', 990.00, 'mark.jpeg', 'Adventure', 'An American classic following Huck Finn''s journey down the Mississippi.', 18, 4.5),
('I Know Why the Caged Bird Sings', 'Maya Angelou', 1180.00, 'maya.jpeg', 'Biography', 'A landmark autobiography of resilience, identity, and self-discovery.', 13, 4.7),
('Good Omens', 'Neil Gaiman & Terry Pratchett', 1450.00, 'omens.jpeg', 'Comady', 'An angel and a demon team up to prevent the apocalypse — hilariously.', 26, 4.7),
('Only Murder', 'Rylie Dark', 1090.00, 'only.PNG', 'Thriller', 'A relentless detective story full of twists and small-town secrets.', 10, 4.0),
('Only Murder', 'Rylie Dark', 1090.00, 'only.jpg', 'Thriller', 'A gripping murder mystery that keeps readers guessing till the end.', 10, 4.0),
('The Fate of the Day', 'Rick Atkinson', 1620.00, 'rick.PNG', 'Biography', 'The second volume of the Revolution Trilogy chronicling America''s fight for independence.', 11, 4.6),
('The Fate of the Day', 'Rick Atkinson', 1620.00, 'rick.jpg', 'Biography', 'A masterful account of war, leadership and the birth of a nation.', 11, 4.6),
('From Goethe to Gundolf', 'Roger Paulin', 1340.00, 'roger paulin.jpg', 'Biography', 'A scholarly exploration of German literature and its lasting legacy.', 7, 4.0),
('From Goethe to Gundolf', 'Roger Paulin', 1340.00, 'roger.PNG', 'Biography', 'An academic journey through two centuries of literary tradition.', 7, 4.0),
('Alexander Hamilton', 'Ron Chernow', 1750.00, 'ron.jpeg', 'Biography', 'The definitive biography that inspired a Broadway revolution.', 20, 4.9),
('The Secret of Secrets', 'Dan Brown', 1690.00, 'secret.jpeg', 'Thriller', 'Robert Langdon returns in a pulse-pounding new puzzle of symbols and secrets.', 27, 4.4),
('The Girl on the Train', 'Paula Hawkins', 1260.00, 'see.jpg', 'Thriller', 'A psychological thriller about memory, addiction, and a crime witnessed from a train.', 19, 4.5),
('Then She Was Gone', 'Lisa Jewell', 1340.00, 'she.PNG', 'Thriller', 'A mother''s search for the truth about her daughter''s disappearance.', 14, 4.5),
('Then She Was Gone', 'Lisa Jewell', 1340.00, 'she.jpg', 'Thriller', 'Years after her daughter vanished, a mother finally finds answers.', 14, 4.5),
('The Silent Patient', 'Alex Michaelides', 1390.00, 'silent.jpeg', 'Thriller', 'A psychotherapist becomes obsessed with treating a woman who refuses to speak.', 25, 4.7),
('Solar Bones', 'Mike McCormack', 1140.00, 'solar.PNG', 'Biography', 'An unforgettable stream-of-consciousness novel about one man''s ordinary life.', 9, 4.2),
('Solar Bones', 'Mike McCormack', 1140.00, 'solar.jpg', 'Biography', 'A poetic meditation on family, memory, and the passage of time.', 9, 4.2),
('Tarzan of the Apes', 'Edgar Rice Burroughs', 980.00, 'tarzan.jpeg', 'Adventure', 'The classic adventure of a man raised by apes in the African jungle.', 16, 4.3),
('A Train at Midnight', 'Anonymous', 1010.00, 'train.jpeg', 'Thriller', 'A suspenseful journey where every passenger holds a secret.', 12, 4.0),
('Unbroken', 'Laura Hillenbrand', 1480.00, 'unbroken.jpeg', 'Biography', 'A true story of survival, resilience and redemption during World War II.', 22, 4.8),
('The Word in their Voices', 'Augustine Jeyaraj', 1120.00, 'voice.jpg', 'Children', 'An inspiring collection of stories told through the voices of young dreamers.', 14, 4.1),
('The Word in their Voices', 'Augustine Jeyaraj', 1120.00, 'word.PNG', 'Children', 'Stories of hope and courage written for and by children.', 14, 4.1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
