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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check: is this user allowed to access the backend of this component?
if (!Factory::getUser()->authorise('core.manage', 'com_speasyimagegallery'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file
JLoader::register('SpeasyimagegalleryHelper', JPATH_COMPONENT . '/helpers/speasyimagegallery.php');
$controller = BaseController::getInstance('Speasyimagegallery');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
