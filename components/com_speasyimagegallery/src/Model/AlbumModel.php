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

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

/**
 * Album site item model.
 */
class AlbumModel extends ItemModel
{
    /**
     * Model context string.
     *
     * @var string
     */
    protected $_context = 'com_speasyimagegallery.album';

    /**
     * Auto-populate the model state.
     *
     * @return void
     */
    protected function populateState(): void
    {
        $app = Factory::getApplication();
        $albumId = $app->input->getInt('id');
        $this->setState('album.id', $albumId);
        $this->setState('filter.language', Multilanguage::isEnabled());
    }

    /**
     * Method to get item data.
     *
     * @param   int|null  $albumId  Album ID
     * @return  object
     * @throws  Exception
     */
    public function getItem($albumId = null)
    {
        $user = Factory::getApplication()->getIdentity();
        $albumId = !empty($albumId) ? (int) $albumId : (int) $this->getState('album.id');

        if ($this->_item === null) {
            $this->_item = [];
        }

        if (!isset($this->_item[$albumId])) {
            try {
                $db = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('a.*')
                    ->from($db->quoteName('#__speasyimagegallery_albums', 'a'))
                    ->where($db->quoteName('a.id') . ' = :id')
                    ->bind(':id', $albumId, ParameterType::INTEGER);

                $query->select($db->quoteName('l.title', 'language_title'))
                    ->leftJoin($db->quoteName('#__languages', 'l') . ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'));

                $query->select($db->quoteName('ua.name', 'author_name'))
                    ->leftJoin($db->quoteName('#__users', 'ua') . ' ON ' . $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by'));

                // Filter by published state.
                $query->where($db->quoteName('a.published') . ' = 1');

                if ($this->getState('filter.language')) {
                    $langTag = Factory::getApplication()->getLanguage()->getTag();
                    $query->where($db->quoteName('a.language') . ' IN (:lang, :all)')
                        ->bind(':lang', $langTag)
                        ->bind(':all', '*');
                }

                $db->setQuery($query);
                $data = $db->loadObject();

                if (empty($data)) {
                    throw new Exception(Text::_('COM_SPEASYIMAGEGALLERY_ERROR_ALBUM_NOT_FOUND'), 404);
                }

                // Check access level.
                $groups = $user->getAuthorisedViewLevels();
                if (!in_array((int) $data->access, $groups, true)) {
                    throw new Exception(Text::_('COM_SPEASYIMAGEGALLERY_ERROR_ALBUM_NOT_AUTHORISED'), 403);
                }

                if (isset($data->id) && $data->id) {
                    $data->images = $this->getImages((int) $data->id);
                }

                $this->_item[$albumId] = $data;
            } catch (Exception $e) {
                if ($e->getCode() === 404 || $e->getCode() === 403) {
                    throw $e;
                }

                $this->setError($e);
                $this->_item[$albumId] = false;
            }
        }

        return $this->_item[$albumId];
    }

    /**
     * Get published images of an album
     *
     * @param   int  $album_id  Album ID
     * @return  array
     */
    public function getImages(int $album_id): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__speasyimagegallery_images', 'a'))
            ->where($db->quoteName('album_id') . ' = :album_id')
            ->where($db->quoteName('state') . ' = 1')
            ->bind(':album_id', $album_id, ParameterType::INTEGER)
            ->order('a.ordering DESC');

        $db->setQuery($query);
        return $db->loadObjectList() ?: [];
    }

    /**
     * Hit counter increment
     *
     * @param   int  $pk  Primary key
     * @return  boolean
     */
    public function hit(int $pk = 0): bool
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('album.id');

        if ($pk > 0) {
            $table = $this->getTable('Album', 'Administrator');
            if ($table && $table->load($pk)) {
                $table->hit($pk);
                return true;
            }
        }

        return false;
    }
}
