-- Clean up guest billing data
-- This will remove entries that match the pattern of test/mock data

-- First, create a backup of the current data
CREATE TABLE IF NOT EXISTS guest_billing_backup AS SELECT * FROM guest_billing;

-- Remove suspicious entries that match the hardcoded pattern
DELETE FROM guest_billing 
WHERE order_id IN ('5436', '1361', '8394', '3328')
   OR (payment_option = 'To be billed' 
       AND payment_method = '-' 
       AND partial_payment = 0 
       AND item_name = 'No Items'
       AND total_amount = 0);

-- Update any remaining entries with null values to have proper defaults
UPDATE guest_billing 
SET payment_method = '-' WHERE payment_method IS NULL;

UPDATE guest_billing 
SET partial_payment = 0 WHERE partial_payment IS NULL;

UPDATE guest_billing 
SET remaining_amount = total_amount - partial_payment 
WHERE remaining_amount IS NULL;