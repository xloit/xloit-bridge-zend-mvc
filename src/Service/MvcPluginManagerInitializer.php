<?php
/**
 * This source file is part of Xloit project.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * <http://www.opensource.org/licenses/mit-license.php>
 * If you did not receive a copy of the license and are unable to obtain it through the world-wide-web,
 * please send an email to <license@xloit.com> so we can send you a copy immediately.
 *
 * @license   MIT
 * @link      http://xloit.com
 * @copyright Copyright (c) 2016, Xloit. All rights reserved.
 */

namespace Xloit\Bridge\Zend\Mvc\Service;

use Xloit\Bridge\Zend\Mvc\Controller\PluginManagerAwareInterface;
use Xloit\Bridge\Zend\ServiceManager\AbstractServiceInitializer;
use Zend\Mvc\Controller\PluginManager;

/**
 * A {@link MvcPluginManagerInitializer} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Service
 */
class MvcPluginManagerInitializer extends AbstractServiceInitializer
{
    /**
     *
     *
     * @return string
     */
    protected function getAwareInstanceInterface()
    {
        return PluginManagerAwareInterface::class;
    }

    /**
     *
     *
     * @return string
     */
    protected function getInstanceInterface()
    {
        return PluginManager::class;
    }

    /**
     *
     *
     * @return array
     */
    protected function getServiceNames()
    {
        return [
            'xloit.mvc.pluginManager',
            'ControllerPluginManager',
            'MvcPluginManager'
        ];
    }

    /**
     *
     *
     * @return array
     */
    protected function getMethods()
    {
        return [
            'getter' => 'getControllerPlugins',
            'setter' => 'setControllerPlugins'
        ];
    }
}
