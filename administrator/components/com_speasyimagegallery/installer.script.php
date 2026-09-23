<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted Access!');

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 * Installation script for com_speasyimagegallery.
 */
class com_speasyimagegalleryInstallerScript
{
    /**
     * Uninstall method
     *
     * @param   object  $parent  Installer object
     * @return  void
     */
    public function uninstall($parent): void
    {
        $status = new stdClass();
        $status->modules = [];
        $manifest = $parent->getParent()->manifest;

        // Uninstall Modules
        $modules = $manifest->xpath('modules/module');
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $name   = (string) $module->attributes()->module;
                $client = (string) $module->attributes()->client;

                $db    = Factory::getContainer()->get(DatabaseInterface::class);
                $query = $db->getQuery(true)
                    ->select($db->quoteName('extension_id'))
                    ->from($db->quoteName('#__extensions'))
                    ->where($db->quoteName('type') . ' = :type')
                    ->where($db->quoteName('element') . ' = :element')
                    ->bind(':type', 'module')
                    ->bind(':element', $name);

                $db->setQuery($query);
                $extension_id = (int) $db->loadResult();

                if ($extension_id > 0) {
                    $installer = new Installer();
                    $installer->setDatabase($db);
                    $result = $installer->uninstall('module', $extension_id);
                    $status->modules[] = ['name' => $name, 'client' => $client, 'result' => $result];
                }
            }
        }
    }

    /**
     * Postflight method
     *
     * @param   string  $type    Installation type ('install', 'update', 'discover_install')
     * @param   object  $parent  Installer object
     * @return  boolean
     */
    public function postflight($type, $parent): bool
    {
        if ($type === 'uninstall') {
            return true;
        }

        // Clean up legacy files from older versions to prevent class collisions
        $this->removeObsoleteFiles();

        $db       = Factory::getContainer()->get(DatabaseInterface::class);
        $src      = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;

        // Install or Update Modules
        $modules = $manifest->xpath('modules/module');
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $name = (string) $module->attributes()->module;
                $path = $src . '/modules/' . $name;

                if (is_dir($path)) {
                    $installer = new Installer();
                    $installer->setDatabase($db);
                    $installer->install($path);
                }
            }
        }

        return true;
    }

    /**
     * Remove obsolete files and directories from previous installations.
     *
     * @return void
     */
    private function removeObsoleteFiles(): void
    {
        $obsoleteFiles = [
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/speasyimagegallery.php',
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/controller.php',
            JPATH_SITE . '/components/com_speasyimagegallery/speasyimagegallery.php',
            JPATH_SITE . '/components/com_speasyimagegallery/controller.php',
            JPATH_SITE . '/components/com_speasyimagegallery/router.php',
            JPATH_SITE . '/modules/mod_speasyimagegallery/mod_speasyimagegallery.php',
            JPATH_SITE . '/modules/mod_speasyimagegallery/helper.php',
        ];

        foreach ($obsoleteFiles as $file) {
            if (file_exists($file)) {
                File::delete($file);
            }
        }

        $obsoleteFolders = [
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/controllers',
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/tables',
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/helpers',
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/views',
            JPATH_SITE . '/components/com_speasyimagegallery/views',
            JPATH_SITE . '/components/com_speasyimagegallery/models',
        ];

        foreach ($obsoleteFolders as $folder) {
            if (is_dir($folder)) {
                Folder::delete($folder);
            }
        }
    }
}