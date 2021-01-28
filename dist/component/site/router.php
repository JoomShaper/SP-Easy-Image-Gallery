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
class SpeasyimagegalleryRouterBase
{
	public static function buildRoute(&$query)
	{
		$app = Factory::getApplication();
		$menu = $app->getMenu();

		$segments = array();

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}

		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_speasyimagegallery')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
			unset($query['view']);
		}

		if (isset($query['view']))
		{
			$view = $query['view'];
		} else {
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

		//Replace with menu
		$mview = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];

		//List view
		if ( $view == 'albums' ) {
			if($mview != $view) {
				$segments[] = $view;
			}
			unset($query['view']);
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
						$db = Factory::getDbo();
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

	public static function parseRoute(&$segments)
	{
		$app = Factory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();
		$count = count($segments);

		$vars = array();
		$vars['view'] = 'album';

		for ($i = 0; $i < $count; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		if (!isset($item))
		{
			$alias = $segments[$count - 1];
		} else {
			$alias = $segments[0];
		}

		$alias = preg_replace('/:/', '-', $alias);

		$db = Factory::getDbo();
		$dbquery = $db->getQuery(true);
		$dbquery->select($dbquery->qn('id'))
			->from($dbquery->qn('#__speasyimagegallery_albums'))
			->where('alias = ' . $dbquery->q($alias));
		$db->setQuery($dbquery);

		$vars['id'] = $db->loadResult();

		return $vars;
	}
}

if(JVERSION >= 4 ) {
	/**
	 * Routing class to support Joomla 4.0
	 *
	 */
	class SpeasyimagegalleryRouter extends Joomla\CMS\Component\Router\RouterBase
	{
		public function build(&$query)
		{
			$segments = SpeasyimagegalleryRouterBase::buildRoute($query);
			return $segments;
		}

		public function parse(&$segments)
		{
			$vars = SpeasyimagegalleryRouterBase::parseRoute($segments);

			$segments = array();

			return $vars;
		}
	}
}

function speasyimagegalleryBuildRoute(&$query)
{
	$segments = SpeasyimagegalleryRouterBase::buildRoute($query);
	return $segments;
}

function speasyimagegalleryParseRoute(&$segments)
{
	$vars = SpeasyimagegalleryRouterBase::parseRoute($segments);
	return $vars;
}
