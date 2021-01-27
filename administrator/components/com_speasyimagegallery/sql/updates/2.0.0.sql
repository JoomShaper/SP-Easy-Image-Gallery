/*Images*/
ALTER TABLE `#__speasyimagegallery_images` CHANGE `created` `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `modified` `modified` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `checked_out_time` `checked_out_time` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `image` `image` varchar(5120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_images` CHANGE `filename` `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_images` CHANGE `language` `language` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*';

/*Albums*/
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `modified` `modified` DATETIME NOT NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `checked_out_time` `checked_out_time` DATETIME NOT NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `metadata` `metadata` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `metakey` `metakey` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `metadesc` `metadesc` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `attribs` `attribs` varchar(5120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `image` `image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `language` `language` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*';