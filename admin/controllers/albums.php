<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filter.output');

class SpeasyimagegalleryControllerAlbums extends JControllerAdmin
{

	public function getModel($name = 'Album', $prefix = 'SpeasyimagegalleryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	// Upload File
	public function upload_image()
	{
		$model = $this->getModel();
		$user = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$album_id = $input->post->get('album_id', 0, 'INT');
		$file = $input->files->get('image');
		$report = array();
		$params = JComponentHelper::getParams('com_speasyimagegallery');
		$width = $params->get('thumb_width', 400);
		$height = $params->get('thumb_height', 400);

		$authorised = $user->authorise('core.edit', 'com_speasyimagegallery') || $user->authorise('core.edit.own', 'com_speasyimagegallery');

		if ($authorised !== true)
		{
			$report['status'] = false;
			$report['output'] = JText::_('JERROR_ALERTNOAUTHOR');
			echo json_encode($report);
			die();
		}

		if(count($file))
		{
			if ($file['error'] == UPLOAD_ERR_OK)
			{
				$error = false;
				$contentLength = (int) $_SERVER['CONTENT_LENGTH'];
				$mediaHelper = new JHelperMedia;
				$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));
				$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

				// Check for the total size of post back data.
				if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit != -1 && $contentLength > $memoryLimit))
				{
					$report['status'] = false;
					$report['output'] = JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_TOTAL_SIZE_EXCEEDS');
					$error = true;
					echo json_encode($report);
					die;
				}

				$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

				if (($file['error'] == 1) || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
				{
					$report['status'] = false;
					$report['output'] = JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_LARGE');
					$error = true;
				}

				// File formats
				$accepted_formats = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

				// Upload if no error found
				if(!$error)
				{
					$date = JFactory::getDate();

					$file_ext = strtolower(JFile::getExt($file['name']));

					if(in_array($file_ext, $accepted_formats))
					{

						$folder = 'images/speasyimagegallery/albums/' . $album_id . '/images';

						if(!JFolder::exists( JPATH_ROOT . '/' . $folder ))
						{
							JFolder::create(JPATH_ROOT . '/' . $folder, 0755);
						}

						$name = $file['name'];
						$path = $file['tmp_name'];
						// Do no override existing file

						$media_file = preg_replace("/[\s-_]+/", "-", JFile::makeSafe(basename(strtolower($name))));
						$i = 0;
						do {
							$base_name  = JFile::stripExt($media_file) . ($i ? "$i" : "");
							$ext        = JFile::getExt($media_file);
							$media_name = $base_name . '.' . $ext;
							$i++;
							$dest       = JPATH_ROOT . '/' . $folder . '/' . $media_name;
							$src        = $folder . '/'  . $media_name;
						} while(file_exists($dest));
						// End Do not override

						if(JFile::upload($path, $dest, false, true))
						{

							$sources = SpeasyimagegalleryHelper::createThumbs($dest, array(
								'mini'=> array(64, 64),
								'thumb'=> array($width, $height),
								'x_thumb'=> array($width*2, $height*2),
								'y_thumb'=> array($width, $height*1.5)
							), $folder, $base_name, $ext);

							$report['thumb'] = JURI::root(true) . '/' . $sources['thumb'];

							$image = array(
								'title' => $base_name,
								'alt' => $base_name,
								'ext' => $ext,
								'album_id' => $album_id,
								'images' => json_encode($sources)
							);

							$inserted_image = $model->insertMedia($image);
							$report['status'] = true;
							$report['output'] = JLayoutHelper::render('image', array('image'=>$inserted_image));

						}
						else
						{
							$report['status'] = false;
							$report['output'] = JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOAD_FAILED');
						}

					}
					else
					{
						$report['status'] = false;
						$report['output'] = JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_NOT_SUPPORTED');
					}

				}
			}
		}
		else
		{
			$report['status'] = false;
			$report['output'] = JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOAD_FAILED');
		}

		$report['count'] = $model->getCount($input->post->get('album_id', 0, 'INT'));

		echo json_encode($report);
		die();
	}

	// Sort images
	public function sort_images() {
		$input = JFactory::getApplication()->input;
		$orders = $input->get('orders', '', 'STRING');
		$orders = explode(',', $orders);
		$model = $this->getModel();
		$model->save_ajax_orderings($orders);
		die();
	}

	// Change Image state
	public function image_state() {
		$input = JFactory::getApplication()->input;
		$id = $input->get('id', '', 'INT');
		$state = $input->get('state', 'enabled', 'STRING');
		$model = $this->getModel();
		$model->change_image_state($id, $state);
		die();
	}

	// Delete Image
	public function image_delete() {
		$input = JFactory::getApplication()->input;
		$id = $input->get('id', '', 'INT');
		$album_id = $input->get('album_id', '', 'INT');
		$model = $this->getModel();
		$result = $model->image_delete($id, $album_id);
		echo json_encode($result);
		die();
	}

	// Edit Image
	public function edit_image() {
		$input = JFactory::getApplication()->input;
		$id = $input->get('id', '', 'INT');
		$album_id = $input->get('album_id', '', 'INT');
		$model = $this->getModel();
		$image = $model->getImages($album_id, $id);
		echo JLayoutHelper::render('edit', array('image'=>$image));
		die();
	}

	// save image
	public function save_image() {
		$input = JFactory::getApplication()->input;
		$id = $input->get('id', '', 'INT');
		$title = $input->get('title', '', 'STRING');
		$alt = $input->get('alt', '', 'STRING');
		$desc = $input->get('desc', '', 'STRING');

		$attr = array(
			'id'=>$id,
			'title'=>$title,
			'alt'=>$alt,
			'desc'=>$desc
		);

		$model = $this->getModel();
		$model->saveImage($attr);
		die();
	}

}
