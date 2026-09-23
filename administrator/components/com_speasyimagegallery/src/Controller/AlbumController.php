<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\Filesystem\Folder;
use JoomShaper\Component\Speasyimagegallery\Administrator\Helper\SpeasyimagegalleryHelper;

/**
 * Album form controller.
 */
class AlbumController extends FormController
{
    /**
     * Method to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     * @return  boolean
     */
    protected function allowAdd($data = []): bool
    {
        return parent::allowAdd($data);
    }

    /**
     * Method to check if you can edit a record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     * @return  boolean
     */
    protected function allowEdit($data = [], $key = 'id'): bool
    {
        $id = isset($data[$key]) ? (int) $data[$key] : 0;

        if (!empty($id)) {
            return Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_speasyimagegallery.album.' . $id);
        }

        return parent::allowEdit($data, $key);
    }

    /**
     * Function that allows child libraries to access the postSaveHook.
     *
     * @param   BaseDatabaseModel  $model      The data model object.
     * @param   array              $validData  The validated data.
     * @return  boolean
     */
    protected function postSaveHook(BaseDatabaseModel $model, $validData = []): bool
    {
        $params = ComponentHelper::getParams('com_speasyimagegallery');
        $width = (int) $params->get('thumb_width', 400);
        $height = (int) $params->get('thumb_height', 400);
        $item = $model->getItem();
        $id = $item->id;

        // Create the album folder first
        $albumFolder = JPATH_ROOT . '/images/speasyimagegallery/albums/' . $id;
        if (!Folder::create($albumFolder, 0750)) {
            return false;
        }

        // Create the "images" folder inside the album folder
        $imagesFolder = $albumFolder . '/images';
        if (!Folder::create($imagesFolder, 0750)) {
            return false;
        }

        $image = JPATH_ROOT . '/' . $item->image;

        if (file_exists($image)) {
            $image = MediaHelper::getCleanMediaFieldValue($image);
            $ext = SpeasyimagegalleryHelper::getExt($image);

            // Create thumbnails for the image
            SpeasyimagegalleryHelper::createThumbs($image, ['thumb' => [$width, $height]], $albumFolder, '', $ext);
        }

        return true;
    }

    /**
     * Delete selected image from list
     *
     * @return void
     */
    public function deleteSelectedList(): void
    {
        $input = $this->input;
        $selected_id = $input->get('boxchecked', '', 'STRING');
        $album_id = $input->get('album_id', 0, 'INT');

        $app = $this->app;

        if (empty($album_id)) {
            $url = Route::_('index.php?option=com_speasyimagegallery&view=album&layout=edit&id=' . $album_id, false);
            $app->enqueueMessage(Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_NO'), 'error');
            $app->redirect($url);
        }

        $image_items = [];

        if (!empty($selected_id)) {
            $image_items = explode(',', $selected_id);
            $image_items = array_map('intval', $image_items);
        }

        if (!empty($image_items)) {
            foreach ($image_items as $ii) {
                $this->image_delete((int) $ii, $album_id);
            }
        }

        $url = Route::_('index.php?option=com_speasyimagegallery&view=album&layout=edit&id=' . $album_id, false);
        $app->enqueueMessage(Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_DELETE_1'));
        $app->redirect($url);
    }

    /**
     * Delete Image
     *
     * @param   int  $image_id  The image id
     * @param   int  $album_id  The album id
     * @return  mixed
     */
    public function image_delete(int $image_id, int $album_id)
    {
        $model = $this->getModel();
        return $model->image_delete($image_id, $album_id);
    }
}
