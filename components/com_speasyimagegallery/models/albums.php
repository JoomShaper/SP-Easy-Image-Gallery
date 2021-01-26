<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryModelAlbums extends ListModel
{

	protected function populateState($ordering = null, $direction = null) {
		$app = Factory::getApplication('site');
		$params = $app->getParams();
		$this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
		$limit = $params->get('limit', 20);
		$this->setState('list.limit', $limit);
	}

	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$user = Factory::getUser();
		$catid = $app->input->get('catid', 0, 'INT');

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from($db->quoteName('#__speasyimagegallery_albums', 'a'));

		// Join over the categories.
		$query->select('c.title AS category_title, c.alias AS category_alias')
		->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Images count
		$query->select('CASE WHEN c.count IS NULL THEN 0 ELSE c.count END as count')->join('LEFT', '( SELECT b.album_id, COUNT(b.album_id) as count FROM '. $db->quoteName('#__speasyimagegallery_images', 'b') . ' WHERE b.state = 1 GROUP BY b.album_id ) AS c ON c.album_id = a.id');

		//Authorised
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('a.access IN (' . $groups . ')');

		// Filter category
		if($catid) {
			$descendants = implode(',',$this->getCatChild($catid));
			$query->where('a.catid IN ( ' . $descendants . ')'); // Get in all the descendants

		}

		// Filter by language
		$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');

		return $query;
	}

	// Get category child ids
	public function getCatChild($id)
	{
		$children = [];
		$ids[] = $id;

		while(!empty($ids)) {
			$cid = array_pop($ids);
			$children[] = (string)$cid;
			$categories = $this->getCategories($cid);

			if(!empty($categories)) {
				foreach($categories as $cat) {
					$ids[] = $cat;
				}
			}
		}

		return $children;
	}

	//Get cat ids
	public function getCategories($catid)
	{
		$cats = [];
		$result = array();
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as cid');
		$query->from($db->quoteName('#__categories', 'a'));
		$query->where($db->quoteName('extension') . ' = ' . $db->quote('com_speasyimagegallery'));
		$query->where('a.parent_id = '. $catid);
		$db->setQuery($query);
		$cats = $db->loadObjectList();

		foreach ($cats as $cat) {
			$result[] = $cat->cid;
		}

		return $result;
	}
}