-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 25 nov 2024 om 09:37
-- Serverversie: 10.11.9-MariaDB
-- PHP-versie: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u220407022_dbfootwear`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `admin_chat`
--

CREATE TABLE `admin_chat` (
  `message_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `admin_chat`
--

INSERT INTO `admin_chat` (`message_id`, `admin_id`, `message`, `timestamp`) VALUES
(1, 25, 'hey', '2024-11-14 18:33:34'),
(2, 25, 'hoe gaat het', '2024-11-14 18:33:40'),
(3, 25, 'goed', '2024-11-14 18:33:52'),
(4, 25, 'hoezo', '2024-11-14 18:33:59'),
(5, 25, 'hey', '2024-11-14 18:36:05'),
(6, 25, 'hallo', '2024-11-14 18:36:13'),
(7, 25, 'hallo', '2024-11-14 18:36:17'),
(8, 25, 'z', '2024-11-14 18:36:25'),
(9, 25, 'z', '2024-11-14 18:36:28'),
(10, 25, 'z', '2024-11-14 18:36:33'),
(11, 101, 'hey', '2024-11-14 23:16:38'),
(12, 101, 'hoe gaat ie\\r\\n', '2024-11-14 23:16:44');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Adres`
--

CREATE TABLE `Adres` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `straat` varchar(255) NOT NULL,
  `huisnummer` varchar(10) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `stad` varchar(100) NOT NULL,
  `land` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `betaling`
--

CREATE TABLE `betaling` (
  `bestelling_id` int(11) NOT NULL,
  `betalingsmethode` varchar(50) DEFAULT NULL,
  `oorspronkelijke_prijs` decimal(10,2) DEFAULT NULL,
  `reductie` decimal(10,2) DEFAULT NULL,
  `eindprijs` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `betaling`
--

INSERT INTO `betaling` (`bestelling_id`, `betalingsmethode`, `oorspronkelijke_prijs`, `reductie`, `eindprijs`) VALUES
(1, 'Credit Card', 100.00, 10.00, 90.00),
(2, 'PayPal', 200.00, 20.00, 180.00),
(3, 'Credit Card', 150.00, 15.00, 135.00);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `BoughtProducts`
--

CREATE TABLE `BoughtProducts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artikelnr` int(11) NOT NULL,
  `variantnr` int(11) NOT NULL,
  `aantal` int(11) NOT NULL,
  `status` enum('in behandeling','verzonden','geleverd','geannuleerd') NOT NULL DEFAULT 'in behandeling',
  `koopdatum` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Cart`
--

