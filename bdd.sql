-- Table: `user`
--
CREATE TABLE user (
    id INT AUTO_INCREMENT NOT NULL,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    credits INT NOT NULL DEFAULT 20,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    pseudo VARCHAR(255) NOT NULL,
    updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    profile_picture_filename VARCHAR(255) DEFAULT NULL,
    desired_role VARCHAR(255) NOT NULL DEFAULT 'passenger',
    PRIMARY KEY(id),
    UNIQUE KEY UNIQ_IDENTIFIER_EMAIL (email),
    UNIQUE KEY UNIQ_IDENTIFIER_PSEUDO (pseudo)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `user_preference`
--
CREATE TABLE user_preference (
    id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    is_smoker TINYINT(1) NOT NULL DEFAULT 0,
    accepts_animals TINYINT(1) NOT NULL DEFAULT 0,
    additional_info LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id),
    UNIQUE KEY UNIQ_USER_ID (user_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `vehicle`
--
CREATE TABLE vehicle (
    id INT AUTO_INCREMENT NOT NULL,
    owner_id INT DEFAULT NULL,
    brand VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    color VARCHAR(255) NOT NULL,
    license_plate VARCHAR(255) NOT NULL,
    seats INT NOT NULL,
    type VARCHAR(255) NOT NULL,
    is_electric TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY(id),
    INDEX IDX_1B80E4867E3C61F9 (owner_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `trip`
--
CREATE TABLE trip (
    id INT AUTO_INCREMENT NOT NULL,
    driver_id INT NOT NULL,
    vehicle_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime)',
    updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime)',
    departure_location VARCHAR(255) NOT NULL,
    destination_location VARCHAR(255) NOT NULL,
    departure_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    arrival_time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    available_seats INT NOT NULL,
    price_per_seat DOUBLE PRECISION NOT NULL,
    description LONGTEXT DEFAULT NULL,
    is_smoking_allowed TINYINT(1) NOT NULL,
    are_animals_allowed TINYINT(1) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'scheduled',
    PRIMARY KEY(id),
    INDEX IDX_7653F05C337CD269 (driver_id),
    INDEX IDX_7653F05C545317D1 (vehicle_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `booking`
--
CREATE TABLE booking (
    id INT AUTO_INCREMENT NOT NULL,
    user_id INT DEFAULT NULL,
    trip_id INT DEFAULT NULL,
    booked_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    status VARCHAR(255) NOT NULL DEFAULT 'pending_validation',
    seats INT NOT NULL,
    PRIMARY KEY(id),
    INDEX IDX_E00CEDDE76ED395 (user_id),
    INDEX IDX_E00CEDDEA5BC2E0E (trip_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `report`
--
CREATE TABLE report (
    id INT AUTO_INCREMENT NOT NULL,
    reporter_id INT NOT NULL,
    reported_trip_id INT NOT NULL,
    reported_user_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    PRIMARY KEY(id),
    INDEX IDX_C42B3209E1CF3E7 (reporter_id),
    INDEX IDX_C42B32092D6664D6 (reported_trip_id),
    INDEX IDX_C42B320920C06A98 (reported_user_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `service`
--
CREATE TABLE service (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Table: `employee`
--
CREATE TABLE employee (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    salary DOUBLE PRECISION NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

--
-- Ajout des contraintes de clés étrangères (Foreign Keys)
--

ALTER TABLE user_preference ADD CONSTRAINT FK_8A30B33A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4867E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id);
ALTER TABLE trip ADD CONSTRAINT FK_7653F05C337CD269 FOREIGN KEY (driver_id) REFERENCES user (id);
ALTER TABLE trip ADD CONSTRAINT FK_7653F05C545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id);
ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id);
ALTER TABLE report ADD CONSTRAINT FK_C42B3209E1CF3E7 FOREIGN KEY (reporter_id) REFERENCES user (id);
ALTER TABLE report ADD CONSTRAINT FK_C42B32092D6664D6 FOREIGN KEY (reported_trip_id) REFERENCES trip (id);
ALTER TABLE report ADD CONSTRAINT FK_C42B320920C06A98 FOREIGN KEY (reported_user_id) REFERENCES user (id);
