-- Run this SQL to add new columns for discount form (Counselled By Include/Not Include and Attachments).
-- Execute in MySQL / phpMyAdmin. Skip any line if that column already exists.

-- Counselled By Include/Not Include fields (each row: checkbox + input value stored as JSON)
ALTER TABLE hms_discount_form ADD COLUMN dis_counselled_by_include TEXT NULL COMMENT 'JSON array of include items' AFTER dis_counselled_by;
ALTER TABLE hms_discount_form ADD COLUMN dis_counselled_by_not_include TEXT NULL COMMENT 'JSON array of not include items' AFTER dis_counselled_by_include;

-- Multi-file attachments (JSON array of file paths)
ALTER TABLE hms_discount_form ADD COLUMN dis_attachments TEXT NULL COMMENT 'JSON array of file paths' AFTER dis_admin_sign;
