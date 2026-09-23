<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Modern Router for com_speasyimagegallery.
 */
class Router extends RouterView
{
    /**
     * Database connector.
     *
     * @var DatabaseInterface
     */
    private DatabaseInterface $db;

    /**
     * Category factory.
     *
     * @var CategoryFactoryInterface
     */
    private CategoryFactoryInterface $categoryFactory;

    /**
     * Constructor.
     *
     * @param   SiteApplication           $app              Site application.
     * @param   AbstractMenu              $menu             Menu object.
     * @param   CategoryFactoryInterface  $categoryFactory  Category factory.
     * @param   DatabaseInterface         $db               Database interface.
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        $this->categoryFactory = $categoryFactory;
        $this->db              = $db;

        $albums = new RouterViewConfiguration('albums');
        $this->registerView($albums);

        $album = new RouterViewConfiguration('album');
        $album->setKey('id')->setParent($albums, 'catid');
        $this->registerView($album);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Method to get segment for an album
     *
     * @param   string  $id     Album ID or ID:alias
     * @param   array   $query  The query parameters
     * @return  array
     */
    public function getAlbumSegment(string $id, array $query): array
    {
        if (strpos($id, ':') !== false) {
            [$albumId, $alias] = explode(':', $id, 2);
        } else {
            $albumId = (int) $id;
            $dbQuery = $this->db->getQuery(true)
                ->select($this->db->quoteName('alias'))
                ->from($this->db->quoteName('#__speasyimagegallery_albums'))
                ->where($this->db->quoteName('id') . ' = :id')
                ->bind(':id', $albumId, ParameterType::INTEGER);
            $this->db->setQuery($dbQuery);
            $alias = $this->db->loadResult() ?: (string) $albumId;
        }

        return [(int) $albumId => $alias];
    }

    /**
     * Method to get album ID from URL segment
     *
     * @param   string  $segment  The URL segment
     * @param   array   $query    The query array
     * @return  int
     */
    public function getAlbumId(string $segment, array $query): int
    {
        $alias = str_replace(':', '-', $segment);

        // Check if segment is formatted as {id}-{alias}
        if (preg_match('/^([0-9]+)-(.*)$/', $alias, $matches)) {
            return (int) $matches[1];
        }

        // Check if numeric
        if (is_numeric($alias)) {
            return (int) $alias;
        }

        // Query by alias
        $dbQuery = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__speasyimagegallery_albums'))
            ->where($this->db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias);

        $this->db->setQuery($dbQuery);
        $result = $this->db->loadResult();

        return (int) ($result ?: 0);
    }
}
