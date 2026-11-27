USE `bangiay2`;
SET NAMES utf8mb4;

START TRANSACTION;

DELETE pi
FROM `product_images` pi
JOIN `products` p ON p.`id` = pi.`product_id`
WHERE p.`slug` IN (
    'nike-air-force-1-07-triple-white',
    'adidas-ultraboost-light-core-black',
    'nike-air-jordan-1-low-chicago',
    'converse-chuck-70-vintage-canvas',
    'vans-old-skool-classic-black-white',
    'new-balance-550-white-grey',
    'puma-suede-classic-xxi',
    'nike-pegasus-40-road-running'
);

INSERT INTO `product_images` (`product_id`, `image_url`, `alt_text`, `sort_order`, `is_primary`)
SELECT p.`id`, demo_images.`image_url`, demo_images.`alt_text`, demo_images.`sort_order`, demo_images.`is_primary`
FROM `products` p
JOIN (
    SELECT 'nike-air-force-1-07-triple-white' AS `slug`,
           'https://images.pexels.com/photos/12611637/pexels-photo-12611637.jpeg?auto=compress&cs=tinysrgb&w=800' AS `image_url`,
           'Nike Air Force 1 Triple White - góc nghiêng' AS `alt_text`, 1 AS `sort_order`, 1 AS `is_primary`
    UNION ALL SELECT 'nike-air-force-1-07-triple-white',
           'https://images.pexels.com/photos/10924124/pexels-photo-10924124.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Nike Air Force 1 Triple White - góc mang thực tế', 2, 0
    UNION ALL SELECT 'adidas-ultraboost-light-core-black',
           'https://images.pexels.com/photos/5930091/pexels-photo-5930091.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Adidas Ultraboost Core Black - góc ngang', 1, 1
    UNION ALL SELECT 'nike-air-jordan-1-low-chicago',
           'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=800&auto=format&fit=crop&q=80',
           'Air Jordan 1 Low Chicago - góc nghiêng', 1, 1
    UNION ALL SELECT 'nike-air-jordan-1-low-chicago',
           'https://images.pexels.com/photos/12725055/pexels-photo-12725055.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Air Jordan 1 Low Chicago - góc gót và đế', 2, 0
    UNION ALL SELECT 'converse-chuck-70-vintage-canvas',
           'https://images.pexels.com/photos/7745334/pexels-photo-7745334.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Converse Chuck 70 Canvas đen - góc nghiêng', 1, 1
    UNION ALL SELECT 'vans-old-skool-classic-black-white',
           'https://images.pexels.com/photos/8079829/pexels-photo-8079829.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Vans Old Skool đen trắng - góc nghiêng', 1, 1
    UNION ALL SELECT 'vans-old-skool-classic-black-white',
           'https://images.pexels.com/photos/13312678/pexels-photo-13312678.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Vans Old Skool đen trắng - góc nhìn từ trên', 2, 0
    UNION ALL SELECT 'new-balance-550-white-grey',
           'https://images.pexels.com/photos/12284598/pexels-photo-12284598.jpeg?auto=compress&w=800&h=1200&fit=crop',
           'Giày New Balance tông màu trung tính - ảnh tham khảo', 1, 1
    UNION ALL SELECT 'puma-suede-classic-xxi',
           'https://images.pexels.com/photos/9537461/pexels-photo-9537461.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Puma Suede Classic đen - góc mang thực tế', 1, 1
    UNION ALL SELECT 'nike-pegasus-40-road-running',
           'https://images.pexels.com/photos/1456705/pexels-photo-1456705.jpeg?auto=compress&cs=tinysrgb&w=800',
           'Giày Nike chạy bộ màu đen - ảnh tham khảo', 1, 1
) AS demo_images ON demo_images.`slug` = p.`slug`
ORDER BY p.`id`, demo_images.`sort_order`;

COMMIT;
