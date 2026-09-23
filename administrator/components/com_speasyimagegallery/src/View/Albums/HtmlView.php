<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\View\Albums;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use JoomShaper\Component\Speasyimagegallery\Administrator\Helper\SpeasyimagegalleryHelper;

/**
 * Albums admin HTML view.
 */
class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    public $filterForm;
    public $activeFilters;
    protected $canDo;

    /**
     * Display the view.
     *
     * @param   string|null  $tpl  Template name.
     * @return  void
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $this->canDo = SpeasyimagegalleryHelper::getActions();

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
        ToolbarHelper::title(
            Text::_('COM_SPEASYIMAGEGALLERY_MANAGER') . Text::_('COM_SPEASYIMAGEGALLERY_MANAGER_ALBUMS'),
            'pictures'
        );

        if ($this->canDo->get('core.create')) {
            ToolbarHelper::addNew('album.add', 'JTOOLBAR_NEW');
        }

        if ($this->canDo->get('core.edit')) {
            ToolbarHelper::editList('album.edit', 'JTOOLBAR_EDIT');
        }

        if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
            ToolbarHelper::deleteList('', 'albums.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($this->state->get('filter.published') !== -2) {
            ToolbarHelper::trash('albums.trash');
        }

        if ($this->canDo->get('core.admin')) {
            ToolbarHelper::divider();
            ToolbarHelper::preferences('com_speasyimagegallery');
        }
    }
}
