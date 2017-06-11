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

namespace Xloit\Bridge\Zend\Mvc\Listener;

use Xloit\Bridge\Zend\EventManager\Listener\AbstractListenerAggregate;
use Xloit\Bridge\Zend\Mvc\Application;
use Xloit\Std\ArrayUtils;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * An {@link ApplicationBootstrapListener} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Listener
 */
class ApplicationBootstrapListener extends AbstractListenerAggregate
{
    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager implementation will pass this to the
     * aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = PHP_INT_MAX)
    {
        $this->listeners[] = $events->attach(
            Application::EVENT_PRE_BOOTSTRAP,
            [
                $this,
                'onBootstrapLoadConfig'
            ],
            $priority - 1
        );
    }

    /**
     * Listen to the "bootstrap" event.
     *
     * @param MvcEvent $event
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Xloit\Std\Exception\RuntimeException
     */
    public function onBootstrapLoadConfig(MvcEvent $event)
    {
        /* @var Application $application */
        $application = $event->getTarget();
        /** @var \Zend\ServiceManager\ServiceManager $serviceManager */
        $serviceManager     = $application->getServiceManager();
        $context            = $application->getContext();
        $applicationContext = $application->getApplicationContext();
        /** @var array $activeContext */
        $activeContext = ArrayUtils::get($applicationContext, $context, []);
        $configuration = $serviceManager->get('Config');
        $config        = [];

        $config = ArrayUtils::merge(
            $config,
            ArrayUtils::get($configuration, Application::CONFIG_KEY_CONTEXT . '.' . $context, [])
        );

        foreach ($activeContext as $contextPart) {
            $config = ArrayUtils::merge(
                $config,
                ArrayUtils::get($configuration, Application::CONFIG_KEY_CONTEXT . '.' . $contextPart, [])
            );
            $config = ArrayUtils::merge(
                $config,
                ArrayUtils::get($config, Application::CONFIG_KEY_CONTEXT . '.' . $contextPart, [])
            );
        }

        $config = ArrayUtils::merge(
            $config,
            ArrayUtils::get($configuration, Application::CONFIG_KEY_ENV . '.' . $application->getEnvironment(), [])
        );

        $config = ArrayUtils::merge(
            $config,
            ArrayUtils::get($config, Application::CONFIG_KEY_ENV . '.' . $application->getEnvironment(), [])
        );

        /** @var \Zend\ModuleManager\ModuleManager $moduleManager */
        $moduleManager  = $serviceManager->get('ModuleManager');
        $configListener = $moduleManager->getEvent()->getConfigListener();

        $configListener->setMergedConfig(
            ArrayUtils::merge(
                $configListener->getMergedConfig(false),
                $config
            )
        );

        // Tell ServiceManager to load "Config" from the module ConfigListener.
        $allowOverride = $serviceManager->getAllowOverride();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('config', null);
        $serviceManager->setService('Config', null);
        $serviceManager->setService('configuration', null);
        $serviceManager->setService('Configuration', null);
        $serviceManager->setAllowOverride($allowOverride);
    }
}
