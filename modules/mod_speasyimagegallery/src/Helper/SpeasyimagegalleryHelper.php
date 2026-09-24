<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  mod_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Module\Speasyimagegallery\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Helper class for mod_speasyimagegallery.
 */
class SpeasyimagegalleryHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Constructor.
     *
     * @param   array|DatabaseInterface|null  $config  Configuration array or Database connector.
     */
    public function __construct($config = [])
    {
        if ($config instanceof DatabaseInterface) {
            $this->setDatabase($config);
        }
    }

    /**
     * Get the database connector.
     *
     * @return  DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface
    {
        if (!$this->databaseAwareTraitDatabase) {
            $this->databaseAwareTraitDatabase = Factory::getContainer()->get(DatabaseInterface::class);
        }

        return $this->databaseAwareTraitDatabase;
    }

    /**
     * Get albums list based on parameters.
     *
     * @param   object  $params  Module parameters
     * @return  array
     */
    public function getAlbumList($params): array
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        $catid = (int) $params->get('catid', 0);
        $layout = (string) $params->get('layout', '');
        $featuredOnly = (int) $params->get('show_featured_only', 0);
        $limit = (int) $params->get('albums_limit', 0);

        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__speasyimagegallery_albums', 'a'));

        // Join over categories
        $query->select([
            $db->quoteName('c.title', 'category_title'),
            $db->quoteName('c.alias', 'category_alias'),
            $db->quoteName('c.description', 'category_description')
        ])->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

        // Images count
        $imgSub = '(SELECT b.album_id, COUNT(b.album_id) as count FROM ' . $db->quoteName('#__speasyimagegallery_images', 'b') . ' WHERE b.state = 1 GROUP BY b.album_id)';
        $query->select('CASE WHEN img.count IS NULL THEN 0 ELSE img.count END AS count')
            ->join('LEFT', $imgSub . ' AS img ON img.album_id = a.id');

        // Authorised access levels
        $groups = $user->getAuthorisedViewLevels();
        if (!empty($groups)) {
            $query->whereIn($db->quoteName('a.access'), $groups);
        }

        // Filter category
        if ($catid > 0 && $layout === 'albums') {
            $descendants = $this->getCatChild($catid);
            if (!empty($descendants)) {
                $query->whereIn($db->quoteName('a.catid'), $descendants);
            }
        }

        // Filter by featured
        if ($featuredOnly === 1) {
            $query->where($db->quoteName('a.featured') . ' = 1');
        }

        // Filter by language
        $langTag = $app->getLanguage()->getTag();
        $allLanguages = '*';
        $query->where($db->quoteName('a.language') . ' IN (:lang, :all)')
            ->bind(':lang', $langTag)
            ->bind(':all', $allLanguages);

        $query->where($db->quoteName('a.published') . ' = 1');
        $query->order($db->quoteName('a.ordering') . ' ASC');

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        $db->setQuery($query);
        $items = $db->loadObjectList() ?: [];
        $itemId = $this->getItemID();

        if (!empty($items)) {
            foreach ($items as &$item) {
                $item->url = Route::_('index.php?option=com_speasyimagegallery&view=album&id=' . (int) $item->id . ':' . $item->alias . $itemId);
            }
        }

        return $items;
    }

    /**
     * Get images for a single album layout.
     *
     * @param   object  $params  Module parameters
     * @return  array
     */
    public function getImages($params): array
    {
        $album_id = (int) $params->get('album_id', 0);
        $limit    = (int) $params->get('album_limit', 8);

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__speasyimagegallery_images', 'a'))
            ->where($db->quoteName('album_id') . ' = :album_id')
            ->where($db->quoteName('state') . ' = 1')
            ->bind(':album_id', $album_id, ParameterType::INTEGER)
            ->order($db->quoteName('a.ordering') . ' DESC');

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        $db->setQuery($query);
        return $db->loadObjectList() ?: [];
    }

    /**
     * Retrieves the album description.
     *
     * @param   object  $params  Module parameters
     * @return  string|null
     */
    public function getAlbumDescription($params): ?string
    {
        if (!$params->get('show_album_desc', 0)) {
            return null;
        }

        $album_id = (int) $params->get('album_id', 0);
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select($db->quoteName('description'))
            ->from($db->quoteName('#__speasyimagegallery_albums'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':id', $album_id, ParameterType::INTEGER);

        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Get menu item ID for component
     *
     * @return  string
     */
    private function getItemID(): string
    {
        $db = $this->getDatabase();
        $link = '%option=com_speasyimagegallery%';

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('link') . ' LIKE :link')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':link', $link);

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result ? '&Itemid=' . (int) $result : '';
    }

    /**
     * Get category descendant IDs
     *
     * @param   int  $id  Category ID
     * @return  array
     */
    private function getCatChild(int $id): array
    {
        $children = [];
        $ids = [$id];

        while (!empty($ids)) {
            $cid = array_pop($ids);
            $children[] = (int) $cid;
            $categories = $this->getCategories($cid);

            if (!empty($categories)) {
                foreach ($categories as $cat) {
                    $ids[] = (int) $cat;
                }
            }
        }

        return $children;
    }

    /**
     * Get child categories
     *
     * @param   int  $catid  Category ID
     * @return  array
     */
    private function getCategories(int $catid): array
    {
        $db = $this->getDatabase();
        $extension = 'com_speasyimagegallery';

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('extension') . ' = :ext')
            ->where($db->quoteName('parent_id') . ' = :parent_id')
            ->bind(':ext', $extension)
            ->bind(':parent_id', $catid, ParameterType::INTEGER);

        $db->setQuery($query);
        return $db->loadColumn() ?: [];
    }
}
