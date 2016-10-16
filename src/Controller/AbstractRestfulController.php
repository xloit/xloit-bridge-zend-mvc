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

use Xloit\Bridge\Zend\Mvc\Controller\Plugin;
use Xloit\Bridge\Zend\Mvc\Exception;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractRestfulController as ZendAbstractRestfulController;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Mvc\Plugin\Prg\PostRedirectGet;

/**
 * An {@link AbstractRestfulController} abstract class.
 *
 * @abstract
 * @package Xloit\Bridge\Zend\Mvc\Controller
 *
 * @method Plugin\Log|LoggerInterface log()
 * @method Plugin\Title headTitle($value = null, $setType = null)
 * @method Plugin\HtmlClass htmlClass($key = null, $value = null, $setType = null)
 * @method Plugin\Redirect redirect($route = null, $params = [], $options = [], $reuseMatchedParams = false)
 * @method Plugin\Title title($title = null, $setType = 'set')
 * @method Plugin\Url url()
 * @method FlashMessenger flashMessenger()
 * @method PostRedirectGet prg($redirect = null, $redirectToUrl = false)
 */
abstract class AbstractRestfulController extends ZendAbstractRestfulController implements TranslatorAwareInterface
{
    use ControllerTrait;

    /**
     * Execute the request
     *
     * @param MvcEvent $e
     *
     * @return mixed
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Xloit\Bridge\Zend\Mvc\Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->routeMatch = $e->getRouteMatch();

        $preResponse = $this->preDispatch($e);

        /** @var MvcEvent $e */

        if ($preResponse !== null) {
            $e->setResult($preResponse);

            return $preResponse;
        }

        $actionResponse = parent::onDispatch($e);

        $e->setResult($actionResponse);

        $postResponse = $this->postDispatch($e);

        if ($postResponse !== null) {
            /** @var MvcEvent $e */
            $e->setResult($postResponse);
        }

        return $e->getResult();
    }

    /**
     * Pre-request action
     *
     * @param MvcEvent $e
     *
     * @return mixed
     */
    protected function preDispatch(MvcEvent $e)
    {
    }

    /**
     * Post-request action
     *
     * @param MvcEvent $e
     *
     * @return mixed
     */
    protected function postDispatch(MvcEvent $e)
    {
    }
}
