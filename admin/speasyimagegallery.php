<?php
/**
 * @package com_speasyimagegallery
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2017 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_speasyimagegallery'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file
JLoader::register('SpeasyimagegalleryHelper', JPATH_COMPONENT . '/helpers/speasyimagegallery.php');
$controller = JControllerLegacy::getInstance('Speasyimagegallery');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
