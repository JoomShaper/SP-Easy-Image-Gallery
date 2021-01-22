ALTER TABLE `#__speasyimagegallery_images` CHANGE `created` `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `modified` `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `checked_out_time` `checked_out_time` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `#__speasyimagegallery_albums` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `modified` `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `checked_out_time` `checked_out_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `#__speasyimagegallery_albums` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `metadata` `metadata` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;