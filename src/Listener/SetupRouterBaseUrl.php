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
use Xloit\Std\ArrayUtils;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\TreeRouteStack;

/**
 * A {@link SetupRouterBaseUrl} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Listener
 */
class SetupRouterBaseUrl extends AbstractListenerAggregate
{
    /**
     *
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = -100)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [
                $this,
                'onRoute'
            ],
            $priority
        );
    }

    /**
     *
     *
     * @param MvcEvent $event
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Xloit\Std\Exception\RuntimeException
     */
    public function onRoute(MvcEvent $event)
    {
        $router = $event->getRouter();

        if ($router instanceof TreeRouteStack) {
            $serviceManager = $event->getApplication()->getServiceManager();
            $config         = $serviceManager->get('Config');

            $router->setBaseUrl(ArrayUtils::get($config, 'router.base_path', '/'));
        }
    }

}
