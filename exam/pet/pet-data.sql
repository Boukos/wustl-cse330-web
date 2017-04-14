CREATE DATABASE petdb;
CREATE TABLE `petdb`.`pet` ( `username` VARCHAR(50) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`username`)) ENGINE = InnoDB;
CREATE TABLE `petdb`.`pets` ( `id` MEDIUMINT NOT NULL AUTO_INCREMENT , `username` VARCHAR(50) NOT NULL , `species` ENUM('cat','dog','chinchilla','snake','rabbit') NOT NULL , `name` VARCHAR(50) NOT NULL , `filename` VARCHAR(150) NOT NULL , `weight` DECIMAL(4,2) NOT NULL , `description` TINYTEXT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `pets` ADD INDEX(`username`);
ALTER TABLE `pets` ADD CONSTRAINT `petuser` FOREIGN KEY (`username`) REFERENCES `petdb`.`users`(`username`) ON DELETE RESTRICT ON UPDATE RESTRICT;
