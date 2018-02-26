<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

extract($displayData);

$col = 'speasyimagegallery-col-md-' . $params->get('column', 4);
$col .= ' speasyimagegallery-col-sm-' . $params->get('column_sm', 3);
$col .= ' speasyimagegallery-col-xs-' . $params->get('column_xs', 2);

echo '<div class="speasyimagegallery-row clearfix">';
foreach ($images as $key => $image) {
  echo '<div class="'. $col .'">';
  echo JLayoutHelper::render('gallery.default.image', array('image'=>$image, 'key'=> $key));
  echo '</div>';
}
echo '</div>';
