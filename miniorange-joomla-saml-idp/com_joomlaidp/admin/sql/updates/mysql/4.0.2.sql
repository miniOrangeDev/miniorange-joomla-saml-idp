ALTER TABLE `#__miniorangesamlidp` ADD COLUMN assertion_signed tinyint(1) DEFAULT FALSE;
ALTER TABLE `#__miniorange_saml_idp_customer` ADD COLUMN `idp_entity_id` VARCHAR(200) NOT NULL