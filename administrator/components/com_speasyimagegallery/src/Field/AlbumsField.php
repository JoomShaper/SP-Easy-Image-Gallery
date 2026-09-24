<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Albums form field for SP Easy Image Gallery.
 */
class AlbumsField extends ListField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'Albums';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    public function getOptions(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select([$db->quoteName('id', 'value'), $db->quoteName('title', 'text')])
            ->from($db->quoteName('#__speasyimagegallery_albums'))
            ->where($db->quoteName('published') . ' = 1')
            ->order('ordering DESC');

        $db->setQuery($query);
        $items = $db->loadObjectList();

        return array_merge(parent::getOptions(), $items ?: []);
    }
}
