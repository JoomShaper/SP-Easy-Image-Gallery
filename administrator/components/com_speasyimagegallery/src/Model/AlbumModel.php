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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\String\StringHelper;
use stdClass;

/**
 * Album admin model.
 */
class AlbumModel extends AdminModel
{
    /**
     * The type alias for this content.
     *
     * @var string
     */
    public $typeAlias = 'com_speasyimagegallery.album';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     * @return  \Joomla\CMS\Table\Table
     */
    public function getTable($name = 'Album', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data, false if not.
     * @return  \Joomla\CMS\Form\Form|false
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_speasyimagegallery.album',
            'album',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_speasyimagegallery.edit.album.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     * @return  boolean
     */
    public function save($data): bool
    {
        $input = Factory::getApplication()->input;

        if (isset($data['image'])) {
            $filteredImage = explode('#', $data['image']);
            $data['image'] = str_replace('%20', ' ', $filteredImage[0]);
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), ['apply', 'save'], true) && (!isset($data['id']) || (int) $data['id'] == 0)) {
            if (empty($data['alias'])) {
                if (Factory::getApplication()->get('unicodeslugs') == 1) {
                    $data['alias'] = OutputFilter::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = OutputFilter::stringURLSafe($data['title']);
                }

                $table = $this->getTable();
                while ($table->load(['alias' => $data['alias'], 'catid' => $data['catid'] ?? 0])) {
                    $data['alias'] = StringHelper::increment($data['alias'], 'dash');
                }
            }
        }

        return parent::save($data);
    }

    /**
     * Method to check if it's OK to delete a record.
     *
     * @param   object  $record  A record object.
     * @return  boolean
     */
    protected function canDelete($record): bool
    {
        if (!empty($record->id)) {
            if ($record->published != -2) {
                return false;
            }

            return Factory::getApplication()->getIdentity()->authorise('core.delete', 'com_speasyimagegallery.album.' . (int) $record->id);
        }

        return false;
    }

    /**
     * Delete album and its images
     *
     * @param   array  &$pks  An array of record primary keys.
     * @return  boolean
     */
    public function delete(&$pks): bool
    {
        $return = parent::delete($pks);

        if ($return) {
            foreach ($pks as $pk) {
                // Delete physical album directory
                $cover = JPATH_ROOT . '/images/speasyimagegallery/albums/' . $pk;
                if (is_dir($cover)) {
                    Folder::delete($cover);
                }

                // Delete associated images
                $images = $this->getImages($pk);
                if (!empty($images)) {
                    foreach ($images as $image) {
                        $this->image_delete($image->id, $pk);
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Get images for an album
     *
     * @param   int  $album_id  Album ID
     * @param   int  $id        Image ID
     * @return  mixed
     */
    public function getImages(int $album_id = 0, int $id = 0)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__speasyimagegallery_images', 'a'));

        if ($album_id > 0) {
            $query->where($db->quoteName('album_id') . ' = :album_id')
                ->bind(':album_id', $album_id, ParameterType::INTEGER);
        }

        if ($id > 0) {
            $query->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query);
            return $db->loadObject();
        }

        $query->order('a.ordering DESC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Get image count for an album
     *
     * @param   int  $album_id  Album ID
     * @return  int
     */
    public function getCount(int $album_id = 0): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(a.id)')
            ->from($db->quoteName('#__speasyimagegallery_images', 'a'));

        if ($album_id > 0) {
            $query->where($db->quoteName('album_id') . ' = :album_id')
                ->bind(':album_id', $album_id, ParameterType::INTEGER);
        }

        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Insert image record
     *
     * @param   array  $attribs  Attributes array
     * @return  object|null
     */
    public function insertMedia(array $attribs): ?object
    {
        $total = $this->getCount((int) $attribs['album_id']);
        $db = $this->getDatabase();

        $title = ucwords(preg_replace('/[\s\-_]+/', ' ', $attribs['title']));
        $now = Factory::getDate()->toSql();
        $userId = Factory::getApplication()->getIdentity()->id;

        $image = new stdClass();
        $image->filename = $attribs['title'] . '.' . $attribs['ext'];
        $image->title = $title;
        $image->alt = $title;
        $image->album_id = (int) $attribs['album_id'];
        $image->state = 1;
        $image->ordering = $total + 1;
        $image->created = $now;
        $image->created_by = $userId;
        $image->modified = $now;
        $image->modified_by = $userId;
        $image->images = $attribs['images'];
        $image->language = $attribs['lang'];

        $db->insertObject('#__speasyimagegallery_images', $image, 'id');
        $insertId = (int) $db->insertid();

        return $this->getImages((int) $attribs['album_id'], $insertId);
    }

    /**
     * Save ordering via AJAX
     *
     * @param   array  $orderings  Array of image IDs in order
     * @return  void
     */
    public function save_ajax_orderings(array $orderings): void
    {
        if (count($orderings)) {
            $count = count($orderings);
            $db = $this->getDatabase();

            foreach ($orderings as $key => $id) {
                $image = new stdClass();
                $image->id = (int) $id;
                $image->ordering = ($count - $key) + 1;
                $db->updateObject('#__speasyimagegallery_images', $image, 'id');
            }
        }
    }

    /**
     * Change image state
     *
     * @param   int     $id     Image ID
     * @param   string  $state  Current state string
     * @return  void
     */
    public function change_image_state(int $id, string $state): void
    {
        $image = new stdClass();
        $image->id = $id;
        $image->state = ($state === 'enabled') ? 0 : 1;
        $this->getDatabase()->updateObject('#__speasyimagegallery_images', $image, 'id');
    }

    /**
     * Save image meta info
     *
     * @param   array  $attr  Image attributes
     * @return  void
     */
    public function saveImage(array $attr): void
    {
        $image = new stdClass();
        $image->id = (int) $attr['id'];
        $image->title = $attr['title'];
        $image->alt = $attr['alt'];
        $image->description = $attr['desc'];
        $this->getDatabase()->updateObject('#__speasyimagegallery_images', $image, 'id');
    }

    /**
     * Delete an image and physical files
     *
     * @param   int  $id        Image ID
     * @param   int  $album_id  Album ID
     * @return  array
     */
    public function image_delete(int $id, int $album_id): array
    {
        $image = $this->getImages($album_id, $id);

        if (!empty($image) && !empty($image->images)) {
            $sources = json_decode($image->images, true);

            if (is_array($sources)) {
                foreach ($sources as $source) {
                    $path = JPATH_ROOT . '/' . $source;
                    if (file_exists($path)) {
                        File::delete($path);
                    }
                }
            }
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__speasyimagegallery_images'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();

        return [
            'status' => true,
            'count'  => $this->getCount($album_id),
        ];
    }
}
