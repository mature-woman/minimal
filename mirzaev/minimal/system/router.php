<?php

declare(strict_types=1);

namespace mirzaev\minimal;

// Files of the project
use mirzaev\minimal\route,
	mirzaev\minimal\http\request,
	mirzaev\minimal\traits\singleton,
	mirzaev\minimal\traits\middleware as middleware_trait;

// Build-ing libraries
use InvalidArgumentException as exception_argument;

/**
 * Router
 *
 * @package mirzaev\minimal
 *
 * @param array $routes Registry of routes
 *
 * @method self write(string $urn, route $route, string|array $method) Write route to registry of routes (fluent interface)
 * @method route|null match(request $request) Match request URI with registry of routes
 * @method self sort() Sort routes (DEVELOPMENT)
 * @method string universalize(string $urn) Universalize URN
 * @method self middleware(middleware $middleware) Middleware
 * @param array $middlewares Stack of middlewares
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class router
{
	use singleton, middleware_trait;

	/**
	 * Routes
	 *
	 * @var array $routes Registry of routes
	 */
	protected array $routes = [] {
		// Read
		&get => $this->routes;
	}

	/**
	 * Write route
	 *
	 * Write route to registry of routes
	 *
	 * @param string $urn URN of the route ('/', '/page', '/page/$variable', '/page/$collector...'...)
	 * @param route $route The route
	 * @param string|array $method Method of requests
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function write(string $urn, route $route, string|array $method): self
	{
		foreach (is_array($method) ? $method : [$method] as $method) {
			// Iterate over methods of requests

			// Initializing the request
			$request = new request(
				uri: $urn,
				method: $method,
				environment: false
			);

			// Writing to the registry of routes
			$this->routes[$request->uri][$request->method->value] = $route;
		}

		// Exit (success) (fluent interface)
		return $this;
	}

	/**
	 * Match
	 *
	 * Match request URI with registry of routes
	 *
	 * @param request $request The request
	 *
	 * @return route|null Route, if found
	 */
	public function match(request $request): ?route
	{
		// Declaration of the registry of routes directoies 
		$routes = [];

		foreach ($this->routes as $route => $data) {
			// Iteration over routes

			// Search directories of route (explode() creates empty value in array)
			preg_match_all('/(^\/$|[^\/]+)/', $route, $data['directories']);
			$routes[$route] = $data['directories'][0];
		}

		if (count($routes) === count($this->routes)) {
			// Initialized the registry of routes directoies

			// Universalization of URN (/foo/bar)
			$urn = self::universalize(parse_url(urldecode($request->uri), PHP_URL_PATH));

			// Search directories of URN (explode() creates empty value in array)
			preg_match_all('/(^\/$|[^\/]+)/', $urn, $directories);
			$directories = $directories[0];

			/**
			 * Initialization of the route
			 */

			// Initializing the buffer of matches of route directories with URN directories
			$matches = [];

			foreach ($directories as $i => $urn_directory) {
				// Iteration over directories of URN 

				foreach ($this->routes as $route => $data) {
					// Iteration over routes

					if (isset($data[$request->method->value])) {
						// The request method matches the route method

						// Universalization of route
						$route = self::universalize($route);

						// Skipping unmatched routes based on results of previous iterations
						if (isset($matches[$route]) && $matches[$route] === false) continue;


						// Initializing of route directory
						$route_directory = $routes[$route][$i] ?? null;

						if (isset($route_directory)) {
							// Initialized of route directory

							if ($urn_directory === $route_directory) {
								// The directory of URN is identical to the directory of route

								// Writing: end of URN directories XNOR end of route directories
								$matches[$route] = !(isset($directories[$_i = $i + 1]) xor isset($routes[$route][$_i]));
							} else if (preg_match('/^\$([a-zA-Z_\x80-\xff]+)$/', $route_directory) === 1) {
								// The directory of route is a variable ($variable)

								// Writing: end of URN directories XNOR end of route directories
								$matches[$route] = !(isset($directories[$_i = $i + 1]) xor isset($routes[$route][$_i]));
							} else if (
								!isset($routes[$route][$i + 1])
								&& preg_match('/^\$([a-zA-Z_\x80-\xff]+\.\.\.)$/', $route_directory) === 1
							) {
								// The directory of route is a collector ($variable...)
								// AND this is the end of route directories

								// Writing
								$matches[$route] = 'collector';
							} else $matches[$route] = false;
						} else if ($matches[$route] === 'collector') {
						} else $matches[$route] = false;
					}
				}
			}

			if (array_all($matches, fn($value) => $value === false)) {
				// Not found any route

				if (false) {
					// Initialized the errors controller

					// Processing the `404` error method

				}
			} else {
				// Found At least one route

				// Finding a priority route from all match results (setting the $route variable)
				foreach ($matches as $route => $match) if ($match !== false) break;
				unset($match);

				if ($route && !empty($data = $this->routes[$route])) {
					// The route found

					// Universalization of the route
					$route = self::universalize($route);

					/**
					 * Initialization of the route variables
					 */

					foreach ($routes[$route] as $i => $route_directory) {
						// Iteration over directories of route

						if (preg_match('/^\$([a-zA-Z_\x80-\xff]+)$/', $route_directory) === 1) {
							// The directory is a variable ($variable)

							// Запись в реестр переменных и перезапись директории в маршруте
							$data[$request->method->value]->variables[trim($route_directory, '$')] = $directories[$i];
						} else if (preg_match('/^\$([a-zA-Z_\x80-\xff]+\.\.\.)$/', $route_directory) === 1) {
							// The directory of route is a collector ($variable...)

							// Инициализаия ссылки на массив сборщика
							$collector = &$data[$request->method->value]->variables[trim($route_directory, '$.')];

							// Инициализаия массива сборщика
							$collector ??= [];

							// Запись в реестр переменных
							$collector[] = $directories[$i];

							foreach (array_slice($directories, ++$i, preserve_keys: true) as &$urn_directory) {
								// Перебор директорий URN

								// Запись в реестр переменных
								$collector[] = $urn_directory;
							}

							break;
						}
					}

					// Exit (success or fail)
					return $data[$request->method->value] ?? null;
				}
			}
		}

		// Exit (success/fail)
		return null;
	}

	/**
	 * Sorting routes
	 *
	 * 1. Short routes
	 * 2. Long routes
	 * 3. Short routes with variables (position of variables from "right" to "left")
	 * 4. Long routes with variables (position of variables from "right" to "left")
	 * 5. Short routes with collector
	 * 6. Long routes with collector
	 * 7. Short routes with variables and collector (position of variables from "right" to "left" then by amount)
	 * 8. Long routes with variables and collector (position of variables from "right" to "left")
	 *
	 * Добавить чтобы сначала текст потом переменная затем после переменной первыми тексты и в конце перменные опять и так рекурсивно
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 *
	 * @todo ПЕРЕДЕЛАТЬ ПОЛНОСТЬЮ
	 */
	public function sort(): self
	{
		uksort($this->routes, function (string $a, string $b) {
			// Sorting routes

			// Initialization of string lengths (multibyte-safe)
			$length = [
				$a => mb_strlen($a),
				$b => mb_strlen($b)
			];

			// Initialization of the presence of variables
			$variables = [
				$a => preg_match('/\$([a-zA-Z_\x80-\xff]+)(\/|$)/', $a) === 1,
				$b => preg_match('/\$([a-zA-Z_\x80-\xff]+)(\/|$)/', $b) === 1
			];

			// Initialization of the presence of collectors
			$collectors = [
				$a => preg_match('/\$([a-zA-Z_\x80-\xff]+)\.\.\.$/', $a) === 1,
				$b => preg_match('/\$([a-zA-Z_\x80-\xff]+)\.\.\.$/', $b) === 1
			];


			if ($variables[$a] && !$variables[$b]) return 1;
			else if (!$variables[$a] && $variables[$b]) return -1;
			else if ($variables[$a] && $variables[$b]) {
			} else if ($collectors[$a] && !$collectors[$b]) return 1;
			else if (!$collectors[$a] && $collectors[$b]) return -1;
			else {
				// NOR variables and XAND collectors (both are not found or both are found)

				// The routes are the same length (no changes)
				if ($length[$a] === $length[$b]) return 0;

				// The longer route moves to the end
				return $length[$a] > $length[$b] ? 1 : -1;
			}
		});

		// Exit (success) (fluent interface)
		return $this;
	}

	/**
	 * Universalize URN 
	 *
	 * Always "/" at the beginning and never "/" at the end
	 *
	 * @param string $urn URN (/foo/bar)
	 *
	 * @return string Universalized URN 
	 */
	private function universalize(string $urn): string
	{
		// Universalization of URN and exit (success)
		return (string) '/' . mb_trim($urn, '/');
	}
}
