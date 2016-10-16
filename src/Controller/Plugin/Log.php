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
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * A {@link Log} class is a controller plugin simply proxies to a Logger.
 *
 * @see     Log::__call()
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 *
 * @method LoggerInterface emerg($message, array $extra = [])
 * @method LoggerInterface alert($message, array $extra = [])
 * @method LoggerInterface crit($message, array $extra = [])
 * @method LoggerInterface err($message, array $extra = [])
 * @method LoggerInterface warn($message, array $extra = [])
 * @method LoggerInterface notice($message, array $extra = [])
 * @method LoggerInterface info($message, array $extra = [])
 * @method LoggerInterface debug($message, array $extra = [])
 */
class Log extends AbstractPlugin implements LoggerAwareInterface
{
    /**
     * Logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Proxy to the registered logger.
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return mixed
     * @throws Exception\BadMethodCallException
     * @throws Exception\RuntimeException
     */
    public function __call($method, $args)
    {
        if (!$this->hasLogger()) {
            throw new Exception\RuntimeException('No logger has been set');
        }

        $logger = $this->getLogger();

        if (method_exists($logger, $method)) {
            return call_user_func_array(
                [
                    $logger,
                    $method
                ],
                $args
            );
        }

        throw new Exception\BadMethodCallException(sprintf('Method "%s" does not exist', $method));
    }

    /**
     * Set Logger.
     *
     * @param LoggerInterface $logger
     *
     * @return static
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Return logger.
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Whether we've got a logger.
     *
     * @return bool
     */
    public function hasLogger()
    {
        return $this->logger instanceof LoggerInterface;
    }
}
