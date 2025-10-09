<?php

declare(strict_types=1);

namespace mirzaev\minimal;

// Files of the project
use mirzaev\minimal\http\request,
	mirzaev\minimal\route;

// Built-in libraries
use Closure as closure;

/**
 * Middleware
 *
 * @see https://en.wikipedia.org/wiki/Middleware Middlewares
 *
 * @package mirzaev\minimal
 *
 * @param closure $function Function
 *
 * @method void __construct(closure $function) Constructor
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class middleware
{
	/**
	 * Function
	 * 
	 * @var closure $function Function
	 */
	 public readonly closure $function;

	/**
	 * Constructor
	 *
	 * @param closure $function Function
	 *
	 * @return void
	 */
	public function __construct(closure $function)
	{
		// Writing the function
		$this->function = $function;
	}

	/**
	 * Invoke
	 *
	 * @param callable $next
	 *
	 * @return string Output
	 */
	 public function __invoke(callable $next): string
	 {
		 // Processing the middleware (entering into recursion)
		
		 return (string) ($this->function)(next: $next);
	 }
}
