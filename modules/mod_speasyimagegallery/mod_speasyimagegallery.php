<?php

/**
 * @package com_speasyimagegallery
 * @subpackage mod_speasyimagegallery
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

// Include the search functions only once
JLoader::register('ModSpeasyimagegalleryHelper', __DIR__ . '/helper.php');

//$params
$lang = Factory::getLanguage();
$app  = Factory::getApplication();

$layout = $params->get('layout', 'album');

if($layout == 'albums') {
  $albums = ModSpeasyimagegalleryHelper::getAlbumList($params);
} else {
  $images = ModSpeasyimagegalleryHelper::getImages($params);
}

require ModuleHelper::getLayoutPath('mod_speasyimagegallery', $layout);
