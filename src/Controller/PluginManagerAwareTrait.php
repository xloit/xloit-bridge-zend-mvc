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

namespace Xloit\Bridge\Zend\Mvc\Controller;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\PluginManager;

/**
 * A {@link PluginManagerAwareTrait} trait.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller
 */
trait PluginManagerAwareTrait
{
    /**
     *
     *
     * @var PluginManager
     */
    protected $controllerPlugins;

    /**
     *
     *
     * @param string $plugin
     *
     * @return AbstractPlugin
     */
    public function getControllerPlugin($plugin)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getControllerPlugins()->get($plugin);
    }

    /**
     *
     *
     * @return PluginManager
     */
    public function getControllerPlugins()
    {
        return $this->controllerPlugins;
    }

    /**
     *
     *
     * @param PluginManager $plugins
     *
     * @return $this
     */
    public function setControllerPlugins(PluginManager $plugins)
    {
        $this->controllerPlugins = $plugins;

        return $this;
    }
}
