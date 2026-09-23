<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Site\View\Album;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Album site HTML view.
 */
class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $params;

    /**
     * Display the view.
     *
     * @param   string|null  $tpl  Template name.
     * @return  void
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        $this->item = $this->get('Item');

        $app = Factory::getApplication();
        $this->params = $app->getParams();
        $menu = $app->getMenu()->getActive();

        if ($menu) {
            $this->params->merge($menu->getParams());
        }

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        /** @var \JoomShaper\Component\Speasyimagegallery\Site\Model\AlbumModel $model */
        $model = $this->getModel();
        $model->hit();

        $this->prepareDocument($this->item);
        parent::display($tpl);
    }

    /**
     * Prepares the document.
     *
     * @param   object  $item  Album item.
     * @return  void
     */
    protected function prepareDocument($item): void
    {
        $app = Factory::getApplication();

        $this->params->def('page_heading', $item->title);
        $title = $item->title;

        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ((int) $app->get('sitename_pagetitles', 0) === 1) {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ((int) $app->get('sitename_pagetitles', 0) === 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);

        if (!empty($item->metadesc)) {
            $this->document->setDescription($item->metadesc);
        }

        if (!empty($item->metakey)) {
            $this->document->setMetadata('keywords', $item->metakey);
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
