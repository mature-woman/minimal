<?php

declare(strict_types=1);

namespace mirzaev\minimal\traits;

// Files of the project
use mirzaev\minimal\middleware as instance;

/**
 * Trait of middleware
 *
 * @see https://en.wikipedia.org/wiki/Middleware Middlewares
 *
 * @package mirzaev\minimal\traits
 *
 * @param array $middlewares Stack of middlewares
 *
 * @method self middleware(middleware $middleware) Middleware
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
trait middleware
{
	/**
	 * Middlewares
	 * 
	 * @var array $middlewares The middlewares stack
	 */
	public array $middlewares = [] {
		// Read
		&get => $this->middlewares;
	}

	/**
	 * Middleware
	 *
	 * Write the middleware into the middlewares stack
	 *
	 * @param instance $middleware The middleware
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function middleware(instance $middleware): self
	{
		// Writing into the middlewares stack
		$this->middlewares[] = $middleware;

		// Exit (success)
		return $this;
	}
}
