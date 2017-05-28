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

use Interop\Container\ContainerInterface;
use Xloit\Bridge\Zend\Mvc\Application;
use Xloit\Bridge\Zend\ServiceManager\AbstractFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * An {@link ApplicationFactory} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Service
 */
class ApplicationFactory extends AbstractFactory
{
    /**
     * Create the Application service (v3).
     * Creates a {@link Application} service, passing it the configuration service and the service manager instance.
     *
     * @param ContainerInterface $container
     * @param string             $name
     * @param null|array         $options
     *
     * @return Application
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        /** @var ServiceManager $container */
        $application       = new Application(
            $container,
            $container->get('EventManager'),
            $container->get('Request'),
            $container->get('Response')
        );
        $validEnvironments = [
            Application::ENV_DEVELOPMENT,
            Application::ENV_PRODUCTION,
            Application::ENV_STAGING,
            Application::ENV_TESTING
        ];
        $environment       = null;

        if (defined('APPLICATION_ENV')) {
            $environment = APPLICATION_ENV;
        } elseif (defined('ENVIRONMENT')) {
            $environment = ENVIRONMENT;
        } elseif (defined('ENV')) {
            $environment = ENV;
        }

        if (!$environment || !in_array($environment, $validEnvironments, true)) {
            $environment = Application::ENV_PRODUCTION;
        }

        $application->setEnvironment($environment);

        return $application;
    }
}
