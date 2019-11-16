CREATE TABLE `register_application` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`token` CHAR(100) NOT NULL,
	`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`email` VARCHAR(200) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `token` (`token`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