CREATE TABLE `Cart` (
  `user_id` int(11) NOT NULL,
  `artikelnr` int(11) NOT NULL,
  `variantnr` int(11) NOT NULL,
  `aantal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Categorie`
--

CREATE TABLE `Categorie` (
  `categorie_id` int(11) NOT NULL,
  `categorie` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Categorie`
--

INSERT INTO `Categorie` (`categorie_id`, `categorie`) VALUES
(4, 'Hakken'),
(5, 'Balletschoenen'),
(6, 'Instappers'),
(7, 'Chelsea Boots'),
(8, 'Flip-Flops'),
(9, 'Bottines'),
(10, 'Sport schoenen'),
(11, 'lopez'),
(12, 'aaa');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `customer_support`
--

CREATE TABLE `customer_support` (
  `support_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `customer_support`
--

INSERT INTO `customer_support` (`support_id`, `user_id`, `admin_id`, `question`, `answer`, `timestamp`) VALUES
(1, 26, 25, 'je stinkt', 'hoezo', '2024-11-14 18:37:53'),
(2, 26, 25, 'hello', 'hey', '2024-11-14 19:05:29'),
(3, 1, NULL, 'hey something aint working', NULL, '2024-11-14 23:10:48'),
(4, 102, 101, 'zemmer help', 'isg man bel me gwn', '2024-11-14 23:18:39'),
(5, 102, NULL, 'fout databank', NULL, '2024-11-15 13:10:49'),
(6, 102, NULL, 'help', NULL, '2024-11-15 13:11:27');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `discount_codes`
--

INSERT INTO `discount_codes` (`id`, `code`, `discount_percentage`, `expiration_date`, `created_at`) VALUES
(1, 'HELLO4', 26.00, '2025-09-23', '2024-10-19 17:46:35'),
(2, 'HELLO4', 20.00, '2025-04-23', '2024-10-19 17:46:56'),
(3, 'HELLO4', 23.00, '0000-00-00', '2024-10-19 18:19:16'),
(4, 'HELLO4', 23.00, '2030-04-24', '2024-10-19 18:19:37'),
(5, 'hello ', 56.00, '2005-03-21', '2024-10-21 09:53:57');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `factuur`
--

CREATE TABLE `factuur` (
  `bestelling_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `oorspronkelijke_prijs` decimal(10,2) DEFAULT NULL,
  `reductie` decimal(10,2) DEFAULT NULL,
  `betalingsmethode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `factuur`
--

INSERT INTO `factuur` (`bestelling_id`, `user_id`, `address_id`, `oorspronkelijke_prijs`, `reductie`, `betalingsmethode`) VALUES
(1, 1, 101, 100.00, 10.00, 'Credit Card'),
(2, 1, 102, 200.00, 20.00, 'PayPal'),
(3, 2, 103, 150.00, 15.00, 'Credit Card');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `method_name` varchar(50) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_name`, `is_enabled`) VALUES
(1, 'Stripe', 1),
(2, 'PayPal', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Products`
--

CREATE TABLE `Products` (
  `artikelnr` int(11) NOT NULL,
  `naam` varchar(100) DEFAULT NULL,
  `prijs` decimal(10,2) DEFAULT NULL,
  `type_of_shoe` int(50) DEFAULT NULL,
  `directory` varchar(255) DEFAULT NULL,
  `product_information` text DEFAULT NULL,
  `merk` varchar(255) DEFAULT NULL,
  `popularity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Products`
--

INSERT INTO `Products` (`artikelnr`, `naam`, `prijs`, `type_of_shoe`, `directory`, `product_information`, `merk`, `popularity`) VALUES
(1, 'Nike Air Max', 120.00, 0, 'nike_air_max.jpg', 'The Nike Air Max is a popular sneaker known for its air cushioning, providing comfort and style. Perfect for casual wear and sporting activities.', NULL, 0),
(2, 'Adidas Ultraboost', 150.00, 0, 'adidas_ultraboost.jpg', 'The Adidas Ultraboost is a running shoe designed for maximum energy return. Its lightweight Primeknit upper offers breathability and comfort during long runs.', NULL, 0),
(3, 'Converse All Star', 80.00, 0, 'converse_all_star.jpg', 'The Converse All Star is an iconic casual shoe that never goes out of style. Featuring a durable canvas upper and rubber sole, it is perfect for everyday wear.', NULL, 0),
(4, 'Timberland Boot', 200.00, 0, 'timberland_boot.jpg', 'The Timberland Boot is a rugged and durable boot, designed for tough outdoor conditions. It features premium leather and a waterproof design to keep your feet dry.', NULL, 0),
(5, 'Puma RS-X', 110.00, 0, 'puma_rsx.jpg', 'The Puma RS-X is a sport shoe that combines retro design with modern technology. It offers excellent cushioning and support, perfect for high-intensity activities.', NULL, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ProductVariant`
--

CREATE TABLE `ProductVariant` (
  `artikelnr` int(11) NOT NULL,
  `variantnr` int(11) NOT NULL,
  `kleur` varchar(50) DEFAULT NULL,
  `maat` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `bought_counter` int(11) DEFAULT NULL,
  `variant_directory` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Reviews`
--

CREATE TABLE `Reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artikelnr` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(1) DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` int(11) NOT NULL,
  `method_name` varchar(50) NOT NULL,
  `base_cost` decimal(10,2) NOT NULL,
  `cost_per_kg` decimal(10,2) NOT NULL,
  `delivery_time` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `method_name`, `base_cost`, `cost_per_kg`, `delivery_time`) VALUES
(3, 'snel', 2.00, 2.00, '1-3 dagen');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `shipping_orders`
--

CREATE TABLE `shipping_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `shipping_method` int(11) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `SocialMediaLinks`
--

CREATE TABLE `SocialMediaLinks` (
  `id` int(11) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `SocialMediaLinks`
--

INSERT INTO `SocialMediaLinks` (`id`, `platform`, `link`) VALUES
(13, 'Facebook', 'https://www.facebook.com/'),
(17, 'instagram', 'https://www.instagram.com/');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `StructuurBezorgopties`
--

CREATE TABLE `StructuurBezorgopties` (
  `BezorgoptieID` int(11) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `Kosten` decimal(10,2) NOT NULL,
  `VerwachteLevertijd` varchar(50) NOT NULL,
  `IsActief` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `StructuurBezorgopties`
--

INSERT INTO `StructuurBezorgopties` (`BezorgoptieID`, `Naam`, `Kosten`, `VerwachteLevertijd`, `IsActief`) VALUES
(1, 'Standaard Levering', 5.00, '3-5 dagen', 1),
(2, 'Snelle Levering', 10.00, '1-2 dagen', 1),
(3, 'Same Day Delivery', 20.00, 'Op dezelfde dag', 0),
(4, 'Standaard Levering', 5.00, '3-5 dagen', 1),
(5, 'Snelle Levering', 10.00, '1-2 dagen', 1),
(6, 'Same Day Delivery', 20.00, 'Op dezelfde dag', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `User`
--

CREATE TABLE `User` (
  `user_id` int(11) NOT NULL,
  `naam` varchar(100) DEFAULT NULL,
  `voornaam` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `schoenmaat` int(11) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `User`
--

INSERT INTO `User` (`user_id`, `naam`, `voornaam`, `password_hash`, `user_type`, `email`, `schoenmaat`, `actief`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'hond', 'bert', '$2y$10$JC9PT2zYqT79S/shhDaJWOK3pP0jcghyIJdYxqnQAR6hKDxl3o/2e', 'user', 'john.doe@example.com', NULL, 1, NULL, NULL),
(101, 'e', 'laur', '$2y$10$2BH.OERbHzZesqtsN56.bOAta1cUofqvFEL6EaiI/F6XyTE994cki', 'admin', 'yazraknoureddine3@gmail.com', NULL, 1, NULL, NULL),
(104, 'Van Ballaert', 'DevBrambo', '', 'user', 'bramvanballaert@gmail.com', NULL, 1, NULL, NULL),
(106, 'hi', 'hi', '$2y$10$4mww/jt4hyKBcLFAbz5b1una6N8tWRjvlJrOB/UZwnuT9C5.3Dja6', 'admin', 'hi@gmail.com', NULL, 1, NULL, NULL),
(107, 'Van Ballaert', 'Bram', '', 'admin', 'bram.vanballaert@feralstorm.com', NULL, 1, NULL, NULL);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `admin_chat`
--
ALTER TABLE `admin_chat`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexen voor tabel `Adres`
--
ALTER TABLE `Adres`
  ADD PRIMARY KEY (`address_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `BoughtProducts`
--
ALTER TABLE `BoughtProducts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artikelnr` (`artikelnr`,`variantnr`);

--
-- Indexen voor tabel `Cart`
--
ALTER TABLE `Cart`
  ADD PRIMARY KEY (`user_id`,`artikelnr`,`variantnr`),
  ADD KEY `artikelnr` (`artikelnr`,`variantnr`);

--
-- Indexen voor tabel `Categorie`
--
ALTER TABLE `Categorie`
  ADD PRIMARY KEY (`categorie_id`);

--
-- Indexen voor tabel `customer_support`
--
ALTER TABLE `customer_support`
  ADD PRIMARY KEY (`support_id`);

--
-- Indexen voor tabel `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `method_name` (`method_name`);

--
-- Indexen voor tabel `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`artikelnr`);

--
-- Indexen voor tabel `ProductVariant`
--
ALTER TABLE `ProductVariant`
  ADD PRIMARY KEY (`artikelnr`,`variantnr`);

--
-- Indexen voor tabel `Reviews`
--
ALTER TABLE `Reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artikelnr` (`artikelnr`);

--
-- Indexen voor tabel `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `shipping_orders`
--
ALTER TABLE `shipping_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipping_method` (`shipping_method`);

--
-- Indexen voor tabel `SocialMediaLinks`
--
ALTER TABLE `SocialMediaLinks`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `StructuurBezorgopties`
--
ALTER TABLE `StructuurBezorgopties`
  ADD PRIMARY KEY (`BezorgoptieID`);

--
-- Indexen voor tabel `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `admin_chat`
--
ALTER TABLE `admin_chat`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT voor een tabel `Adres`
--
ALTER TABLE `Adres`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor een tabel `BoughtProducts`
--
ALTER TABLE `BoughtProducts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Categorie`
--
ALTER TABLE `Categorie`
  MODIFY `categorie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT voor een tabel `customer_support`
--
ALTER TABLE `customer_support`
  MODIFY `support_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT voor een tabel `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor een tabel `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `Products`
--
ALTER TABLE `Products`
  MODIFY `artikelnr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT voor een tabel `Reviews`
--
ALTER TABLE `Reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT voor een tabel `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `shipping_orders`
--
ALTER TABLE `shipping_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `SocialMediaLinks`
--
ALTER TABLE `SocialMediaLinks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT voor een tabel `StructuurBezorgopties`
--
ALTER TABLE `StructuurBezorgopties`
  MODIFY `BezorgoptieID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT voor een tabel `User`
--
ALTER TABLE `User`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `Adres`
--
ALTER TABLE `Adres`
  ADD CONSTRAINT `Adres_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `BoughtProducts`
--
ALTER TABLE `BoughtProducts`
  ADD CONSTRAINT `BoughtProducts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `BoughtProducts_ibfk_2` FOREIGN KEY (`artikelnr`,`variantnr`) REFERENCES `ProductVariant` (`artikelnr`, `variantnr`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `Cart`
--
ALTER TABLE `Cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`artikelnr`,`variantnr`) REFERENCES `ProductVariant` (`artikelnr`, `variantnr`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

--
-- Beperkingen voor tabel `ProductVariant`
--
ALTER TABLE `ProductVariant`
  ADD CONSTRAINT `productvariant_ibfk_1` FOREIGN KEY (`artikelnr`) REFERENCES `Products` (`artikelnr`);

--
-- Beperkingen voor tabel `Reviews`
--
ALTER TABLE `Reviews`
  ADD CONSTRAINT `reviews_product_fk` FOREIGN KEY (`artikelnr`) REFERENCES `Products` (`artikelnr`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_fk` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `shipping_orders`
--
ALTER TABLE `shipping_orders`
  ADD CONSTRAINT `shipping_orders_ibfk_1` FOREIGN KEY (`shipping_method`) REFERENCES `shipping_methods` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
