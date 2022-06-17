<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;

/**
 * SP easy image gallery helper class.
 * @since 1.0.0
 */
class SpeasyimagegalleryHelper
{
	/**
	 * component name
	 *
	 * @var string
	 */
	public static $extension = 'com_speasyimagegallery';

	/**
	 * Undocumented function
	 *
	 * @param	string $submenu	submenu name
	 * @return void
	 */
	public static function addSubmenu($submenu)
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_SPEASYIMAGEGALLERY_SUBMENU_ALBUMS'),
			'index.php?option=com_speasyimagegallery',
			$submenu == 'albums'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_SPEASYIMAGEGALLERY_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_speasyimagegallery',
			$submenu == 'categories'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_SPEASYIMAGEGALLERY_SUBMENU_OPTIONS'),
			'index.php?option=com_config&view=component&component=com_speasyimagegallery',
			$submenu == 'options'
		);
	}

	/**
	 * Actions
	 *
	 * @param	integer	$messageId	action id
	 * @return	JObject
	 */
	public static function getActions($messageId = 0)
	{
		$result	= new JObject;

		if (empty($messageId))
		{
			$assetName = 'com_speasyimagegallery';
		}
		else
		{
			$assetName = 'com_speasyimagegallery.album.' . (int) $messageId;
		}

		$actions = Access::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/access.xml', '/access/section[@name="component"]/'
		);

		foreach ($actions as $action)
		{
			$result->set($action->name, Factory::getUser()->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function getVersion($type = 'major')
	{
		$version = JVERSION;
		list ($major, $minor, $patch) = explode('.', $version);

		if (strpos($patch, '-') !== false)
		{
			$patch = explode('-', $patch)[0];
		}

		switch ($type)
		{
			case 'minor':
				return (int) $minor;
			case 'patch':
				return (int) $patch;
			case 'major':
			default:
				return (int) $major;
		}
	}

	/**
	 * Create thumbs
	 *
	 * @param	string	$src		file path
	 * @param	array	$sizes		file size
	 * @param	string	$folder		folder name
	 * @param	string	$base_name	file base name
	 * @param	string	$ext		file extention
	 * @return	boolean/string
	 */
	public static function createThumbs($src, $sizes , $folder, $base_name, $ext)
	{

		list($originalWidth, $originalHeight) = getimagesize($src);

		$img = "";

		switch ($ext)
		{
			case 'bmp': $img = imagecreatefromwbmp($src); break;
			case 'gif': $img = imagecreatefromgif($src); break;
			case 'jpg': $img = imagecreatefromjpeg($src); break;
			case 'jpeg': $img = imagecreatefromjpeg($src); break;
			case 'png': $img = imagecreatefrompng($src); break;
		}

		if (count($sizes))
		{
			$output = array();

			if ($base_name)
			{
				$output['original'] = $folder . '/' . $base_name . '.' . $ext;
			}

			foreach ($sizes as $key => $size)
			{
				$targetWidth = $size[0];
				$targetHeight = $size[1];
				$ratio_thumb = $targetWidth / $targetHeight;
				$ratio_original = $originalWidth / $originalHeight;

				if ($ratio_original >= $ratio_thumb) 
				{
					$height = $originalHeight;
					$width = ceil(($height * $targetWidth) / $targetHeight);
					$x = ceil(($originalWidth - $width) / 2);
					$y = 0;
				}
				else
				{
					$width = $originalWidth;
					$height = ceil(($width * $targetHeight) / $targetWidth);
					$y = ceil(($originalHeight - $height) / 2);
					$x = 0;
				}

				$new = imagecreatetruecolor($targetWidth, $targetHeight);

				if($ext == "gif" or $ext == "png")
				{
					imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
					imagealphablending($new, false);
					imagesavealpha($new, true);
				}

				imagecopyresampled($new, $img, 0, 0, $x, $y, $targetWidth, $targetHeight, $width, $height);

				if ($base_name)
				{
					$dest = dirname($src) . '/' . $base_name . '_' . $key . '.' . $ext;
					$output[$key] = $folder . '/' . $base_name . '_' . $key . '.' . $ext;
				}
				else
				{
					$dest = $folder . '/' . $key . '.' . $ext;
				}

				switch ($ext)
				{
					case 'bmp': imagewbmp($new, $dest); break;
					case 'gif': imagegif($new, $dest); break;
					case 'jpg': imagejpeg($new, $dest); break;
					case 'jpeg': imagejpeg($new, $dest); break;
					case 'png': imagepng($new, $dest); break;
				}
			}

			return $output;
		}

		return false;
	}

}
