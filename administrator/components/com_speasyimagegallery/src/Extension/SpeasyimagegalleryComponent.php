<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_speasyimagegallery
 */
class SpeasyimagegalleryComponent extends MVCComponent implements
    BootableExtensionInterface,
    CategoryServiceInterface,
    RouterServiceInterface
{
    use HTMLRegistryAwareTrait;
    use RouterServiceTrait;
    use CategoryServiceTrait;

    /**
     * Booting the extension.
     *
     * @param   ContainerInterface  $container  The container
     * @return  void
     */
    public function boot(ContainerInterface $container): void
    {
    }

    /**
     * Returns the table for category items counting.
     *
     * @param   ?string  $section  The section
     * @return  string|null
     */
    protected function getTableNameForSection(?string $section = null): ?string
    {
        return 'speasyimagegallery_albums';
    }

    /**
     * Returns the state column for the given section.
     *
     * @param   ?string  $section  The section
     * @return  string|null
     */
    protected function getStateColumnForSection(?string $section = null): ?string
    {
        return 'published';
    }
}
