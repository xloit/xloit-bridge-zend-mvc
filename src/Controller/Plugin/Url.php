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

use Traversable;
use Xloit\Bridge\Zend\Mvc\Exception;
use Xloit\Bridge\Zend\Uri\Helper\UrlTrait;
use Zend\EventManager\EventInterface;
use Zend\Mvc\Controller\Plugin\Url as ZendUrl;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;

/**
 * An {@link Url} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 */
class Url extends ZendUrl
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
     * @param string            $route
     * @param array|Traversable $params
     * @param array|bool        $options
     * @param bool              $reuseMatchedParams
     *
     * @return string
     * @throws \Xloit\Bridge\Zend\Uri\Exception\RuntimeException
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Zend\Mvc\Exception\InvalidArgumentException
     * @throws \Zend\Mvc\Exception\RuntimeException
     */
    public function fromRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        return parent::fromRoute($this->generateRouteUrl($route), $params, $options, $reuseMatchedParams);
    }

    /**
     * Generates a URL based on a route.
     *
     * @return RouteMatch
     * @throws \Xloit\Bridge\Zend\Mvc\Exception\DomainException
     * @throws \Xloit\Bridge\Zend\Mvc\Exception\RuntimeException
     */
    public function getRouteMatch()
    {
        if ($this->routeMatch instanceof RouteMatch) {
            return $this->routeMatch;
        }

        $controller = $this->getController();

        if (!$controller instanceof InjectApplicationEventInterface) {
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
