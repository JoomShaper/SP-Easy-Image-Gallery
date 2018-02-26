<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport( 'joomla.application.component.helper' );

class SpeasyimagegalleryControllerAlbum extends JControllerForm
{

	public function __construct($config = array()) {
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
			return JFactory::getUser()->authorise( "core.edit", "com_speasyimagegallery.album." . $id );
		}
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$params = JComponentHelper::getParams('com_speasyimagegallery');
		$width = $params->get('thumb_width', 400);
		$height = $params->get('thumb_height', 400);
		$item = $model->getItem();
		$id = $item->get('id');
		$image = JPATH_ROOT . '/' . $item->image;

		if(file_exists($image)) {
			$folder = JPATH_ROOT . '/images/speasyimagegallery/albums/' . $id;
			$base_name = JFile::stripExt($image);
			$ext = JFile::getExt($image);

			if(!JFolder::exists($folder))
			{
				JFolder::create($folder, 0755);
			}

			SpeasyimagegalleryHelper::createThumbs($image, array(
				'thumb'=> array($width, $height)
			), $folder, '', $ext);
		}

		return true;
	}
}
