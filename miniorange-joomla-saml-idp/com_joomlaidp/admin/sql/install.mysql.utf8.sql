 
CREATE TABLE IF NOT EXISTS `#__miniorangesamlidp` (
	`id` INT(11) NOT NULL ,
	`sp_name` VARCHAR(50) NOT NULL,
	`sp_entityid` VARCHAR(200) NOT NULL,
	`acs_url` VARCHAR(200) NOT NULL,
	`default_relay_state` VARCHAR(200) NOT NULL,
	`nameid_format` VARCHAR(100) NOT NULL,
	`nameid_attribute` VARCHAR(100) NOT NULL,
	`enabled` tinyint(1) DEFAULT 0,
	`assertion_signed` tinyint(1) DEFAULT TRUE,
	PRIMARY KEY (`id`)
)
ENGINE =MyISAM
DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miniorange_saml_idp_customer` (
`id` int(11) UNSIGNED NOT NULL ,
`idp_entity_id` VARCHAR(200) NOT NULL,
`email` VARCHAR(255)  NOT NULL ,
`password` VARCHAR(255)  NOT NULL ,
`admin_phone` VARCHAR(255)  NOT NULL ,
`customer_key` VARCHAR(255)  NOT NULL ,
`customer_token` VARCHAR(255) NOT NULL,
`api_key` VARCHAR(255)  NOT NULL,
`login_status` tinyint(1) DEFAULT 0,
`registration_status` VARCHAR(255) NOT NULL,
`transaction_id` VARCHAR(255) NOT NULL,
`email_count` int(11),
`sms_count` int(11),
`uninstall_feedback` int(2) NOT NULL,
`initialise_visual_tour` tinyint(1) DEFAULT 1,
`show_tc_popup` tinyint(1) DEFAULT 1,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

INSERT IGNORE INTO `#__miniorange_saml_idp_customer`(`id`,`login_status`) values (1,0) ;
INSERT IGNORE INTO `#__miniorangesamlidp`(`id`,`enabled`) values (1,0);
ALTER TABLE `#__users` ADD COLUMN `userIn` int(11) DEFAULT 0;
