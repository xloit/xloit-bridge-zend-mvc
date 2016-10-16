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

use Exception;
use Throwable;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application as ZendApplication;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;
use Zend\Stdlib\ResponseInterface;

/**
 * A {@link Application} class.
 *
 * @package Xloit\Bridge\Zend\Mvc
 */
class Application extends ZendApplication
{
    /**
     * Application events triggered before bootstrap.
     *
     * @var string
     */
    const EVENT_PRE_BOOTSTRAP = 'prebootstrap';

    /**
     * Application events triggered after bootstrap.
     *
     * @var string
     */
    const EVENT_POST_BOOTSTRAP = 'postbootstrap';

    /**
     * Default application name.
     *
     * @var string
     */
    const NAME = 'Xloit';

    /**
     * Config key to be used to grab config by the current context.
     *
     * @var string
     */
    const CONFIG_KEY_CONTEXT = 'XloitApplication::Context';

    /**
     * Config key to be used to grab config by the current environment.
     *
     * @var string
     */
    const CONFIG_KEY_ENV = 'XloitApplication::Environment';

    /**#@+
     * Possible application environments.
     *
     * @var string
     */
    const ENV_DEVELOPMENT = 'development';

    const ENV_PRODUCTION  = 'production';

    const ENV_STAGING     = 'staging';

    const ENV_TESTING     = 'testing';

    /**#@-*/

    /**#@+
     * Possible application contexts.
     *
     * @var string
     */
    const CONTEXT_DEFAULT = 'application';

    const CONTEXT_CONSOLE = 'console';

    const CONTEXT_API     = 'api';

    const CONTEXT_BACKEND = 'backend';

    const CONTEXT_SETUP   = 'setup';

    /**#@-*/

    /**
     * Holds the current environment.
     *
     * @var string
     */
    protected $environment = self::ENV_PRODUCTION;

    /**
     * Holds the current application context.
     *
     * @var string
     */
    protected $context = self::CONTEXT_DEFAULT;

    /**
     * Holds the current valid application context.
     *
     * @var array
     */
    protected $applicationContext = [
        self::CONTEXT_DEFAULT => [self::CONTEXT_DEFAULT],
        self::CONTEXT_CONSOLE => [self::CONTEXT_CONSOLE],
        self::CONTEXT_API     => [self::CONTEXT_API],
        self::CONTEXT_SETUP   => [self::CONTEXT_SETUP]
    ];

    /**
     * Pre bootstrap application event listeners will triggered first before any listeners.
     *
     * @var array
     */
    protected $preBootstrapListeners = [
        Listener\ApplicationBootstrapListener::class => Listener\ApplicationBootstrapListener::class
    ];

    /**
     * Returns the value of application context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the value of application context.
     *
     * @param string $context
     *
     * @return static
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Returns the value of application environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Sets the value of application environment.
     *
     * @param string $environment
     *
     * @return static
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Indicates whether the current environment is development.
     *
     * @return bool
     */
    public function isDevelopment()
    {
        return $this->environment !== static::ENV_PRODUCTION;
    }

    /**
     * Indicates whether the current environment is production.
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->environment === static::ENV_PRODUCTION;
    }

    /**
     * Indicates whether the current context is standard mode.
     *
     * @return bool
     */
    public function isDefaultContext()
    {
        return $this->context !== static::CONTEXT_DEFAULT;
    }

    /**
     * Indicates whether the current context is api mode.
     *
     * @return bool
     */
    public function isApiContext()
    {
        return $this->context !== static::CONTEXT_API;
    }

    /**
     * Indicates whether the current context is console mode.
     *
     * @return bool
     */
    public function isConsoleContext()
    {
        return $this->context !== static::CONTEXT_CONSOLE;
    }

    /**
     * Indicates whether the current context is setup mode.
     *
     * @return bool
     */
    public function isSetupContext()
    {
        return $this->context !== static::CONTEXT_SETUP;
    }

    /**
     * Returns the current valid application context.
     *
     * @return array
     */
    public function getApplicationContext()
    {
        return $this->applicationContext;
    }

    /**
     * Bootstrap the application.
     *
     * Defines and binds the MvcEvent, and passes it the request, response, and router. Attaches the ViewManager as
     * a listener. Triggers the bootstrap event.
     *
     * @param array $listeners List of listeners to attach.
     *
     * @return static
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function bootstrap(array $listeners = [])
    {
        /** @var \Zend\ServiceManager\ServiceManager $serviceManager */
        $serviceManager = $this->serviceManager;
        $config         = $serviceManager->get('Config');

        if (array_key_exists('Application::Context', $config)) {
            $this->applicationContext = $config['Application::Context'];
        }

