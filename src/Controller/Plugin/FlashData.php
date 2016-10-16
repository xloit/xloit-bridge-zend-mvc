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

use Xloit\Std\ArrayUtils;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container as SessionContainer;

/**
 * A {@link FlashData} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 */
class FlashData extends AbstractPlugin
{
    /**
     *
     *
     * @var SessionContainer[]
     */
    protected $sessions = [];

    /**
     *
     *
     * @var string
     */
    protected $name = 'fd';

    /**
     * Perform Flash data based
     *
     * @param array        $data
     * @param string|array $routeName
     *
     * @return static
     * @throws \Zend\Session\Exception\InvalidArgumentException
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function __invoke($data = null, $routeName = null)
    {
        if ($data) {
            if (is_scalar($routeName) && (null !== $routeName)) {
                $routeName = (array) $routeName;
            }

            if (is_array($routeName)) {
                $routeName = (array) $routeName;

                foreach ($routeName as $name) {
                    $this->storeData($data, $name);
                }
            } else {
                $this->storeData($data);
            }
        }

        return $this;
    }

    /**
     * Returns the value of Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of Name.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     *
     *
     * @param array        $data
     * @param string|array $routeName
     *
     * @return SessionContainer
     * @throws \Zend\Session\Exception\InvalidArgumentException
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function storeData($data, $routeName = null)
    {
        $container = $this->getSessionForRoute($routeName);

        /** @var \Zend\Mvc\Controller\AbstractController $controller */
        $controller = $this->getController();
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request    = $controller->getEvent()->getRequest();
        $routeMatch = $controller->getEvent()->getRouteMatch();

        /** @noinspection PhpUndefinedFieldInspection */
        $container->data = ArrayUtils::iteratorToArray($data);
        /** @noinspection PhpUndefinedFieldInspection */
        $container->source = [
            'routeParams'   => $routeMatch->getParams(),
            'routeName'     => $routeMatch->getMatchedRouteName(),
            'requestMethod' => $request->getMethod(),
            'server'        => ArrayUtils::iteratorToArray($request->getServer()),
            'requestQuery'  => ArrayUtils::iteratorToArray($request->getQuery()),
            'requestPost'   => ArrayUtils::iteratorToArray($request->getPost())
        ];

        $container->setExpirationHops(
            1,
            [
                'data',
                'source'
            ]
        )->setExpirationSeconds(300); // 5 minutes

        return $container;
    }

    /**
     * Indicates whether current data from the specified route name
     *
     * @param string $routeName
     *
     * @return bool
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function isFromRoute($routeName)
    {
        $session = $this->getSession();

        /** @noinspection PhpUndefinedFieldInspection */
        if ($session->data === null || !is_array($session->source)) {
            return null;
        }

        /** @noinspection PhpUndefinedFieldInspection */
        return $session->source['routeName'] === $routeName;
    }

    /**
     * Return the current route data
     *
     * @return array
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function getData()
    {
        $session = $this->getSession();

        /** @noinspection PhpUndefinedFieldInspection */
        return $session->data;
    }

    /**
     * Indicates whether current route has data
     *
     * @return bool
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function hasData()
    {
        $session = $this->getSession();

        /** @noinspection PhpUndefinedFieldInspection */
        return $session->data !== null;
    }

    /**
     * Remove current route data
     *
     * @return bool
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function removeData()
    {
        $session = $this->getSession();

        /** @noinspection PhpUndefinedFieldInspection */
        if ($session->data !== null) {
            unset($session->data);

            return true;
        }

        return false;
    }

    /**
     * Returns the current route session container.
     *
     * @return SessionContainer
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function getSession()
    {
        return $this->getSessionForRoute();
    }

    /**
     *
     *
     * @param string $routeName
     *
     * @return SessionContainer
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function getSessionForRoute($routeName = null)
    {
        if (!$routeName) {
            /** @var \Zend\Mvc\Controller\AbstractController $controller */
            $controller = $this->getController();
            $routeMatch = $controller->getEvent()->getRouteMatch();
            $routeName  = $routeMatch->getMatchedRouteName();
        }

        if (array_key_exists($routeName, $this->sessions)) {
            return $this->sessions[$routeName];
        }

        $this->sessions[$routeName] = new SessionContainer(
            $this->getName() . '_' . strtolower(
                str_replace(
                    [
                        '\\',
                        '/'
                    ], '_', $routeName
                )
            )
        );

        return $this->sessions[$routeName];
    }
}
