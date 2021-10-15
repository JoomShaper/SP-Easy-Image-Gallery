/*Images*/
ALTER TABLE `#__speasyimagegallery_images` MODIFY `created` DATETIME NOT NULL;
ALTER TABLE `#__speasyimagegallery_images` MODIFY `modified` DATETIME NOT NULL;
ALTER TABLE `#__speasyimagegallery_images` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__speasyimagegallery_images` MODIFY `description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__speasyimagegallery_images` MODIFY `images` varchar(5120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_images` MODIFY `language` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*';

/*Albums*/
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `created` DATETIME NOT NULL;
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `modified` DATETIME NOT NULL;
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `description` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `metadata` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `attribs` varchar(5120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__speasyimagegallery_albums` MODIFY `language` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*';