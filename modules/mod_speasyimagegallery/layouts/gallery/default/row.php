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

use Joomla\CMS\Layout\FileLayout;

extract($displayData);

$col = 'speasyimagegallery-col-md-' . $params->get('album_column', 4);
$col .= ' speasyimagegallery-col-sm-' . $params->get('album_column_sm', 3);
$col .= ' speasyimagegallery-col-xs-' . $params->get('album_column_xs', 2);
$layout = new FileLayout('gallery.default.image', JPATH_ROOT .'/modules/mod_speasyimagegallery/layouts');

echo '<div class="speasyimagegallery-row clearfix">';
foreach ($images as $key => $image) {
  echo '<div class="'. $col .'">';
  echo $layout->render(array('image'=>$image, 'key'=> $key));
  echo '</div>';
}
echo '</div>';
