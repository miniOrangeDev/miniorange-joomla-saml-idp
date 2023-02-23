ALTER TABLE `#__miniorange_saml_config` CHANGE `userslim` `userslim` VARCHAR(255) DEFAULT 'MAo=';
ALTER TABLE `#__miniorange_saml_config` CHANGE `usrlmt` `usrlmt` VARCHAR(255) DEFAULT 'MTAK';
ALTER TABLE `#__miniorange_saml_config` ADD COLUMN `sso_var` VARCHAR(255) DEFAULT 'NjAK';
ALTER TABLE `#__miniorange_saml_config` ADD COLUMN `sso_test` VARCHAR(255) DEFAULT 'MAo=';
UPDATE `#__miniorange_saml_config` SET `usrlmt` = 'MTAK' WHERE `id` = 1;