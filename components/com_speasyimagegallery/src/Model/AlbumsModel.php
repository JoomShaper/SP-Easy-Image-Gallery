<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;

/**
 * Albums site list model.
 */
class AlbumsModel extends ListModel
{
    /**
     * Auto-populate the model state.
     *
     * @param   string|null  $ordering   Ordering field.
     * @param   string|null  $direction  Direction.
     * @return  void
     */
    protected function populateState($ordering = null, $direction = null): void
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
        $limit = $params->get('limit', 20);
        $this->setState('list.limit', $limit);

        $showFeaturedOnly = (int) $params->get('show_featured_only', 0);
        $this->setState('filter.featured', $showFeaturedOnly);
    }

    /**
     * Method to build the list query.
     *
     * @return  \Joomla\Database\DatabaseQuery
     */
    protected function getListQuery()
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        $catid = $app->input->get('catid', 0, 'INT');

        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__speasyimagegallery_albums', 'a'));

        // Join over categories
        $query->select($db->quoteName('c.title', 'category_title'))
            ->select($db->quoteName('c.alias', 'category_alias'))
            ->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

        // Images count subquery
        $imgSub = '(SELECT b.album_id, COUNT(b.album_id) as count FROM ' . $db->quoteName('#__speasyimagegallery_images', 'b') . ' WHERE b.state = 1 GROUP BY b.album_id)';
        $query->select('CASE WHEN img.count IS NULL THEN 0 ELSE img.count END AS count')
            ->join('LEFT', $imgSub . ' AS img ON img.album_id = a.id');

        // Authorised access levels
        $groups = $user->getAuthorisedViewLevels();
        if (!empty($groups)) {
            $query->whereIn($db->quoteName('a.access'), $groups);
        }

        // Filter category
        if ($catid > 0) {
            $descendants = $this->getCatChild($catid);
            if (!empty($descendants)) {
                $query->whereIn($db->quoteName('a.catid'), ArrayHelper::toInteger($descendants));
            }
        }

        // Filter by featured
        if ((int) $this->getState('filter.featured', 0) === 1) {
            $query->where($db->quoteName('a.featured') . ' = 1');
        }

        // Filter by language
        $langTag = Factory::getApplication()->getLanguage()->getTag();
        $allLanguages = '*';
        $query->where($db->quoteName('a.language') . ' IN (:lang, :all)')
            ->bind(':lang', $langTag)
            ->bind(':all', $allLanguages);

        $query->where($db->quoteName('a.published') . ' = 1');
        $query->order($db->quoteName('a.ordering') . ' ASC');

        return $query;
    }

    /**
     * Get category child IDs
     *
     * @param   int  $id  Category ID
     * @return  array
     */
    public function getCatChild(int $id): array
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
     * Get child category IDs of a category
     *
     * @param   int  $catid  Category ID
     * @return  array
     */
    public function getCategories(int $catid): array
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
