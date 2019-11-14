CREATE TABLE `user` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(200) NOT NULL,
	`name` VARCHAR(200) NOT NULL,
	`password` VARCHAR(200) NOT NULL,
	`active` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `email` (`email`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
