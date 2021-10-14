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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\FormController;


class SpeasyimagegalleryControllerAlbum extends FormController
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	protected function allowAdd($data = array())
	{
		return parent::allowAdd($data);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		$id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
		if( !empty( $id ) )
		{
			return Factory::getUser()->authorise( "core.edit", "com_speasyimagegallery.album." . $id );
		}
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$params = ComponentHelper::getParams('com_speasyimagegallery');
		$width = $params->get('thumb_width', 400);
		$height = $params->get('thumb_height', 400);
		$item = $model->getItem();
		$id = $item->get('id');
		$folder = JPATH_ROOT . '/images/speasyimagegallery/albums/' . $id;
		$image = JPATH_ROOT . '/' . $item->image;

		$filteredImage = explode('#', $image);
		$image = str_replace('%20', ' ', $filteredImage[0]);

		$ext = File::getExt($image);

		if (File::exists($image))
		{
			if (!Folder::exists($folder))
			{
				Folder::create($folder, 0755);
			}

			SpeasyimagegalleryHelper::createThumbs($image, array('thumb' => array($width, $height)), $folder, '', $ext);
		}

		return true;
	}
	/**
	 * Delete selected image from list
	 *
	 * @return void
	 */
	public function deleteSelectedList()
	{
		$input = Factory::getApplication()->input;
		$selected_id = $input->get('boxchecked', '', 'STRING');
		$album_id = $input->get('album_id', 0, 'INT');
		$image_count = 0;

		$app = Factory::getApplication();

		$output = array();

		if (empty($album_id))
		{
			$url = Route::_('index.php?option=com_speasyimagegallery&view=album&layout=edit&id=' . $album_id, false);

			$app->enqueueMessage(Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_NO', MSG_EOR));
			$app->redirect($url);
		}

		$image_items = array();

		if (!empty($selected_id))
		{
			$image_items = explode(',', $selected_id);
		}

		if (!empty($image_items))
		{
			$image_count = count($image_items);

			foreach ($image_items as $ii)
			{
				$this->image_delete((int)$ii, $album_id);
			}
		}

		$url = Route::_('index.php?option=com_speasyimagegallery&view=album&layout=edit&id=' . $album_id, false);

		$app->enqueueMessage(Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_DELETE_1'));
		$app->redirect($url);

	}

	// Delete Image
	public function image_delete($image_id, $album_id)
	{
		$model = $this->getModel();
		$result = $model->image_delete($image_id, $album_id);

		return $result;
	}
}
