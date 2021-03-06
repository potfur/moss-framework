<?php

/*
* This file is part of the moss-framework package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Moss\Kernel;

use Moss\Config\ConfigInterface;
use Moss\Container\ContainerInterface;
use Moss\Dispatcher\DispatcherInterface;
use Moss\Http\Request\RequestInterface;
use Moss\Http\Response\ResponseInterface;
use Moss\Http\Router\RouterInterface;
use Moss\Http\Session\SessionInterface;

/**
 * Moss app interface
 *
 * @package Moss Kernel
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface AppInterface
{

    /**
     * Returns parameter or component from container under set name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function &get($name);

    /**
     * Registers route
     *
     * @param string          $name
     * @param string          $pattern
     * @param string|callable $controller
     * @param array           $arguments
     * @param array           $methods
     *
     * @return $this
     */
    public function route($name, $pattern, $controller, array $arguments = [], array $methods = []);

    /**
     * Registers component in container (also variable)
     *
     * @param string $name
     * @param mixed  $definition
     * @param bool   $shared
     *
     * @return $this
     */
    public function component($name, $definition, $shared = false);

    /**
     * Registers event listener
     *
     * @param string   $event
     * @param callable $definition
     *
     * @return $this
     */
    public function listener($event, $definition);

    /**
     * Fires passed event and returns its response or null if no response passed and received
     *
     * @param string      $event
     * @param null|mixed  $subject
     * @param null|string $message
     *
     * @return mixed
     */
    public function fire($event, $subject = null, $message = null);

    /**
     * Returns Container instance
     *
     * @return ContainerInterface
     */
    public function container();

    /**
     * Returns Config instance
     *
     * @return ConfigInterface
     */
    public function config();

    /**
     * Returns Router instance
     *
     * @return RouterInterface
     */
    public function router();

    /**
     * Returns event dispatcher instance
     *
     * @return DispatcherInterface
     */
    public function dispatcher();

    /**
     * Returns request instance
     *
     * @return RequestInterface
     */
    public function request();

    /**
     * Returns session instance
     *
     * @return SessionInterface
     */
    public function session();

    /**
     * Handles request
     *
     * @return ResponseInterface
     */
    public function run();
}
