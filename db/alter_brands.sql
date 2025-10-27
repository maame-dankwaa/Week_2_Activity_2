-- Add user_id and category_id fields to brands table to track brand ownership and category relationship
ALTER TABLE `brands` ADD COLUMN `user_id` int(11) NOT NULL AFTER `brand_name`;
ALTER TABLE `brands` ADD COLUMN `category_id` int(11) NOT NULL AFTER `user_id`;
ALTER TABLE `brands` ADD KEY `user_id` (`user_id`);
ALTER TABLE `brands` ADD KEY `category_id` (`category_id`);
ALTER TABLE `brands` ADD CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `brands` ADD CONSTRAINT `brands_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Add unique constraint for brand_name + category_id combination
ALTER TABLE `brands` ADD UNIQUE KEY `unique_brand_category` (`brand_name`, `category_id`);
