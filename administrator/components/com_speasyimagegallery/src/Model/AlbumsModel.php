<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;

/**
 * Albums admin list model.
 */
class AlbumsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'featured', 'a.featured',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'created_by', 'a.created_by',
                'published', 'a.published',
                'catid', 'a.catid', 'category_title',
                'access', 'a.access', 'access_level',
                'created_on', 'a.created_on',
                'ordering', 'a.ordering',
                'hits', 'a.hits',
                'language', 'a.language',
                'category_id',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction.
     * @return  void
     */
    protected function populateState($ordering = 'a.id', $direction = 'desc'): void
    {
        $app = Factory::getApplication();

        $fullOrdering = $app->getUserStateFromRequest(
            $this->context . '.list.fullordering',
            'list[fullordering]',
            '',
            'string'
        );

        if (!empty($fullOrdering)) {
            $parts = explode(' ', $fullOrdering);
            $this->setState('list.ordering', $parts[0]);
            $this->setState('list.direction', $parts[1] ?? 'ASC');
        } else {
            $this->setState('list.ordering', $ordering);
            $this->setState('list.direction', $direction);
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
        $this->setState('filter.access', $access);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        $language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * @param   string  $id  A prefix for the store id.
     * @return  string  A store id.
     */
    protected function getStoreId($id = ''): string
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.category_id');
        $id .= ':' . $this->getState('filter.language');

        return parent::getStoreId($id);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\DatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($this->getState('list.select', 'a.*'))
            ->from($db->quoteName('#__speasyimagegallery_albums', 'a'));

        // Join languages
        $query->select($db->quoteName('l.title', 'language_title'))
            ->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'));

        // Join checked out user
        $query->select($db->quoteName('uc.name', 'editor'))
            ->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON ' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out'));

        // Join author
        $query->select($db->quoteName('ua.name', 'author_name'))
            ->join('LEFT', $db->quoteName('#__users', 'ua') . ' ON ' . $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by'));

        // Join viewlevels
        $query->select($db->quoteName('ug.title', 'access_title'))
            ->join('LEFT', $db->quoteName('#__viewlevels', 'ug') . ' ON ' . $db->quoteName('ug.id') . ' = ' . $db->quoteName('a.access'));

        // Join categories
        $query->select($db->quoteName('c.title', 'category_title'))
            ->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where($db->quoteName('a.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->where($db->quoteName('a.published') . ' IN (0, 1)');
        }

        // Filter by featured state
        $featured = $this->getState('filter.featured');
        if (is_numeric($featured)) {
            $query->where($db->quoteName('a.featured') . ' = :featured')
                ->bind(':featured', $featured, ParameterType::INTEGER);
        }

        // Filter by category
        $categoryId = $this->getState('filter.category_id');

        if (is_numeric($categoryId) && (int) $categoryId > 0) {
            $catQuery = $db->getQuery(true)
                ->select(['lft', 'rgt'])
                ->from($db->quoteName('#__categories'))
                ->where($db->quoteName('id') . ' = :catid')
                ->bind(':catid', $categoryId, ParameterType::INTEGER);
            $db->setQuery($catQuery);
            $cat = $db->loadObject();

            if ($cat) {
                $lft = (int) $cat->lft;
                $rgt = (int) $cat->rgt;
                $query->where($db->quoteName('c.lft') . ' >= :lft')
                    ->where($db->quoteName('c.rgt') . ' <= :rgt')
                    ->bind(':lft', $lft, ParameterType::INTEGER)
                    ->bind(':rgt', $rgt, ParameterType::INTEGER);
            }
        } elseif (is_array($categoryId) && !empty($categoryId)) {
            $categoryId = ArrayHelper::toInteger($categoryId);
            $query->whereIn($db->quoteName('a.catid'), $categoryId);
        }

        // Filter by language
        $language = $this->getState('filter.language');
        if (!empty($language)) {
            $query->where($db->quoteName('a.language') . ' = :language')
                ->bind(':language', $language);
        }

        // Search
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $searchId = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :search_id')
                    ->bind(':search_id', $searchId, ParameterType::INTEGER);
            } else {
                $searchLike = '%' . $search . '%';
                $query->where($db->quoteName('a.title') . ' LIKE :search_title')
                    ->bind(':search_title', $searchLike);
            }
        }

        // Access level
        $accessLevel = $this->getState('filter.access');
        if (!empty($accessLevel)) {
            $query->where($db->quoteName('a.access') . ' = :access')
                ->bind(':access', $accessLevel, ParameterType::INTEGER);
        }

        // Ordering
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Set featured items
     *
     * @param   array  $cid    Array of IDs
     * @param   int    $value  Featured value (0 or 1)
     * @return  boolean
     */
    public function setFeatured(array $cid, int $value): bool
    {
        if (empty($cid)) {
            return false;
        }

        $cid = ArrayHelper::toInteger($cid);
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__speasyimagegallery_albums'))
            ->set($db->quoteName('featured') . ' = :featured')
            ->whereIn($db->quoteName('id'), $cid)
            ->bind(':featured', $value, ParameterType::INTEGER);

        $db->setQuery($query);
        return (bool) $db->execute();
    }
}
