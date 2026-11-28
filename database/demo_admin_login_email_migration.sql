USE `bangiay2`;

UPDATE `users` AS legacy_admin
JOIN `user_roles` AS user_role ON user_role.`user_id` = legacy_admin.`id`
JOIN `roles` AS role ON role.`id` = user_role.`role_id`
LEFT JOIN `users` AS current_admin
    ON current_admin.`email` = 'admin@applemeomeo.vn'
    AND current_admin.`id` <> legacy_admin.`id`
SET legacy_admin.`email` = 'admin@applemeomeo.vn'
WHERE legacy_admin.`email` = 'admin@solemate.vn'
  AND LOWER(role.`name`) IN ('admin', 'super admin')
  AND current_admin.`id` IS NULL;
