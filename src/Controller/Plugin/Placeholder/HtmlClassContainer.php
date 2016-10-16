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

namespace Xloit\Bridge\Zend\Mvc\Controller\Plugin\Placeholder;

use Zend\View\Helper\Placeholder\Container;

/**
 * A {@link HtmlClassContainer} class.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller\Plugin\Placeholder
 */
class HtmlClassContainer extends Container
{
    /**
     *
     *
     * @var string
     */
    const SEPARATOR = ' ';

    /**
     * Constructor to prevent {@link HtmlClassContainer} from being loaded more than once.
     */
    public function __construct()
    {
        parent::__construct();

        $this->separator = self::SEPARATOR;
    }

    /**
     *
     *
     * @return static
     */
    public function reset()
    {
        $this->exchangeArray([]);

        return $this;
    }

    /**
     * Exchange the array for another one.
     *
     * @link  http://php.net/manual/en/arrayobject.exchangearray.php
     *
     * @param mixed $values The new array or object to exchange with the current array.
     *
     * @return array
     */
    public function exchangeArray($values)
    {
        return parent::exchangeArray($this->sanitize($values));
    }

    /**
     * Append a value to the end of the container.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function append($value)
    {
        $values = $this->sanitize($value);

        foreach ($values as $class) {
            parent::append($class);
        }

        return $this;
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
        if (!in_array(
            $separator, [
            ' ',
            '-'
        ], true
        )
        ) {
            $separator = ' ';
        }

        $this->separator = $separator;

        return $this;
    }

    /**
     * Set the indentation string for __toString() serialization, optionally, if a number is passed, it will be the
     * number of spaces.
     *
     * @param  string|int $indent
     *
     * @return static
     */
    public function setIndent($indent)
    {
        $this->indent = null;

        return $this;
    }

    /**
     * Set postfix for __toString() serialization.
     *
     * @param  string $postfix
     *
     * @return static
     */
    public function setPostfix($postfix)
    {
        $this->postfix = (string) $postfix;

        return $this;
    }

    /**
     * Set prefix for __toString() serialization.
     *
     * @param  string $prefix
     *
     * @return static
     */
    public function setPrefix($prefix)
    {
        $this->prefix = null;

        return $this;
    }

    /**
     * Render the placeholder.
     *
     * @param string $indent
     *
     * @return string
     */
    public function toString($indent = null)
    {
        return parent::toString();
    }

    /**
     *
     *
     * @param string|array $values
     *
     * @return array
     */
    protected function sanitize($values)
    {
        $classes = [];

        if (is_string($values)) {
            $values  = preg_replace("/(\r\n?|\n|\s)/", ' ', $values);
            $classes = array_filter(explode(' ', $values));
        } elseif (is_array($values)) {
            foreach ($values as $value) {
                $sanitizedValue = $this->sanitize($value);

                foreach ($sanitizedValue as $className) {
                    $classes[] = $className;
                }
            }
        }

        return array_unique((array) $classes);
    }
}
