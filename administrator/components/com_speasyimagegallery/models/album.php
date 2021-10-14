<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\MVC\Model\AdminModel;

class SpeasyimagegalleryModelAlbum extends AdminModel
{
	/**
	* Method to get a table object, load it if necessary.
	*
	* @param   string  $type    The table name. Optional.
	* @param   string  $prefix  The class prefix. Optional.
	* @param   array   $config  Configuration array for model. Optional.
	*
	* @return  JTable  A JTable object
	*
	* @since   1.6
	*/

	public function getTable($type = 'Album', $prefix = 'SpeasyimagegalleryTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	* Method to get the record form.
	*
	* @param   array    $data      Data for the form.
	* @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	*
	* @return  mixed    A JForm object on success, false on failure
	*
	* @since   1.6
	*/
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_speasyimagegallery.album', 'album', array( 'control' => 'jform', 'load_data' => $loadData ) );

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	* Method to get the data that should be injected in the form.
	*
	* @return  mixed  The data for the form.
	*
	* @since   1.6
	*/
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState( 'com_speasyimagegallery.edit.album.data', array() );

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data) {
		$input  = Factory::getApplication()->input;
		$filter = InputFilter::getInstance();

		$filteredImage = explode('#', $data['image']);
		$data['image'] = str_replace('%20', ' ', $filteredImage[0]);

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save')) && (!isset($data['id']) || (int) $data['id'] == 0))
		{
			if ($data['alias'] == null)
			{
				if (Factory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = OutputFilter::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = OutputFilter::stringURLSafe($data['title']);
				}

				$table = Table::getInstance('Album', 'SpeasyimagegalleryTable');

				while ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])))
				{
					$data['alias'] = StringHelper::increment($data['alias'], 'dash');
				}
			}
		}

		if (parent::save($data))
		{
			return true;
		}

		return false;
	}

	/**
	* Method to check if it's OK to delete a message. Overwrites JModelAdmin::canDelete
	*/
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return false;
			}

			return Factory::getUser()->authorise('core.delete', 'com_speasyimagegallery.album.' . (int) $record->id);
		}

		return false;
	}

	// Delete images
	public function delete(&$pks)
	{
		$return = parent::delete($pks);

		if ($return)
		{
			foreach ($pks as $pk) {
				// delete images
				$cover = JPATH_ROOT . "/images/speasyimagegallery/albums/" . $pk;
				if(Folder::exists($cover)) {
					Folder::delete($cover);
				}

				// Get all images
				$images = $this->getImages($pk);
				if(count($images)) {
					foreach ($images as $key => $image) {
						$this->image_delete($image->id, $pk);
					}
				}

			}
		}

		return $return;
	}

	// Get images
	public function getImages($album_id = 0, $id = 0) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.*'));
		$query->from($db->quoteName('#__speasyimagegallery_images', 'a'));

		if($album_id) {
			$query->where($db->quoteName('album_id') . ' = '. $db->quote($album_id));
		}

		if($id) {
			$query->where($db->quoteName('id') . ' = '. $db->quote($id));
			$db->setQuery($query);
			return $db->loadObject();
		} else {
			$query->order('a.ordering DESC');
			$db->setQuery($query);
			return $db->loadObjectList();
		}
	}

	public function getCount($album_id = 0) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(a.id)');
		$query->from($db->quoteName('#__speasyimagegallery_images', 'a'));
		if($album_id) {
			$query->where($db->quoteName('album_id') . ' = '. $db->quote($album_id));
		}
		$db->setQuery($query);
		$total = $db->loadResult();

		if($total) {
			return $total;
		}

		return 0;
	}

	// upload
	public function insertMedia($attribs = array())
	{
		$total = $this->getCount($attribs['album_id']);
		$db = Factory::getDbo();
		$image = new stdClass();
		$title = ucwords(preg_replace("/[\s\-_]+/", " ", $attribs['title']));
		$image->filename = $attribs['title'] . '.' . $attribs['ext'];
		$image->title = $title;
		$image->alt = $title;
		$image->album_id = $attribs['album_id'];
		$image->state = 1;
		$image->ordering = ($total + 1);
		$image->created = HTMLHelper::_('date', strtotime(Factory::getDate('now')), 'Y-m-d h:m:s');
		$image->created_by = Factory::getUser()->id;
		$image->modified = HTMLHelper::_('date', strtotime(Factory::getDate('now')), 'Y-m-d h:m:s');
		$image->modified_by = Factory::getUser()->id;
		$image->images = $attribs['images'];

		try
		{
			$db->insertObject('#__speasyimagegallery_images', $image, 'id');
		}
		catch (\Exception $e)
		{
			echo $e->getMessage();
			die;
		}

		// Retrive Image
		$insertid = $db->insertid();
		$insertImage = $this->getImages($attribs['album_id'], $insertid);

		return $insertImage;
	}

	// Update orderings
	public function save_ajax_orderings($orderings = array()) {
		if(count($orderings)) {
			$count = count($orderings);
			foreach ($orderings as $key => $id) {
				$image = new stdClass();
				$image->id = $id;
				$image->ordering = ($count - $key) + 1;
				$result = Factory::getDbo()->updateObject('#__speasyimagegallery_images', $image, 'id');
			}
		}
	}

	// Update state
	public function change_image_state($id, $state) {
		$image = new stdClass();
		$image->id = $id;
		if($state == 'enabled') {
			$image->state = 0;
		} else {
			$image->state = 1;
		}
		$result = Factory::getDbo()->updateObject('#__speasyimagegallery_images', $image, 'id');
	}

	public function saveImage($attr)
	{
		$image = new stdClass();
		$image->id = $attr['id'];
		$image->title = $attr['title'];
		$image->alt = $attr['alt'];
		$image->description = $attr['desc'];
		$result = Factory::getDbo()->updateObject('#__speasyimagegallery_images', $image, 'id');
	}

	public function image_delete($id, $album_id)
	{
		$image = $this->getImages($album_id, $id);
		$sources = json_decode($image->images);

		foreach ($sources as $key => $source)
		{
			$path = JPATH_ROOT . '/' . $source;

			if (file_exists($path))
			{
				File::delete($path);
			}
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$conditions = array($db->quoteName('id') . ' = ' . $id);
		$query->delete($db->quoteName('#__speasyimagegallery_images'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();

		$output = array();
		$output['status'] = true;
		$output['count'] = $this->getCount($album_id);

		return $output;

	}

}
