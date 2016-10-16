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

/**
 * A {@link Config} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 */
class Config extends AbstractPlugin
{
    /**
     *
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor to prevent {@link Config} from being loaded more than once.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     *
     *
     * @param string $path
     * @param mixed  $default
     *
     * @return mixed
     * @throws \Xloit\Std\Exception\RuntimeException
     */
    public function __invoke($path, $default = null)
    {
        return ArrayUtils::get($this->config, $path, $default);
    }
}
