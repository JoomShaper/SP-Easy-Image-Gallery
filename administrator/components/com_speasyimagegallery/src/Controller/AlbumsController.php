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

use finfo;
use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\Input\Input;
use JoomShaper\Component\Speasyimagegallery\Administrator\Helper\SpeasyimagegalleryHelper;

/**
 * Albums admin controller.
 */
class AlbumsController extends AdminController
{
    /**
     * Constructor.
     *
     * @param   array                        $config   An optional associative array of configuration settings.
     * @param   ?MVCFactoryInterface         $factory  The factory.
     * @param   ?CMSWebApplicationInterface  $app      The Application for the dispatcher
     * @param   ?Input                       $input    The Input object for the request
     */
    public function __construct(
        $config = [],
        ?MVCFactoryInterface $factory = null,
        ?CMSWebApplicationInterface $app = null,
        ?Input $input = null
    ) {
        parent::__construct($config, $factory, $app, $input);

        // Needed for jgrid.featured to work
        $this->registerTask('unfeature', 'feature');
    }

    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name.
     * @param   string  $prefix  The class prefix.
     * @param   array   $config  Configuration array for model.
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     */
    public function getModel($name = 'Album', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Verify CSRF token and user edit authorization for AJAX operations
     *
     * @return bool
     */
    private function checkAccess(): bool
    {
        $validToken = Session::checkToken('request') || Session::checkToken();

        if (!$validToken) {
            $token = Session::getFormToken();
            $customToken = $this->input->get('csrf_token', '', 'alnum');
            $validToken = (!empty($customToken) && $customToken === $token);
        }

        if (!$validToken) {
            echo json_encode(['status' => false, 'output' => Text::_('JINVALID_TOKEN')]);
            $this->app->close();
            return false;
        }

        $user = $this->app->getIdentity() ?? Factory::getUser();

        if (!$user || $user->guest) {
            echo json_encode(['status' => false, 'output' => Text::_('JERROR_ALERTNOAUTHOR')]);
            $this->app->close();
            return false;
        }

        $albumId = (int) $this->input->get('album_id', 0, 'INT');
        $assetName = $albumId > 0 ? 'com_speasyimagegallery.album.' . $albumId : 'com_speasyimagegallery';

        $authorised = $user->authorise('core.edit', $assetName)
            || $user->authorise('core.edit.own', $assetName)
            || $user->authorise('core.edit', 'com_speasyimagegallery')
            || $user->authorise('core.edit.own', 'com_speasyimagegallery')
            || $user->authorise('core.admin', 'com_speasyimagegallery');

        if (!$authorised) {
            echo json_encode(['status' => false, 'output' => Text::_('JERROR_ALERTNOAUTHOR')]);
            $this->app->close();
            return false;
        }

        return true;
    }

    /**
     * Upload File via AJAX
     *
     * @return void
     */
    public function upload_image(): void
    {
        $this->checkAccess();

        $model = $this->getModel();

        if (!$model) {
            echo json_encode(['status' => false, 'output' => Text::_('JERROR_ALERTNOAUTHOR')]);
            $this->app->close();
            return;
        }

        $input = $this->input;
        $album_id = $input->post->get('album_id', 0, 'INT');
        $file = $input->files->get('image');
        $lang = $input->get('lang', '*', 'STRING');

        $report = [];
        $params = ComponentHelper::getParams('com_speasyimagegallery');
        $width = (int) $params->get('thumb_width', 400);
        $height = (int) $params->get('thumb_height', 400);

        if (!empty($file) && is_array($file)) {
            if ($file['error'] == UPLOAD_ERR_OK) {
                $error = false;
                $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
                $mediaHelper = new MediaHelper();
                $postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));
                $memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

                // Check for total size of post back data.
                if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit != -1 && $contentLength > $memoryLimit)) {
                    $report['status'] = false;
                    $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_TOTAL_SIZE_EXCEEDS');
                    echo json_encode($report);
                    $this->app->close();
                }

                $uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

