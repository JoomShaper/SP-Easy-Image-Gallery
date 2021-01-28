<?php
/**
* @package     SP Easy Image Gallery
* @subpackage  com_speasyimagegallery
*
* @copyright   Copyright (C) 2010 - 2021 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die('Restricted Access!');

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;


class com_speasyimagegalleryInstallerScript
{
    
    public function uninstall($parent)
    {
        $status = new stdClass;
        $status->modules = array();
        $manifest = $parent->getParent()->manifest;
        
        // Uninstall Modules
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module)
        {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('extension_id'));
            $query->from($db->quoteName('#__extensions'));
            $query->where($db->quoteName('type') . ' = ' . $db->quote('module'));
            $query->where($db->quoteName('element') . ' = ' . $db->quote($name));
            $db->setQuery($query);
            $extension_id = $db->loadResult();

            if (!empty($extension_id))
            {
                $installer = new Installer;
                $result = $installer->uninstall('module', $extension_id);
                $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
            }
        }
    }
    
    public function postflight($type, $parent) {

        if ($type == 'uninstall')
        {
            return true;
        }

        $db = Factory::getDbo();
        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;
        
        // Install Modules
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module)
        {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            $path = $src . '/modules/' . $name;
            $position = (isset($module->attributes()->position) && $module->attributes()->position) ? (string)$module->attributes()->position : '';
            $ordering = (isset($module->attributes()->ordering) && $module->attributes()->ordering) ? (string)$module->attributes()->ordering : 0;
            
            $installer = new Installer;
            $result = $installer->install($path);
        }
    }
}