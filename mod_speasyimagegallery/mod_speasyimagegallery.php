<?php
/**
* @package com_speasyimagegallery
* @subpackage mod_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include the search functions only once
JLoader::register('ModSpeasyimagegalleryHelper', __DIR__ . '/helper.php');

//$params
$lang = JFactory::getLanguage();
$app  = JFactory::getApplication();

$layout = $params->get('layout', 'album');

if($layout == 'albums') {
  $albums = ModSpeasyimagegalleryHelper::getAlbumList($params);
} else {
  $images = ModSpeasyimagegalleryHelper::getImages($params);
}

require JModuleHelper::getLayoutPath('mod_speasyimagegallery', $layout);
