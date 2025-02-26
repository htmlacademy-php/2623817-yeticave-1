CREATE SCHEMA IF NOT EXISTS `yeticave_1` DEFAULT CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `contact_info` VARCHAR(120) NOT NULL,
  `registration_date` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`categories` (
  `id` VARCHAR(45) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`lots` (
  `id` INT NOT NULL,
  `create_date` TIMESTAMP NOT NULL,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NULL,
  `image_path` VARCHAR(260) NULL,
  `start_price` DECIMAL(15,2) NOT NULL,
  `expiration_date` TIMESTAMP NOT NULL,
  `price_step` INT NOT NULL,
  `author` INT NULL,
  `winner` INT NULL,
  `category` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `author_idx` (`author` ASC) VISIBLE,
  INDEX `winner_idx` (`winner` ASC) VISIBLE,
  INDEX `category_idx` (`category` ASC) VISIBLE,
  CONSTRAINT `author`
    FOREIGN KEY (`author`)
    REFERENCES `yeticave_1`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `winner`
    FOREIGN KEY (`winner`)
    REFERENCES `yeticave_1`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `category`
    FOREIGN KEY (`category`)
    REFERENCES `yeticave_1`.`categories` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`bets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date` TIMESTAMP NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `user` INT NOT NULL,
  `lot` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `user_idx` (`user` ASC) VISIBLE,
  INDEX `lot_idx` (`lot` ASC) VISIBLE,
  CONSTRAINT `user`
    FOREIGN KEY (`user`)
    REFERENCES `yeticave_1`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `lot`
    FOREIGN KEY (`lot`)
    REFERENCES `yeticave_1`.`lots` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
;

