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

$i = 0;
$j = 0;
$last = count($images);

$layout = new FileLayout('gallery.rectangle.image', JPATH_ROOT .'/modules/mod_speasyimagegallery/layouts');

foreach ($images as $key => $image) {
  if($i == 0) {
    echo '<div class="speasyimagegallery-row clearfix">';

    $j++;
    if($j == 3) {
      $j = 0;
    }
  }

  echo $layout->render(array('image'=>$image, 'key'=> $key, 'column'=>$j, 'index'=>$i));

  $i++;
  if(($i == 3) || ($i == $last)) {
    echo '</div>';
    $i = 0;
  }

}
