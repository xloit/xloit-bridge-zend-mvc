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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
                    'config'    => Controller\Plugin\Config::class,
                    'Config'    => Controller\Plugin\Config::class,
                    'flashdata' => Controller\Plugin\FlashData::class,
                    'flashData' => Controller\Plugin\FlashData::class,
                    'FlashData' => Controller\Plugin\FlashData::class,
                    'htmlclass' => Controller\Plugin\HtmlClass::class,
                    'htmlClass' => Controller\Plugin\HtmlClass::class,
                    'HtmlClass' => Controller\Plugin\HtmlClass::class,
                    'log'       => Controller\Plugin\Log::class,
                    'Log'       => Controller\Plugin\Log::class,
                    'redirect'  => Controller\Plugin\Redirect::class,
                    'Redirect'  => Controller\Plugin\Redirect::class,
                    'headtitle' => Controller\Plugin\Title::class,
                    'headTitle' => Controller\Plugin\Title::class,
                    'HeadTitle' => Controller\Plugin\Title::class,
                    'title'     => Controller\Plugin\Title::class,
                    'Title'     => Controller\Plugin\Title::class,
                    'url'       => Controller\Plugin\Url::class,
                    'Url'       => Controller\Plugin\Url::class
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
