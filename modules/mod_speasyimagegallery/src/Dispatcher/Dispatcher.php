<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  mod_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Module\Speasyimagegallery\Site\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

/**
 * Dispatcher class for mod_speasyimagegallery.
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     */
    protected function getLayoutData(): array
    {
        $data   = parent::getLayoutData();
        $params = $data['params'];
        $layout = $params->get('layout', 'album');

        /** @var \JoomShaper\Module\Speasyimagegallery\Site\Helper\SpeasyimagegalleryHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('SpeasyimagegalleryHelper');

        if ($layout === 'albums') {
            $data['albums'] = $helper->getAlbumList($params);
        } else {
            $data['images'] = $helper->getImages($params);
            $data['albumDescription'] = (!empty($data['images'])) ? $helper->getAlbumDescription($params) : null;
        }

        return $data;
    }
}
