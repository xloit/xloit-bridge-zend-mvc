<?php
/**
 * This source file is part of Virtupeer project.
 *
 * @link      https://virtupeer.com
 * @copyright Copyright (c) 2016, Virtupeer. All rights reserved.
 */

namespace Xloit\Bridge\Zend\Mvc;

use Interop\Container\ContainerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * A {@link Module} class.
 *
 * @package Xloit\Bridge\Zend\Mvc
 */
class Module
{
    /**
     * Return default zend-validator configuration for zend-mvc applications.
     *
     * @return array
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getConfig()
    {
        return [
            'service_manager'    => [
                'aliases'   => [
                    'ModuleRouteListener' => ModuleRouteListener::class
                ],
                'factories' => [
                    ModuleRouteListener::class                   => InvokableFactory::class,
                    Listener\ApplicationBootstrapListener::class => InvokableFactory::class,
                    Listener\ErrorLoggerListener::class          => InvokableFactory::class,
                    Listener\RouteTrailingSlashListener::class   => InvokableFactory::class,
                    Listener\SetupRouterBaseUrl::class           => InvokableFactory::class
                ]
            ],
            'controller_plugins' => [
                'aliases'   => [
                    'flashdata' => Controller\Plugin\FlashData::class,
                    'flashData' => Controller\Plugin\FlashData::class,
                    'FlashData' => Controller\Plugin\FlashData::class,
                    'config'    => Controller\Plugin\Config::class,
                    'Config'    => Controller\Plugin\Config::class,
                    'htmlClass' => Controller\Plugin\HtmlClass::class,
                    'log'       => Controller\Plugin\Log::class,
                    'redirect'  => Controller\Plugin\Redirect::class,
                    'headTitle' => Controller\Plugin\Title::class,
                    'title'     => Controller\Plugin\Title::class,
                    'url'       => Controller\Plugin\Url::class
                ],
                'factories' => [
                    Controller\Plugin\Config::class    => function(ContainerInterface $container) {
                        return new  Controller\Plugin\Config($container->get('Config'));
                    },
                    Controller\Plugin\FlashData::class => InvokableFactory::class,
                    Controller\Plugin\HtmlClass::class => InvokableFactory::class,
                    Controller\Plugin\Log::class       => InvokableFactory::class,
                    Controller\Plugin\Redirect::class  => InvokableFactory::class,
                    Controller\Plugin\Title::class     => InvokableFactory::class,
                    Controller\Plugin\Url::class       => InvokableFactory::class
                ]
            ],
            'listeners'          => [
                ModuleRouteListener::class                 => ModuleRouteListener::class,
                Listener\ErrorLoggerListener::class        => Listener\ErrorLoggerListener::class,
                Listener\RouteTrailingSlashListener::class => Listener\RouteTrailingSlashListener::class,
                Listener\SetupRouterBaseUrl::class         => Listener\RouteTrailingSlashListener::class
            ]
        ];
    }
}
