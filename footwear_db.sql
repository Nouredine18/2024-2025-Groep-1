-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 06 okt 2024 om 21:36
-- Serverversie: 10.4.28-MariaDB
-- PHP-versie: 8.2.4

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
-- Tabelstructuur voor tabel `Adres`
--

CREATE TABLE `Adres` (
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
-- Tabelstructuur voor tabel `Betaling`
--

CREATE TABLE `Betaling` (
  `bestelling_id` int(11) NOT NULL,
  `betalingsmethode` varchar(50) DEFAULT NULL,
  `oorspronkelijke_prijs` decimal(10,2) DEFAULT NULL,
  `reductie` decimal(10,2) DEFAULT NULL,
  `eindprijs` decimal(10,2) DEFAULT NULL
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

--
-- Gegevens worden geëxporteerd voor tabel `Cart`
--

INSERT INTO `Cart` (`user_id`, `artikelnr`, `variantnr`, `aantal`) VALUES
(1, 2, 1, 1),
(1, 3, 1, 2),
(2, 1, 1, 1),
(2, 1, 2, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Factuur`
--

CREATE TABLE `Factuur` (
  `bestelling_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `oorspronkelijke_prijs` decimal(10,2) DEFAULT NULL,
  `reductie` decimal(10,2) DEFAULT NULL,
  `betalingsmethode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Products`
--

CREATE TABLE `Products` (
  `artikelnr` int(11) NOT NULL,
  `naam` varchar(100) DEFAULT NULL,
  `prijs` decimal(10,2) DEFAULT NULL,
  `type_of_shoe` varchar(50) DEFAULT NULL,
  `directory` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Products`
--

INSERT INTO `Products` (`artikelnr`, `naam`, `prijs`, `type_of_shoe`, `directory`) VALUES
(1, 'Nike Air Max', 120.00, 'Sneaker', NULL),
(2, 'Adidas Ultraboost', 150.00, 'Running Shoe', NULL),
(3, 'Converse All Star', 80.00, 'Casual Shoe', NULL),
(4, 'Timberland Boot', 200.00, 'Boot', NULL),
(5, 'Puma RS-X', 110.00, 'Sport Shoe', NULL);

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
  `bought_counter` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `ProductVariant`
--

INSERT INTO `ProductVariant` (`artikelnr`, `variantnr`, `kleur`, `maat`, `stock`, `bought_counter`) VALUES
(1, 1, 'Zwart', 42, 10, 0),
(1, 2, 'Wit', 43, 5, 0),
(2, 1, 'Rood', 40, 20, 0),
(2, 2, 'Blauw', 41, 15, 0),
(3, 1, 'Groen', 44, 8, 0),
(3, 2, 'Zwart', 45, 12, 0);

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
  `actief` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `User`
--

INSERT INTO `User` (`user_id`, `naam`, `voornaam`, `password_hash`, `user_type`, `email`, `schoenmaat`, `actief`) VALUES
(1, 'Nouredine', 'Tahrioui', '$2y$10$uMlpcwplnV4kla.9VQ9jYe5wXCPs9XdIJGVLtL9zzy7d09LGJ7kf6', 'admin', 'nouredine.tahrioui@gmail.com', 40, 1),
(2, 'Balhaar', 'Bram', '$2y$10$Vvw7UxN3f.NAB7GquNYQ2ujMTkKRpgwlH3I9RE4vFDuChEl0Col52', 'user', 'bram@gmail.com', 39, 1),
(3, 'Galardo EL', 'dante', '$2y$10$gOYBsWBDSQJZTNyjCs6FJuSrCWvpw7FbY7pjLq0P8KDWfzj42z/fq', 'user', 'dante@gmail.com', 40, 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `Adres`
--
ALTER TABLE `Adres`
  ADD PRIMARY KEY (`address_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `Betaling`
--
ALTER TABLE `Betaling`
  ADD PRIMARY KEY (`bestelling_id`);

--
-- Indexen voor tabel `Cart`
--
ALTER TABLE `Cart`
  ADD PRIMARY KEY (`user_id`,`artikelnr`,`variantnr`),
  ADD KEY `artikelnr` (`artikelnr`,`variantnr`);

--
-- Indexen voor tabel `Factuur`
--
ALTER TABLE `Factuur`
  ADD PRIMARY KEY (`bestelling_id`);

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
-- Indexen voor tabel `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Betaling`
--
ALTER TABLE `Betaling`
  MODIFY `bestelling_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Products`
--
ALTER TABLE `Products`
  MODIFY `artikelnr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor een tabel `Reviews`
--
ALTER TABLE `Reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `User`
--
ALTER TABLE `User`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `Adres`
--
ALTER TABLE `Adres`
  ADD CONSTRAINT `adres_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

--
-- Beperkingen voor tabel `Cart`
--
ALTER TABLE `Cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`artikelnr`,`variantnr`) REFERENCES `ProductVariant` (`artikelnr`, `variantnr`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

--
-- Beperkingen voor tabel `Factuur`
--
ALTER TABLE `Factuur`
  ADD CONSTRAINT `factuur_ibfk_1` FOREIGN KEY (`bestelling_id`) REFERENCES `Betaling` (`bestelling_id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
