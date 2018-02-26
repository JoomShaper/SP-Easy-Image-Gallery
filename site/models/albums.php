<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryModelAlbums extends JModelList
{

	protected function getListQuery()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
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
			$query->where('a.catid = ' . $catid);
		}

		// Filter by language
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');

		return $query;
	}

}
