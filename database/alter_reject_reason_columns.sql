-- Add reject_reason column to discount, cancel and refund form tables.
-- Run this once on your database (e.g. in phpMyAdmin or mysql client).
-- If column already exists, skip that statement or ignore the error.

-- Discount form
ALTER TABLE hms_discount_form
ADD COLUMN reject_reason TEXT NULL COMMENT 'Reason when rejected by approver';

-- Cancel bill form
ALTER TABLE hms_cancelbill_form
ADD COLUMN reject_reason TEXT NULL COMMENT 'Reason when rejected by approver';

-- Refund form
ALTER TABLE hms_refund_form
ADD COLUMN reject_reason TEXT NULL COMMENT 'Reason when rejected by approver';
