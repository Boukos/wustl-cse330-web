CREATE DATABASE matchmaker;

CREATE TABLE TABLENAME (
	id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	username VARCHAR(50),
	email VARCHAR(50),
	pictureUrl varchar(100)
	description tinytext 
	age mediumint
	posted TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
) 
