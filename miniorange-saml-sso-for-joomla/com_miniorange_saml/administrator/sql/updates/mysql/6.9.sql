ALTER TABLE `#__miniorange_saml_config` ADD COLUMN `test_configuration` boolean DEFAULT false;
ALTER TABLE `#__miniorange_saml_customer_details` ADD COLUMN `admin_email`  VARCHAR(255)  NOT NULL;
ALTER TABLE `#__miniorange_saml_config` ADD COLUMN `sso_status` boolean DEFAULT false;