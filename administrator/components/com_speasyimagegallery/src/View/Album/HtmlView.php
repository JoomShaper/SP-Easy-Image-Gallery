<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\View\Album;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use JoomShaper\Component\Speasyimagegallery\Administrator\Helper\SpeasyimagegalleryHelper;

/**
 * Album admin HTML view.
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $canDo;
    protected $id;
    protected $images = [];
    protected $total = 0;

    /**
     * Display the view.
     *
     * @param   string|null  $tpl  Template name.
     * @return  void
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        /** @var \JoomShaper\Component\Speasyimagegallery\Administrator\Model\AlbumModel $model */
        $model = $this->getModel('Album');
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->id = $this->item->id ?? 0;

        $this->images = [];
        $this->total = 0;

        if ($this->id > 0) {
            $this->images = $model->getImages($this->id);
            $this->total = $model->getCount($this->id);
        }

        $this->canDo = SpeasyimagegalleryHelper::getActions((int) $this->id);

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        $this->addToolBar();
        parent::display($tpl);
    }

    /**
     * Add toolbar buttons.
     *
     * @return  void
     */
    protected function addToolBar(): void
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew = empty($this->item->id);

        ToolbarHelper::title(
            Text::_('COM_SPEASYIMAGEGALLERY_MANAGER') . ($isNew ? Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_NEW') : Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_EDIT')),
            'pictures'
        );

        if ($isNew) {
            if ($this->canDo->get('core.create')) {
                ToolbarHelper::apply('album.apply', 'JTOOLBAR_APPLY');
                ToolbarHelper::save('album.save', 'JTOOLBAR_SAVE');
            }
            ToolbarHelper::cancel('album.cancel', 'JTOOLBAR_CANCEL');
        } else {
            if ($this->canDo->get('core.edit')) {
                ToolbarHelper::apply('album.apply', 'JTOOLBAR_APPLY');
                ToolbarHelper::save('album.save', 'JTOOLBAR_SAVE');
            }
            ToolbarHelper::cancel('album.cancel', 'JTOOLBAR_CLOSE');
        }

        if ($this->canDo->get('core.edit')) {
            ToolbarHelper::custom('album.deleteSelectedList', 'delete has-text-danger', '', Text::_('COM_SPEASYIMAGEGALLERY_DELETE'), false);
        }
    }
}
