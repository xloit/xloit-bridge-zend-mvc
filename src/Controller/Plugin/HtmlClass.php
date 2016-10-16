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
use Zend\View\Helper\Placeholder\Registry;

/**
 * A {@link HtmlClass} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin
 */
class HtmlClass extends AbstractPlugin
{
    /**
     *
     *
     * @var string
     */
    const SEPARATOR = ' ';

    /**
     * Registry container.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Default attach order.
     *
     * @var string
     */
    protected $defaultAttachOrder = Placeholder\HtmlClassContainer::APPEND;

    /**
     * Constructor to prevent {@link AbstractHtmlClass} from being loaded more than once.
     *
     * @throws \Zend\View\Exception\DomainException
     * @throws \Zend\View\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        $this->registry = new Registry();

        $this->registry->setContainerClass(Placeholder\HtmlClassContainer::class);
    }

    /**
     *
     *
     * @param string $key
     * @param string $value
     * @param string $setType
     *
     * @return static|Placeholder\HtmlClassContainer
     */
    public function __invoke($key = null, $value = null, $setType = null)
    {
        if (null === $setType) {
            $setType = $this->defaultAttachOrder;
        }

        if ($key) {
            if ($value === null) {
                return $this->getContainer($key);
            }

            if ($setType === Placeholder\HtmlClassContainer::SET) {
                $this->set($key, $value);
            } elseif ($setType === Placeholder\HtmlClassContainer::PREPEND) {
                $this->prepend($key, $value);
            } else {
                $this->append($key, $value);
            }
        }

        return $this;
    }

    /**
     * Retrieve a placeholder container.
     *
     * @param  string $key
     *
     * @return Placeholder\HtmlClassContainer
     */
    public function getContainer($key)
    {
        return $this->registry->getContainer($key);
    }

    /**
     * Set html class.
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function set($key, $value)
    {
        $this->getContainer($key)->set($value);

        return $this;
    }

    /**
     * Prepend html class.
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function prepend($key, $value)
    {
        $this->getContainer($key)->prepend($value);

        return $this;
    }

    /**
     * Append html class.
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function append($key, $value)
    {
        $this->getContainer($key)->append($value);

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
     * Set the container for an item in the registry.
     *
     * @param  string                         $key
     * @param  Placeholder\HtmlClassContainer $container
     *
     * @return static
     */
    public function setContainer($key, Placeholder\HtmlClassContainer $container)
    {
        $this->registry->setContainer($key, $container);

        return $this;
    }

    /**
     * Does a particular container exist?
     *
     * @param  string $key
     *
     * @return bool
     */
    public function containerExists($key)
    {
        return $this->registry->containerExists($key);
    }

    /**
     * createContainer.
     *
     * @param  string $key
     * @param  array  $value
     *
     * @return Placeholder\HtmlClassContainer
     */
    public function createContainer($key, array $value = [])
    {
        return $this->registry->createContainer($key, $value);
    }

    /**
     * Delete a container.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function deleteContainer($key)
    {
        return $this->registry->deleteContainer($key);
    }
}
