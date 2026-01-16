-- Increase setting_value size to accommodate longer text like addresses
ALTER TABLE `settings` MODIFY `setting_value` TEXT;

-- Insert Company Name
INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`)
SELECT 'company_name', 'Company Name displayed on invoices and header', 'Srijaya Print House'
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `setting_name` = 'company_name');

-- Insert Company Address
INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`)
SELECT 'company_address', 'Company Address displayed on invoices', 'FF26, Megacity, Athurugiriya.'
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `setting_name` = 'company_address');

-- Insert Company Phone
INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`)
SELECT 'company_phone', 'Company Phone Number', '0714730996'
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `setting_name` = 'company_phone');

-- Insert Company Logo URL (Default placeholder, can be updated in Settings)
INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`)
SELECT 'company_logo', 'URL/Path to Company Logo', 'logo.png'
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `setting_name` = 'company_logo');

-- Insert Base URL for Mobile App QR Code
INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`)
SELECT 'company_base_url', 'Base URL for API calls', 'https://pos.srijaya.lk'
WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `setting_name` = 'company_base_url');
