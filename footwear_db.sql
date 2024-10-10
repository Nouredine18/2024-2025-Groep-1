-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 10 okt 2024 om 17:35
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `footwear_db`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `adres`
--

CREATE TABLE `adres` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `straat` varchar(255) DEFAULT NULL,
  `huisnummer` varchar(10) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `stad` varchar(100) DEFAULT NULL,
  `land` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `artikelnr` int(11) NOT NULL,
  `variantnr` int(11) NOT NULL,
  `aantal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `cart`
--

INSERT INTO `cart` (`user_id`, `artikelnr`, `variantnr`, `aantal`) VALUES
(1, 2, 1, 1),
(1, 3, 1, 2),
(2, 1, 1, 1),
(2, 1, 2, 1),
(4, 2, 1, 2),
(4, 2, 2, 1),
(4, 3, 2, 1);

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

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `products`
--

CREATE TABLE `products` (
  `artikelnr` int(11) NOT NULL,
  `naam` varchar(100) DEFAULT NULL,
  `prijs` decimal(10,2) DEFAULT NULL,
  `type_of_shoe` varchar(50) DEFAULT NULL,
  `directory` varchar(255) DEFAULT NULL,
  `product_information` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `products`
--

INSERT INTO `products` (`artikelnr`, `naam`, `prijs`, `type_of_shoe`, `directory`, `product_information`) VALUES
(1, 'Nike Air Max', 120.00, 'Sneaker', 'nike_air_max.jpg', 'The Nike Air Max is a popular sneaker known for its air cushioning, providing comfort and style. Perfect for casual wear and sporting activities.'),
(2, 'Adidas Ultraboost', 150.00, 'Running Shoe', 'adidas_ultraboost.jpg', 'The Adidas Ultraboost is a running shoe designed for maximum energy return. Its lightweight Primeknit upper offers breathability and comfort during long runs.'),
(3, 'Converse All Star', 80.00, 'Casual Shoe', 'converse_all_star.jpg', 'The Converse All Star is an iconic casual shoe that never goes out of style. Featuring a durable canvas upper and rubber sole, it is perfect for everyday wear.'),
(4, 'Timberland Boot', 200.00, 'Boot', 'timberland_boot.jpg', 'The Timberland Boot is a rugged and durable boot, designed for tough outdoor conditions. It features premium leather and a waterproof design to keep your feet dry.'),
(5, 'Puma RS-X', 110.00, 'Sport Shoe', 'puma_RSX.jpg', 'The Puma RS-X is a sport shoe that combines retro design with modern technology. It offers excellent cushioning and support, perfect for high-intensity activities.');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `productvariant`
--

CREATE TABLE `productvariant` (
  `artikelnr` int(11) NOT NULL,
  `variantnr` int(11) NOT NULL,
  `kleur` varchar(50) DEFAULT NULL,
  `maat` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `bought_counter` int(11) DEFAULT NULL,
  `variant_directory` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `productvariant`
--

INSERT INTO `productvariant` (`artikelnr`, `variantnr`, `kleur`, `maat`, `stock`, `bought_counter`, `variant_directory`) VALUES
(1, 1, 'black', 42, 10, 0, NULL),
(1, 2, 'white', 43, 5, 0, NULL),
(2, 1, 'red', 40, 20, 0, NULL),
(2, 2, 'blue', 41, 15, 0, NULL),
(3, 1, 'green', 44, 8, 0, NULL),
(3, 2, 'black', 45, 12, 0, NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artikelnr` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(1) DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `artikelnr`, `review_text`, `rating`, `review_date`) VALUES
(1, 4, 3, 'good', 4, '2024-10-10 17:25:56'),
(2, 4, 3, 'To be honest I excpected better from this shoe', 4, '2024-10-10 17:26:24'),
(3, 4, 3, 'zd', 4, '2024-10-10 17:33:59'),
(4, 4, 1, 'what', 4, '2024-10-10 17:34:26');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `naam` varchar(100) DEFAULT NULL,
  `voornaam` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `schoenmaat` int(11) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `user`
--

INSERT INTO `user` (`user_id`, `naam`, `voornaam`, `password_hash`, `user_type`, `email`, `schoenmaat`, `actief`) VALUES
(1, 'Nouredine', 'Tahrioui', '$2y$10$uMlpcwplnV4kla.9VQ9jYe5wXCPs9XdIJGVLtL9zzy7d09LGJ7kf6', 'admin', 'nouredine.tahrioui@gmail.com', 40, 1),
(2, 'Balhaar', 'Bram', '$2y$10$Vvw7UxN3f.NAB7GquNYQ2ujMTkKRpgwlH3I9RE4vFDuChEl0Col52', 'user', 'bram@gmail.com', 39, 1),
(3, 'Galardo EL', 'dante', '$2y$10$gOYBsWBDSQJZTNyjCs6FJuSrCWvpw7FbY7pjLq0P8KDWfzj42z/fq', 'user', 'dante@gmail.com', 40, 1),
(4, 'Tahrioui', 'Nouredine', '$2y$10$nWpb17mUJW1b7YmoUztxB.mEqkiNc3S7IiGiO5NlbWXSVIDlLoDmW', 'admin', 'admin@gmail.com', 39, 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `adres`
--
ALTER TABLE `adres`
  ADD PRIMARY KEY (`address_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `betaling`
--
ALTER TABLE `betaling`
  ADD PRIMARY KEY (`bestelling_id`);

--
-- Indexen voor tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`artikelnr`,`variantnr`),
  ADD KEY `artikelnr` (`artikelnr`,`variantnr`);

--
-- Indexen voor tabel `factuur`
--
ALTER TABLE `factuur`
  ADD PRIMARY KEY (`bestelling_id`);

--
-- Indexen voor tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`artikelnr`);

--
-- Indexen voor tabel `productvariant`
--
ALTER TABLE `productvariant`
  ADD PRIMARY KEY (`artikelnr`,`variantnr`);

--
-- Indexen voor tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artikelnr` (`artikelnr`);

--
-- Indexen voor tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `betaling`
--
ALTER TABLE `betaling`
  MODIFY `bestelling_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `products`
--
ALTER TABLE `products`
  MODIFY `artikelnr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT voor een tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `adres`
--
ALTER TABLE `adres`
  ADD CONSTRAINT `adres_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Beperkingen voor tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`artikelnr`,`variantnr`) REFERENCES `productvariant` (`artikelnr`, `variantnr`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Beperkingen voor tabel `factuur`
--
ALTER TABLE `factuur`
  ADD CONSTRAINT `factuur_ibfk_1` FOREIGN KEY (`bestelling_id`) REFERENCES `betaling` (`bestelling_id`);

--
-- Beperkingen voor tabel `productvariant`
--
ALTER TABLE `productvariant`
  ADD CONSTRAINT `productvariant_ibfk_1` FOREIGN KEY (`artikelnr`) REFERENCES `products` (`artikelnr`);

--
-- Beperkingen voor tabel `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_fk` FOREIGN KEY (`artikelnr`) REFERENCES `products` (`artikelnr`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
