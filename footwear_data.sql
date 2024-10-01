-- Tabel voor gebruikers
CREATE TABLE `User`(
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100),
    voornaam VARCHAR(100),
    password_hash VARCHAR(255),
    user_type VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    schoenmaat INT,
    actief BOOLEAN
);

-- Tabel voor adressen, met samengestelde primaire sleutel
CREATE TABLE Adres (
    address_id INT,
    user_id INT,
    straat VARCHAR(255),
    huisnummer VARCHAR(10),
    postcode VARCHAR(10),
    stad VARCHAR(100),
    land VARCHAR(100),
    PRIMARY KEY (address_id, user_id),
    FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

-- Tabel voor producten
CREATE TABLE Products (
    artikelnr INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100),
    prijs DECIMAL(10, 2),
    type_of_shoe VARCHAR(50)
);

-- Tabel voor productvarianten, zonder auto_increment voor variantnr, met samengestelde primaire sleutel
CREATE TABLE ProductVariant (
    artikelnr INT,
    variantnr INT,
    kleur VARCHAR(50),
    maat INT,
    stock INT,
    bought_counter INT,
    PRIMARY KEY (artikelnr, variantnr),
    FOREIGN KEY (artikelnr) REFERENCES Products(artikelnr)
);

-- Tabel voor winkelwagen, met samengestelde primaire sleutel en correcte foreign key referenties
CREATE TABLE Cart (
    user_id INT,
    artikelnr INT,
    variantnr INT,
    aantal INT,
    PRIMARY KEY (user_id, artikelnr, variantnr),
    FOREIGN KEY (artikelnr, variantnr) REFERENCES ProductVariant(artikelnr, variantnr),
    FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

-- Tabel voor betalingen
CREATE TABLE Betaling (
    bestelling_id INT AUTO_INCREMENT PRIMARY KEY,
    betalingsmethode VARCHAR(50),
    oorspronkelijke_prijs DECIMAL(10, 2),
    reductie DECIMAL(10, 2),
    eindprijs DECIMAL(10, 2)
);

-- Tabel voor facturen
CREATE TABLE Factuur (
    bestelling_id INT PRIMARY KEY,
    user_id INT,
    address_id INT,
    oorspronkelijke_prijs DECIMAL(10, 2),
    reductie DECIMAL(10, 2),
    betalingsmethode VARCHAR(50),
    FOREIGN KEY (bestelling_id) REFERENCES Betaling(bestelling_id)
);