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

use ErrorException;
use Xloit\Bridge\Zend\EventManager\Listener\AbstractListenerAggregate;
use Xloit\Bridge\Zend\Log\LoggerAwareInterface;
use Xloit\Bridge\Zend\Log\LoggerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * An {@link ErrorLoggerListener} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Listener
 */
class ErrorLoggerListener extends AbstractListenerAggregate implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Attach one or more listeners.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = -200)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [
                $this,
                'logError'
            ],
            $priority
        );

        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_RENDER_ERROR,
            [
                $this,
                'logError'
            ],
            $priority
        );
    }

    /**
     * Log an error.
     *
     * @param MvcEvent $e
     *
     * @return void
     */
    public function logError(MvcEvent $e)
    {
        /** @var \Exception $exception */
        $exception = $e->getResult()->exception;

        if (!$exception) {
            return;
        }

        $extra = [
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'trace' => $exception->getTrace()
        ];

        if ($exception instanceof ErrorException) {
            /** @var ErrorException $exception */
            $extra['severity'] = $exception->getSeverity();
        }

        $this->logger->err($exception->getMessage(), $extra);
    }
}
