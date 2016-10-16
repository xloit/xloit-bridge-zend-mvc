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

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Helper\Placeholder\Container;

/**
 * An {@link AbstractStringConcatPlugin} abstract class.
 *
 * @abstract
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 */
abstract class AbstractStringConcatPlugin extends AbstractPlugin
{
    /**
     *
     *
     * @var string
     */
    const SEPARATOR = ' | ';

    /**
     * Placeholder container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Default attach order.
     *
     * @var string
     */
    protected $defaultAttachOrder = Container::SET;

    /**
     * Constructor to prevent {@link Title} from being loaded more than once.
     *
     * @param string $separator
     */
    public function __construct($separator = self::SEPARATOR)
    {
        $this->container = new Container();

        $this->setSeparator($separator);
    }

    /**
     * Set Separator.
     *
     * @param string $separator
     *
     * @return static
     */
    public function setSeparator($separator)
    {
        $this->container->setSeparator($separator);

        return $this;
    }

    /**
     *
     * @param string $value
     * @param string $setType
     *
     * @return static
     */
    public function __invoke($value = null, $setType = null)
    {
        if (null === $setType) {
            $setType = $this->getDefaultAttachOrder();
        }

        if ($value) {
            if ($setType === Container::SET) {
                $this->set($value);
            } elseif ($setType === Container::PREPEND) {
                $this->prepend($value);
            } else {
                $this->append($value);
            }
        }

        return $this;
    }

    /**
     *
     *
     * @return string
     */
    public function getDefaultAttachOrder()
    {
        return $this->defaultAttachOrder;
    }

    /**
     * Set context.
     *
     * @param string $value
     *
     * @return static
     */
    public function set($value)
    {
        $this->container->set((string) $value);

        return $this;
    }

    /**
     * Prepend data.
     *
     * @param string $value
     *
     * @return static
     */
    public function prepend($value)
    {
        $this->container->prepend((string) $value);

        return $this;
    }

    /**
     * Append data.
     *
     * @param string $value
     *
     * @return static
     */
    public function append($value)
    {
        $this->container->append((string) $value);

        return $this;
    }

    /**
     *
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get data as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Get value data.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->container->toString();
    }
}
