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

namespace Xloit\Bridge\Zend\Mvc\Controller;

use Zend\I18n\Translator\TranslatorAwareTrait;

/**
 * A {@link ControllerTrait} trait.
 *
 * @package Xloit\Bridge\Zend\Mvc\Controller
 */
trait ControllerTrait
{
    use TranslatorAwareTrait;

    /**
     *
     *
     * @var \Zend\Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * Translate a message.
     *
     * @param string $message
     * @param string $textDomain
     * @param string $locale
     *
     * @return string
     */
    public function __($message, $textDomain = 'default', $locale = null)
    {
        return $this->getTranslator()->translate($message, $textDomain, $locale);
    }

    /**
     *
     *
     * @param string $constraint
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getRouteParameter($constraint, $default = null)
    {
        return $this->getRouteMatch()->getParam($constraint, $default);
    }

    /**
     * Return matched route.
     *
     * @return \Zend\Router\RouteMatch
     */
    public function getRouteMatch()
    {
        if (null === $this->routeMatch) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->routeMatch = $this->getEvent()->getRouteMatch();
        }

        return $this->routeMatch;
    }

    /**
     *
     *
     * @param string $constraint
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getQueryParameter($constraint, $default = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->getRequest()->getQuery()->get($constraint, $default);
    }
}
