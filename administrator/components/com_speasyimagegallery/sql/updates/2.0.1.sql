ALTER TABLE `#__speasyimagegallery_albums` CHANGE `created` `created` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `modified` `modified` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_albums` CHANGE `checked_out_time` `checked_out_time` DATETIME NULL DEFAULT NULL;

ALTER TABLE `#__speasyimagegallery_images` CHANGE `created` `created` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `modified` `modified` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__speasyimagegallery_images` CHANGE `checked_out_time` `checked_out_time` DATETIME NULL DEFAULT NULL;

UPDATE `#__speasyimagegallery_images` SET `created` = NULL WHERE `created` = '0000-00-00 00:00:00';
UPDATE `#__speasyimagegallery_images` SET `modified` = NULL WHERE `modified` = '0000-00-00 00:00:00';
UPDATE `#__speasyimagegallery_images` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';

UPDATE `#__speasyimagegallery_albums` SET `created` = NULL WHERE `created` = '0000-00-00 00:00:00';
UPDATE `#__speasyimagegallery_albums` SET `modified` = NULL WHERE `modified` = '0000-00-00 00:00:00';
UPDATE `#__speasyimagegallery_albums` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
