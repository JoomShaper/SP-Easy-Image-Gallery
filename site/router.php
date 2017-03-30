<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryRouter extends JComponentRouterBase
{
	public function build(&$query)
	{
		$segments = array();

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $this->menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $this->menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}

		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_speasyimagegallery')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
		}

		if (isset($query['view']))
		{
			$view = $query['view'];
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}

		// Are we dealing with an album that is attached to a menu item?
		if (($menuItem instanceof stdClass)
		&& $menuItem->query['view'] == $query['view']
		&& isset($query['id'])
		&& $menuItem->query['id'] == (int) $query['id'])
		{

			unset($query['view']);
			unset($query['id']);

			return $segments;
		}

		if ($view == 'album')
		{
			if (!$menuItemGiven)
			{
				$segments[] = $view;
			}

			unset($query['view']);

			if ($view == 'album')
			{
				if (isset($query['id']))
				{
					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$db = JFactory::getDbo();
						$dbQuery = $db->getQuery(true)
						->select('alias')
						->from('#__speasyimagegallery_albums')
						->where('id=' . (int) $query['id']);
						$db->setQuery($dbQuery);
						$alias = $db->loadResult();
						$query['id'] = $query['id'] . ':' . $alias;
					}
				}
				else
				{
					return $segments;
				}
			}
			else
			{
				return $segments;
			}

			if ($view == 'album')
			{
				list($tmp, $id) = explode(':', $query['id'], 2);
				$segments[] = $id;
			}

			unset($query['id']);
		}

		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;

	}

	public function parse(&$segments)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();
		$count = count($segments);

		$vars = array();
		$vars['view'] = 'album';

		if (!isset($item))
		{
			$alias = $segments[$count - 1];
		} else {
			$alias = $segments[0];
		}

		$db = JFactory::getDbo();
		$dbquery = $db->getQuery(true);
		$dbquery->select($dbquery->qn('id'))
			->from($dbquery->qn('#__speasyimagegallery_albums'))
			->where('alias = ' . $dbquery->q($alias));
		$db->setQuery($dbquery);

		$vars['id'] = $db->loadResult();

		return $vars;
	}
}

function  speasyimagegalleryBuildRoute(&$query)
{
	$router = new SpeasyimagegalleryRouter;

	return $router->build($query);
}

function speasyimagegalleryParseRoute($segments)
{
	$router = new SpeasyimagegalleryRouter;

	return $router->parse($segments);
}
