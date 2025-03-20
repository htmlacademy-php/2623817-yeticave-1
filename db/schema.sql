CREATE SCHEMA IF NOT EXISTS `yeticave_1` DEFAULT CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `contact_info` VARCHAR(120) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`categories` (
  `id` VARCHAR(45) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`lots` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` VARCHAR(250) NOT NULL,
  `description` TEXT NULL,
  `image_path` VARCHAR(260) NULL,
  `start_price` INT UNSIGNED NOT NULL,
  `expiration_date` TIMESTAMP NOT NULL,
  `price_step` INT UNSIGNED NOT NULL,
  `author_id` INT NULL,
  `winner_id` INT NULL,
  `category_id` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `author_idx` (`author_id` ASC) VISIBLE,
  INDEX `winner_idx` (`winner_id` ASC) VISIBLE,
  INDEX `category_idx` (`category_id` ASC) VISIBLE,
  CONSTRAINT `author`
    FOREIGN KEY (`author_id`)
    REFERENCES `yeticave_1`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `winner`
    FOREIGN KEY (`winner_id`)
    REFERENCES `yeticave_1`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `category`
    FOREIGN KEY (`category_id`)
    REFERENCES `yeticave_1`.`categories` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
;

CREATE TABLE IF NOT EXISTS `yeticave_1`.`bets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price` INT UNSIGNED NOT NULL,
  `user_id` INT NOT NULL,
  `lot_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `user_idx` (`user_id` ASC) VISIBLE,
  INDEX `lot_idx` (`lot_id` ASC) VISIBLE,
  CONSTRAINT `user`
    FOREIGN KEY (`user_id`)
    REFERENCES `yeticave_1`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `lot`
    FOREIGN KEY (`lot_id`)
    REFERENCES `yeticave_1`.`lots` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
    ;