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
use Zend\EventManager\EventManagerInterface;
use Zend\Filter;
use Zend\Http\PhpEnvironment;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Response;

/**
 * A {@link RouteTrailingSlashListener} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Listener
 */
class RouteTrailingSlashListener extends AbstractListenerAggregate
{
    /**
     * FilterInterface/inflector used to normalize names for use as template identifiers.
     *
     * @var Filter\FilterChain
     */
    protected $inflector;

    /**
     * Attach one or more listeners.
     * Implementors may add an optional $priority argument; the EventManager implementation will pass this to the
     * aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1000)
    {
        /** @noinspection TypeUnsafeComparisonInspection */
        if (PHP_SAPI == 'cli') {
            return;
        }

        $this->listeners[] = $events->getSharedManager()->attach(
            Application::class,
            MvcEvent::EVENT_ROUTE,
            [
                $this,
                'onRouteRemoveTrailingSlash'
            ],
            $priority
        );

        $this->listeners[] = $events->getSharedManager()->attach(
            Application::class,
            MvcEvent::EVENT_ROUTE,
            [
                $this,
                'onRouteNormalizeParameters'
            ],
            -$priority
        );
    }

    /**
     *
     *
     * @param MvcEvent $event
     *
     * @return null|Response
     * @throws \Zend\Http\Exception\InvalidArgumentException
     */
    public function onRouteRemoveTrailingSlash(MvcEvent $event)
    {
        /** @var $var Application */
        $application = $event->getApplication();
        /** @var $request PhpEnvironment\Request */
        $request    = $application->getRequest();
        $uri        = $request->getUri();
        $basePath   = $request->getBasePath();
        $scriptName = $request->getServer('SCRIPT_NAME');
        $path       = $uri->getPath();

        // Try to figure out (base) path relative to front controller
        if (strpos($path, $scriptName) !== false) {
            // Handle the case where the script name is included in the path.
            // For example: http://example.org/index.php/some/route
            $path = substr($path, strlen($scriptName));
        } else {
            $path = substr($path, strlen($basePath));
        }

        $isRoot           = ($path === '/');
        $hasTrailingSlash = substr($path, -1) === '/';

        if (!$isRoot && $hasTrailingSlash) {
            $uri->setPath(substr($uri->getPath(), 0, -1));

            /** @var PhpEnvironment\Response $response */
            $response = $application->getResponse();
            $response->setStatusCode(301);
            $response->getHeaders()->addHeaderLine('Location', $uri);

            return $response;
        }

        return null;
    }

    /**
     *
     *
     * @return Filter\FilterChain
     * @throws Filter\Exception\InvalidArgumentException
     */
    protected function getInflector()
    {
        $inflector = $this->inflector;

        if (null === $inflector) {
            $inflector = new Filter\FilterChain();

            $inflector->attach(new Filter\Word\DashToCamelCase());
            $inflector->attach(new Filter\Word\DashToCamelCase('_'));

            $this->inflector = $inflector;
        }

        return $inflector;
    }

    /**
     *
     *
     * @param MvcEvent $event
     *
     * @return void
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws Filter\Exception\InvalidArgumentException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function onRouteNormalizeParameters(MvcEvent $event)
    {
        /** @var $var Application */
        $application = $event->getApplication();
        $routeMatch  = $event->getRouteMatch();
        $namespace   = $routeMatch->getParam('__NAMESPACE__');

        if (!$namespace) {
            $namespace = $routeMatch->getParam('namespace', 'application');
        }

        $controller        = $routeMatch->getParam('controller', 'not-found');
        $controllerClass   = $this->deriveClassNamespace($namespace, $controller);
        $controllerManager = $application->getServiceManager()->get('ControllerManager');

        if ($controllerManager->has($controllerClass)) {
            $namespace  = substr($controllerClass, 0, strrpos($controllerClass, '\\'));
            $controller = $controllerClass;
        }

        $routeMatch->setParam('__NAMESPACE__', $namespace);
        $routeMatch->setParam('namespace', $namespace);
        $routeMatch->setParam('controller', $controller);
    }

    /**
     *
     *
     * @param string $namespace
     *
     * @return string
     * @throws Filter\Exception\InvalidArgumentException
     */
    protected function filterClassNamespace($namespace)
    {
        return $this->getInflector()->filter(trim($namespace, '\\'));
    }

    /**
     *
     *
     * @param string $module
     * @param string $controller
     *
     * @return string
     * @throws Filter\Exception\InvalidArgumentException
     */
    protected function deriveClassNamespace($module, $controller)
    {
        $module     = $this->filterClassNamespace($module);
        $controller = $this->filterClassNamespace($controller);
        $className  = $module . '\\' . $controller;

        if (strpos($module, '\\Controller') === false && strpos($controller, 'Controller\\') === false) {
            $className = $module . '\\Controller\\' . $controller;
        }

        return preg_replace('/(\\\)+/', '\\', $className);
    }
}
