<?php

declare(strict_types=1);

namespace mirzaev\minimal;

// Files of the project
use mirzaev\minimal\controller,
	mirzaev\minimal\middleware,
	mirzaev\minimal\traits\middleware as middleware_trait;

/**
 * Route
 *
 * @package mirzaev\minimal
 *
 * @param string|controller $controller Name of the controller
 * @param string $method Name of the method of the method of $this->controller
 * @param string|model $model Name of the model
 * @param array $parameters Arguments for the $this->method (will be concatenated together with generated request parameters)
 * @param array $options Options for `request_parse_body($options)`
 * @param array $middlewares Stack of middlewares
 *
 * @method void __construct(string|controller $controller, ?string $method, string|model|null $model, array $parameters, array $options, array $middlewares) Constructor
 * @method self middleware(middleware $middleware) Middleware
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class route
{
	use middleware_trait;

	/**
	 * Controller
	 * 
	 * @var string|controller $controller Name of the controller or an instance of the controller
	 */
	public string|controller $controller {
		// Read
		get => $this->controller;
	}

	/**
	 * Method
	 * 
	 * @var string $method Name of the method of the method of $this->controller
	 */
	public string $method {
		// Read
		get => $this->method;
	}

	/**
	 * Model
	 * 
	 * @var string|model|null $model Name of the model of an instance of the model
	 */
	public string|model|null $model {
		// Read
		get => $this->model;
	}

	/**
	 * Parameters
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 * 
	 * @var array $parameters Arguments for the $this->method (will be concatenated together with generated request parameters)
	 */
	public array $parameters = [] {
		// Read
		&get => $this->parameters;
	}

	/**
	 * Options
	 *
	 * Required if $this->method !== method::post
	 *
	 * @see https://wiki.php.net/rfc/rfc1867-non-post About request_parse_body()
	 * @see https://wiki.php.net/rfc/property-hooks Hooks (find a table about backed and virtual hooks)
	 *
	 * @throws exception_runtime if reinitialize the property
	 * 
	 * @var array $options Options for `request_parse_body($options)`
	 */
	public array $options {
		// Write
		set(array $value) {
			if (isset($this->{__PROPERTY__})) {
				// The property is already initialized

				// Exit (fail)
				throw new exception_runtime('The property is already initialized: ' . __PROPERTY__, status::internal_server_error->value);
			}

			// Writing
			$this->options = array_filter(
				$value,
				fn(string $key) => match ($key) {
					'post_max_size',
					'max_input_vars',
					'max_multipart_body_parts',
					'max_file_uploads',
					'upload_max_filesize' => true,
					default => throw new exception_domain("Failed to recognize option: $key", status::internal_server_error->value)
				},
				ARRAY_FILTER_USE_KEY
			);
		}

		// Read
		get => $this->options ?? [];
	}

	/**
	 * Parameters
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 * 
	 * @var array $parameters Arguments for the $this->method (will be concatenated together with generated request parameters)
	 */
	public array $variables = [] {
		// Read
		&get => $this->variables;
	}

	/**
	 * Constructor
	 *
	 * @param string|controller $controller Name of the controller
	 * @param string|null $method Name of the method of the method of $controller
	 * @param string|model|null $model Name of the model
	 * @param array $parameters Arguments for the $method (will be concatenated together with generated request parameters)
	 * @param array $options Options for `request_parse_body` (Only for POST method)
	 * @param array $middlewares Middlewares stack
	 *
	 * @return void
	 */
	public function __construct(
		string|controller $controller,
		?string $method = 'index',
		string|model|null $model = null,
		array $parameters = [],
		array $options = [],
		array $middlewares = []
	) {
		// Writing name of the controller
		$this->controller = $controller;

		// Writing name of the model
		$this->model = $model;

		// Writing name of the method of the controller
		$this->method = $method;

		// Writing parameters
		$this->parameters = $parameters;

		// Declaring the register of the middlewares stack validity
		$stack = true;

		foreach ($middlewares as $middleware) {
			// Iterating over middlewares

			if ($middleware instanceof middleware) {
				// Initialized the middleware
			} else {
				// Not initialized the middleware

				// Writing the register of the middlewares stack validity
				$stack = false;

				// Exit (fail)
				break;
			}
		}

		if ($stack) {
			// The middlewares stack is valid

			// Writing the middlewares stack
			$this->middlewares = $middlewares;
		}

		// Writing options
		if (match ($method) {
			'GET', 'PUT', 'PATCH', 'DELETE' => true,
			default => false
		}) $this->options = $options;
	}
}
