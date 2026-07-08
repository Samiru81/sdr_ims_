-- SDR IMS feature update patch for existing database
-- Run this only if you do not want to re-import database.sql.

USE sdr_ims;

ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL AFTER last_login;
ALTER TABLE products ADD COLUMN IF NOT EXISTS barcode VARCHAR(100) NULL UNIQUE AFTER sku;
ALTER TABLE sales ADD COLUMN IF NOT EXISTS subtotal DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER selling_price;
ALTER TABLE sales ADD COLUMN IF NOT EXISTS discount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER subtotal;

UPDATE sales SET subtotal = quantity * selling_price WHERE subtotal = 0;
UPDATE products SET barcode = CONCAT('893000', LPAD(id, 6, '0')) WHERE barcode IS NULL OR barcode = '';


ALTER TABLE customers MODIFY phone VARCHAR(10);
ALTER TABLE suppliers MODIFY phone VARCHAR(10);
UPDATE customers SET phone = LEFT(REGEXP_REPLACE(phone, '[^0-9]', ''), 10);
UPDATE suppliers SET phone = LEFT(REGEXP_REPLACE(phone, '[^0-9]', ''), 10);


ALTER TABLE sales ADD COLUMN issued_by VARCHAR(120) NULL AFTER reference_no;
