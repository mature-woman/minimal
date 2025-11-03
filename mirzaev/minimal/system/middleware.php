<?php

declare(strict_types=1);

namespace mirzaev\minimal;

// Files of the project
use mirzaev\minimal\http\request,
	mirzaev\minimal\controller,
	mirzaev\minimal\route;

// Built-in libraries
use Closure as closure,
	LogicException as exception_logic
	;

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
class middleware
{
	/**
	 * Function
	 * 
	 * @var closure|array $function Function
	 */
	public readonly closure|array $function;

	/**
	 * Constructor
	 *
	 * @param closure $function Function
	 *
	 * @return void
	 */
	public function __construct(?closure $function = null)
	{
		if (static::class === self::class) {
			// The middleware class itself

			// Writing the function
			$this->function = $function;
		} else {
			// The middleware inheriting class 

			if (method_exists($this, 'middleware')) {
				// Found the method

				// Writing the function
				$this->function = [$this, 'middleware'];
			} else {
				// Not found the method

				// Exit (fail)
				throw new exception_logic('The middleware method is not initialized', 500);
			}
		}
	}

	/**
	 * Invoke
	 *
	 * @param callable $next
	 * @param controller $controller
	 *
	 * @return string Output
	 */
	public function __invoke(callable $next, controller $controller): string
	{
		// Processing the middleware (entering into recursion)
		return (string) ($this->function)(next: $next, controller: $controller);
	}
}
