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

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Event\DispatcherInterface;

/**
 * Album table class.
 */
class AlbumTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseInterface     $db          Database connector object
     * @param   ?DispatcherInterface  $dispatcher  Event dispatcher for this table
     */
    public function __construct(DatabaseInterface $db, ?DispatcherInterface $dispatcher = null)
    {
        $this->typeAlias = 'com_speasyimagegallery.album';

        parent::__construct('#__speasyimagegallery_albums', 'id', $db, $dispatcher);
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

        // Verify that the alias is unique
        $alias = (string) $this->alias;
        $query = $this->getDatabase()->getQuery(true)
            ->select($this->getDatabase()->quoteName('id'))
            ->from($this->getDatabase()->quoteName('#__speasyimagegallery_albums'))
            ->where($this->getDatabase()->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias);

        if ($this->id) {
            $id = (int) $this->id;
            $query->where($this->getDatabase()->quoteName('id') . ' != :id')
                ->bind(':id', $id, ParameterType::INTEGER);
        }

        $this->getDatabase()->setQuery($query);
        $duplicateId = (int) $this->getDatabase()->loadResult();

        if ($duplicateId > 0) {
            $this->setError(Text::_('COM_SPEASYIMAGEGALLERY_ERROR_UNIQUE_ALIAS'));
            return false;
        }

        return parent::store($updateNulls);
    }

    /**
     * Overloaded check method to ensure data integrity
     *
     * @return  boolean  True if the buffer is valid
     * @throws  \UnexpectedValueException
     */
    public function check(): bool
    {
        if (trim((string) $this->title) === '') {
            throw new \UnexpectedValueException(Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_TITLE_EMPTY'));
        }

        if (empty($this->alias)) {
            $this->alias = $this->title;
        }

        if (empty($this->attribs)) {
            $this->attribs = '';
        }

        if (empty($this->metadata)) {
            $this->metadata = '';
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language ?? '*');

        if (trim(str_replace('-', '', $this->alias)) === '') {
            $this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
        }

        return true;
    }
}
