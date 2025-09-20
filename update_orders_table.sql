ALTER TABLE orders 
ADD COLUMN payment_status VARCHAR(20) DEFAULT 'pending',
ADD COLUMN payment_date TIMESTAMP NULL;