        $router             = $serviceManager->get('Router');
        $applicationContext = $this->applicationContext;
        $context            = $this->getContext();
        $routeMatch         = $router->match($this->request);
        $this->event        = $event = new MvcEvent(); // Setup MVC Event
        /** @var \Zend\EventManager\EventManager $events */
        $events = $this->events;

        if ($routeMatch instanceof RouteMatch) {
            $event->setRouteMatch($routeMatch);

            $matchedRouteName = $routeMatch->getMatchedRouteName();

            /** @var array $contextValues */
            foreach ($applicationContext as $name => $contextValues) {
                foreach ($contextValues as $value) {
                    if ($value === $matchedRouteName) {
                        $context = $name;

                        break;
                    } elseif (fnmatch($value, $matchedRouteName)) {
                        $context = $name;

                        break;
                    } elseif (fnmatch($value . '*', $matchedRouteName)) {
                        $context = $name;

                        break;
                    }
                }

                if ($matchedRouteName === $name) {
                    $context = $name;

                    break;
                }
            }
        }

        $this->setContext($context);

        // Setup pre bootstrap listeners
        $preListeners = $this->preBootstrapListeners;

        foreach ($preListeners as $listener) {
            $serviceManager->get($listener)->attach($events);
        }

        $event->setName(self::EVENT_PRE_BOOTSTRAP);
        $event->setTarget($this);
        $event->setApplication($this);
        $event->setRequest($this->request);
        $event->setResponse($this->response);
        /** @noinspection PhpParamsInspection */
        $event->setRouter($serviceManager->get('Router'));

        // Trigger bootstrap events
        $events->triggerEvent($event);

        // Setup default listeners
        $listeners = array_unique(array_merge($this->defaultListeners, $listeners));

        foreach ($listeners as $listener) {
            $serviceManager->get($listener)->attach($events);
        }

        $event->setName(MvcEvent::EVENT_BOOTSTRAP);

        // Trigger bootstrap events
        $events->triggerEvent($event);

        return $this;
    }

    /**
     * Run the application.
     * This method overrides the behavior of {@link Zend\Mvc\Application} to wrap the trigger of the route event in
     * a try/catch block, allowing us to catch route listener exceptions and trigger the dispatch.error event.
     *
     * @return static
     */
    public function run()
    {
        $events = $this->events;
        $event  = $this->event;

        // Define callback used to determine whether or not to short-circuit
        $shortCircuit = function($r) use ($event) {
            if ($r instanceof ResponseInterface) {
                return true;
            }
            if ($event->getError()) {
                return true;
            }

            return false;
        };

        // Prepare route event
        $event->setName(MvcEvent::EVENT_ROUTE);
        $event->stopPropagation(false); // Clear before triggering

        // Trigger route event
        try {
            $result = $events->triggerEventUntil($shortCircuit, $event);
        } catch (Throwable $e) {
            return $this->handleException($e, $event, $events);
        } catch (Exception $e) {
            return $this->handleException($e, $event, $events);
        }

        if ($result->stopped()) {
            $response = $result->last();

            if ($response instanceof ResponseInterface) {
                return $this->completeResponse($events, $event, $response);
            }
        }

        if ($event->getError()) {
            return $this->completeRequest($event);
        }

        // Prepare dispatch event
        $event->setName(MvcEvent::EVENT_DISPATCH);
        $event->stopPropagation(false); // Clear before triggering

        // Trigger dispatch event
        $result = $events->triggerEventUntil($shortCircuit, $event);

        // Complete response
        $response = $result->last();

        if ($response instanceof ResponseInterface) {
            return $this->completeResponse($events, $event, $response);
        }

        $response = $this->response;

        $event->setResponse($response);

        return $this->completeRequest($event);
    }

    /**
     * Handle an exception/throwable.
     *
     * @param Throwable|Exception   $exception
     * @param MvcEvent              $event
     * @param EventManagerInterface $events
     *
     * @return static
     */
    protected function handleException($exception, MvcEvent $event, EventManagerInterface $events)
    {
        // Prepare error event
        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $event->setError(static::ERROR_EXCEPTION);
        $event->setParam('exception', $exception);

        // Trigger route event
        $result   = $events->triggerEvent($event);
        $response = $result->last();

        if ($response instanceof ResponseInterface) {
            return $this->completeResponse($events, $event, $response);
        }

        return $this->completeRequest($event);
    }

    /**
     * Complete the request response.
     * Triggers "finish" events, and returns current application instance.
     *
     * @param EventManagerInterface $events
     * @param MvcEvent              $event
     * @param ResponseInterface     $response
     *
     * @return static
     */
    protected function completeResponse(EventManagerInterface $events, MvcEvent $event, ResponseInterface $response)
    {
        $event->setName(MvcEvent::EVENT_FINISH);
        $event->setTarget($this);
        $event->setResponse($response);
        $event->stopPropagation(false); // Clear before triggering

        $events->triggerEvent($event);

        $this->response = $response;

        return $this;
    }
}
