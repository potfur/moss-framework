<?php
namespace Moss\kernel;

use \Moss\container\ContainerInterface;
use \Moss\router\RouterInterface;
use \Moss\dispatcher\DispatcherInterface;
use \Moss\http\request\RequestInterface;
use \Moss\http\response\ResponseInterface;
use \Moss\kernel\KernelException;
use \Moss\router\RouterException;
use \Moss\security\SecurityException;

/**
 * Moss Kernel
 *
 * @package Moss Kernel
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Kernel {

	/** @var RouterInterface */
	protected $Router;

	/** @var ContainerInterface */
	protected $Container;

	/** @var DispatcherInterface */
	protected $Dispatcher;

	/**
	 * Constructor
	 *
	 * @param RouterInterface     $Router
	 * @param ContainerInterface  $Container
	 * @param DispatcherInterface $Dispatcher
	 */
	public function __construct(RouterInterface $Router, ContainerInterface $Container, DispatcherInterface $Dispatcher) {
		$this->Router = & $Router;
		$this->Container = & $Container;
		$this->Dispatcher = & $Dispatcher;
	}

	/**
	 * Handles request
	 *
	 * @param RequestInterface $Request
	 *
	 * @return ResponseInterface
	 * @throws \Exception|KernelException
	 */
	public function handle(RequestInterface $Request) {
		try {
			if($Response = $this->fireEvent('kernel.request')) {
				return $this->fireEvent('kernel.send', $Response);
			}

			$action = $this->Router->match($Request);

			if($Response = $this->fireEvent('kernel.route')) {
				return $this->fireEvent('kernel.send', $Response);
			}

			if($Response = $this->fireEvent('kernel.access')) {
				return $this->fireEvent('kernel.send', $Response);
			}

			if($Response = $this->fireEvent('kernel.controller')) {
				return $this->fireEvent('kernel.send', $Response);
			}

			$Response = $this->callController($action);

			if(!$Response || !$Response instanceof ResponseInterface) {
				throw new KernelException(sprintf('There was no response returned from the "%s (%s)" or is not an instance of ResponseInterface', $action, $Request->url()));
			}

			$Response = $this->fireEvent('kernel.response', $Response);

			return $this->fireEvent('kernel.send', $Response);
		}
		catch(SecurityException $e) {
			$Response = $this->fireEvent('kernel.403', null, sprintf('%s (%s line:%s)', $e->getMessage(), $e->getFile(), $e->getLine()));
		}
		catch(RouterException $e) {
			$Response = $this->fireEvent('kernel.404', null, sprintf('%s (%s line:%s)', $e->getMessage(), $e->getFile(), $e->getLine()));
		}
		catch(\Exception $e) {
			$Response = $this->fireEvent('kernel.500', null, sprintf('%s (%s line:%s)', $e->getMessage(), $e->getFile(), $e->getLine()));
		}

		if(!$Response instanceof ResponseInterface) {
			throw new KernelException(sprintf('Received response is not an instance of ResponseInterface', $Request->url()));
		}

		if(!empty($e) && empty($Request)) {
			throw $e;
		}

		return $this->fireEvent('kernel.send', $Response);
	}

	/**
	 * Fires passed event and returns its response or null if no response passed and received
	 *
	 * @param string $event
	 * @param null   $Response
	 * @param null   $message
	 *
	 * @return ResponseInterface|null
	 */
	protected function fireEvent($event, $Response = null, $message = null) {
		$EventResponse = $this->Dispatcher->fire($event, $Response, $message);
		if($EventResponse instanceof ResponseInterface) {
			return $EventResponse;
		}

		return $Response;
	}

	/**
	 * Calls controller from callable or class
	 *
	 * @param string|callable $controller
	 *
	 * @return mixed
	 * @throws KernelException
	 * @throws \LogicException
	 */
	protected function callController($controller) {
		if(is_callable($controller)) {
			return $controller($this->Container);
		}

		if(is_string($controller)) {
			if(substr_count($controller, ':') < 2) {
				throw new KernelException(sprintf('Invalid controller identifier "%s". Controller identifier should have at least two ":".', $controller));
			}

			preg_match_all('/^(?P<controller>.+):(?P<action>[0-9a-z_]+)$/i', $controller, $matches, PREG_SET_ORDER);

			if(empty($matches[0]['controller']) || ($modPos = strpos($matches[0]['controller'], ':')) === false) {
				throw new KernelException(sprintf('Invalid or missing controller identifier "%s"', $controller));
			}

			$controller = sprintf('\\%s\\controller\\%sController', substr($matches[0]['controller'], 0, $modPos), str_replace(':', '\\', substr($matches[0]['controller'], $modPos + 1)));

			if(!class_exists($controller)) {
				throw new KernelException(sprintf('Unable to load controller class "%s"', $controller));
			}

			$Controller = new $controller($this->Container);

			if(empty($matches[0]['action'])) {
				throw new KernelException(sprintf('Invalid or missing action name in controller identifier "%s"', $controller));
			}

			$action = $matches[0]['action'];

			if(!method_exists($Controller, $action) || !is_callable(array($Controller, $action))) {
				throw new KernelException(sprintf('Unable to call action "%s" on controller "%s"', $controller, $action));
			}

			return $Controller->$action();
		}

		throw new KernelException('Unable to resolve controller "%s"');
	}
}