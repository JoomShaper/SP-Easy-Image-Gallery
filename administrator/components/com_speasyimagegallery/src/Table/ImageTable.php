<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Event\DispatcherInterface;

/**
 * Image table class.
 */
class ImageTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseInterface     $db          Database connector object
     * @param   ?DispatcherInterface  $dispatcher  Event dispatcher for this table
     */
    public function __construct(DatabaseInterface $db, ?DispatcherInterface $dispatcher = null)
    {
        $this->typeAlias = 'com_speasyimagegallery.image';

        parent::__construct('#__speasyimagegallery_images', 'id', $db, $dispatcher);
    }

    /**
     * Overloaded store method
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     * @return  boolean  True on success.
     */
    public function store($updateNulls = false): bool
    {
        $date = Factory::getDate()->toSql();
        $user = Factory::getApplication()->getIdentity();

        if ($this->id) {
            $this->modified = $date;
            $this->modified_by = $user->id;
        } else {
            if (!(int) $this->created) {
                $this->created = $date;
            }
            if (empty($this->created_by)) {
                $this->created_by = $user->id;
            }
            if (!(int) $this->modified) {
                $this->modified = $date;
            }
            if (empty($this->modified_by)) {
                $this->modified_by = $user->id;
            }
        }

        return parent::store($updateNulls);
    }
}