                if (($file['error'] == 1) || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize)) {
                    $report['status'] = false;
                    $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_LARGE');
                    $error = true;
                }

                $accepted_formats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

                if (!$error) {
                    $file_ext = strtolower(SpeasyimagegalleryHelper::getExt($file['name']));

                    if (in_array($file_ext, $accepted_formats, true)) {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->file($file['tmp_name']);

                        $allowedMimes = [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/bmp',
                            'image/webp'
                        ];

                        if (!in_array($mimeType, $allowedMimes, true)) {
                            $report['status'] = false;
                            $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_NOT_SUPPORTED');
                            echo json_encode($report);
                            $this->app->close();
                        }

                        $imageInfo = @getimagesize($file['tmp_name']);
                        if ($imageInfo === false) {
                            $report['status'] = false;
                            $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_NOT_SUPPORTED');
                            echo json_encode($report);
                            $this->app->close();
                        }

                        $albumFolder = 'images/speasyimagegallery/albums/' . $album_id . '/images';
                        $name = $file['name'];
                        $path = $file['tmp_name'];

                        $media_file = preg_replace("/[\s\-_]+/", "-", File::makeSafe(basename(strtolower($name))));
                        $i = 0;
                        do {
                            $base_name = File::stripExt($media_file) . ($i ? "$i" : "");
                            $ext = SpeasyimagegalleryHelper::getExt($media_file);
                            $media_name = $base_name . '.' . $ext;
                            $i++;
                            $dest = JPATH_ROOT . '/' . $albumFolder . '/' . $media_name;
                        } while (file_exists($dest));

                        if (File::upload($path, $dest, false, true)) {
                            $sources = SpeasyimagegalleryHelper::createThumbs($dest, [
                                'mini' => [64, 64],
                                'thumb' => [$width, $height],
                                'x_thumb' => [$width * 2, $height * 2],
                                'y_thumb' => [$width, (int) round($height * 1.5)]
                            ], $albumFolder, $base_name, $ext);

                            $report['thumb'] = Uri::root(true) . '/' . ($sources['thumb'] ?? '');

                            $image = [
                                'title' => $base_name,
                                'alt' => $base_name,
                                'ext' => $ext,
                                'album_id' => $album_id,
                                'images' => json_encode($sources),
                                'lang' => $lang
                            ];

                            $inserted_image = $model->insertMedia($image);

                            $report['status'] = true;
                            $report['output'] = LayoutHelper::render('image', ['image' => $inserted_image]);
                        } else {
                            $report['status'] = false;
                            $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOAD_FAILED');
                        }
                    } else {
                        $report['status'] = false;
                        $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_NOT_SUPPORTED');
                    }
                }
            }
        } else {
            $report['status'] = false;
            $report['output'] = Text::_('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOAD_FAILED');
        }

        $report['count'] = $model->getCount($album_id);

        echo json_encode($report);
        $this->app->close();
    }

    /**
     * Sort images via AJAX
     *
     * @return void
     */
    public function sort_images(): void
    {
        $this->checkAccess();

        $input = $this->input;
        $orders = $input->get('orders', '', 'STRING');
        $orders = explode(',', $orders);
        $model = $this->getModel();

        if ($model) {
            $model->save_ajax_orderings($orders);
        }

        $this->app->close();
    }

    /**
     * Change image state via AJAX
     *
     * @return void
     */
    public function image_state(): void
    {
        $this->checkAccess();

        $input = $this->input;
        $id = $input->get('id', 0, 'INT');
        $state = $input->get('state', 'enabled', 'STRING');
        $model = $this->getModel();

        if ($model) {
            $model->change_image_state($id, $state);
        }

        $this->app->close();
    }

    /**
     * Delete image via AJAX
     *
     * @return void
     */
    public function image_delete(): void
    {
        $this->checkAccess();

        $input = $this->input;
        $id = $input->get('id', 0, 'INT');
        $album_id = $input->get('album_id', 0, 'INT');
        $model = $this->getModel();
        $result = $model ? $model->image_delete($id, $album_id) : false;
        echo json_encode($result);
        $this->app->close();
    }

    /**
     * Edit image via AJAX
     *
     * @return void
     */
    public function edit_image(): void
    {
        $this->checkAccess();

        $input = $this->input;
        $id = $input->get('id', 0, 'INT');
        $album_id = $input->get('album_id', 0, 'INT');
        $model = $this->getModel();

        if (!$model) {
            echo json_encode(['status' => false, 'output' => Text::_('JERROR_ALERTNOAUTHOR')]);
            $this->app->close();
            return;
        }

        $image = $model->getImages($album_id, $id);
        echo LayoutHelper::render('edit', ['image' => $image]);
        $this->app->close();
    }

    /**
     * Save image via AJAX
     *
     * @return void
     */
    public function save_image(): void
    {
        $this->checkAccess();

        $input = $this->input;
        $id = $input->get('id', 0, 'INT');
        $title = $input->get('title', '', 'STRING');
        $alt = $input->get('alt', '', 'STRING');
        $desc = $input->get('desc', '', 'STRING');

        $attr = [
            'id' => $id,
            'title' => $title,
            'alt' => $alt,
            'desc' => $desc
        ];

        $model = $this->getModel();

        if (!$model) {
            echo json_encode(['status' => false, 'output' => Text::_('JERROR_ALERTNOAUTHOR')]);
            $this->app->close();
            return;
        }

        $model->saveImage($attr);
        echo json_encode(['status' => true]);
        $this->app->close();
    }

    /**
     * Toggle featured status
     *
     * @return void
     */
    public function feature(): void
    {
        $input = $this->input;
        $cid = (array) $input->get('cid', [], 'array');
        $value = ($this->getTask() === 'feature') ? 1 : 0;

        /** @var \JoomShaper\Component\Speasyimagegallery\Administrator\Model\AlbumsModel $model */
        $model = $this->getModel('Albums');

        if ($model->setFeatured($cid, $value)) {
            $message = $value ? 'Items featured' : 'Items unfeatured';
            $this->setMessage(Text::_($message));
        }

        $this->setRedirect('index.php?option=com_speasyimagegallery&view=albums');
    }
}
