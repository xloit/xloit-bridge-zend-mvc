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

namespace Xloit\Bridge\Zend\Mvc\Controller\Plugin;

use Xloit\Bridge\Zend\Mvc\Exception;
use Xloit\Bridge\Zend\Uri\Helper\UrlTrait;
use Zend\EventManager\EventInterface;
use Zend\Mvc\Controller\Plugin\Redirect as ZendRedirect;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;

/**
 * A {@link Redirect} class
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 */
class Redirect extends ZendRedirect
{
    use UrlTrait;

    /**
     *
     *
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * Generates a URL based on a route.
     *
     * @param string $route
     * @param array  $params
     * @param array  $options
     * @param bool   $reuseMatchedParams
     *
     * @return \Zend\Http\Response
     * @throws \Xloit\Bridge\Zend\Uri\Exception\RuntimeException
     * @throws Exception\RuntimeException
     * @throws Exception\DomainException
     * @throws \Zend\Mvc\Exception\DomainException
     */
    public function toRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        return parent::toRoute($this->generateRouteUrl($route), $params, $options, $reuseMatchedParams = false);
    }

    /**
     *
     *
     * @param string $defaultRoute
     * @param array  $params
     * @param array  $options
     * @param bool   $reuseMatchedParams
     *
     * @return \Zend\Http\Response
     * @throws \Xloit\Bridge\Zend\Uri\Exception\RuntimeException
     * @throws Exception\DomainException
     * @throws Exception\RuntimeException
     * @throws \Zend\Mvc\Exception\DomainException
     */
    public function refresh($defaultRoute = null, $params = [], $options = [], $reuseMatchedParams = true)
    {
        $controller = $this->getController();

        if (!($controller instanceof InjectApplicationEventInterface)) {
            throw new Exception\DomainException(
                'Url plugin requires a controller that implements InjectApplicationEventInterface'
            );
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var \Zend\Http\Request $request */
        $request = $controller->getRequest();
        /** @var \Zend\Http\Header\Referer $referer */
        $referer = $request->getHeader('Referer');

        if ($referer) {
            $refererPath = $referer->uri()->getPath(); // referer url
            $refererHost = $referer->uri()->getHost(); // referer host
            $host        = $request->getUri()->getHost(); // current host

            // only redirect to previous page if request comes from same host
            if ($refererPath && $refererHost === $host) {
                return $this->toUrl($refererPath);
            }
        }

        if ($defaultRoute === null) {
            return $this->toRoute(null, $params, $options, $reuseMatchedParams);
        }

        return $this->toRoute($defaultRoute, $params, $options, $reuseMatchedParams);
    }

    /**
     * Generates a URL based on a route.
     *
     * @return RouteMatch
     * @throws Exception\RuntimeException
     * @throws Exception\DomainException
     */
    public function getRouteMatch()
    {
        if ($this->routeMatch instanceof RouteMatch) {
            return $this->routeMatch;
        }

        $controller = $this->getController();

        if (!($controller instanceof InjectApplicationEventInterface)) {
            throw new Exception\DomainException(
                'Url plugin requires a controller that implements InjectApplicationEventInterface'
            );
        }

        $event      = $controller->getEvent();
        $routeMatch = null;

        if ($event instanceof MvcEvent) {
            $routeMatch = $event->getRouteMatch();
        } elseif ($event instanceof EventInterface) {
            $routeMatch = $event->getParam('route-match', false);
        }

        if (!($routeMatch instanceof RouteMatch)) {
            throw new Exception\RuntimeException('Url plugin requires event compose a route match');
        }

        return $this->routeMatch;
    }
}
