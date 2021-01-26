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
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldAlbums extends JFormFieldList
{

	protected $type = 'Albums';

	public function getOptions()
	{

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'title')));
		$query->from($db->quoteName('#__speasyimagegallery_albums'));
		$query->where($db->quoteName('published') . ' = '. $db->quote(1));
		$query->order('ordering DESC');
		$db->setQuery($query);

		return $db->loadAssocList('id', 'title');

	}
}
