-- Run this once in phpMyAdmin / MySQL CLI to add the username column to the live table
ALTER TABLE `user_activity_logs`
    ADD COLUMN `username` VARCHAR(100) NULL COMMENT 'Employee number / username from tbl_ticket_users'
    AFTER `user_id`;
