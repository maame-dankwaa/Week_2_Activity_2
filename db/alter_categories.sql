-- Add user_id field to categories table to track category ownership
ALTER TABLE `categories` ADD COLUMN `user_id` int(11) NOT NULL AFTER `cat_name`;
ALTER TABLE `categories` ADD KEY `user_id` (`user_id`);
ALTER TABLE `categories` ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
