<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  mod_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Service provider for mod_speasyimagegallery
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     * @return  void
     */
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\JoomShaper\\Module\\Speasyimagegallery'));
        $container->registerServiceProvider(new HelperFactory('\\JoomShaper\\Module\\Speasyimagegallery\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
