<?php

declare(strict_types=1);

namespace mirzaev\minimal\http;

// Files of the project
use mirzaev\minimal\http\enumerations\method,
	mirzaev\minimal\http\enumerations\protocol,
	mirzaev\minimal\http\enumerations\status,
	mirzaev\minimal\http\enumerations\content,
	mirzaev\minimal\http\response;

// The smartphones detection library
use Detection\MobileDetect as mobile;

// Built-in libraries
use DomainException as exception_domain,
	InvalidArgumentException as exception_argument,
	RuntimeException as exception_runtime,
	LogicException as exception_logic;

/**
 * Request
 *
 * @package mirzaev\minimal\http
 *
 * @param method $method Method
 * @param string $uri URI
 * @param protocol $protocol Version of HTTP protocol
 * @param array $headers Headers
 * @param array $parameters Deserialized parameters from URI and body
 * @param array $files Deserialized files from body
 * @param array $options Options for `request_parse_body($options)`
 *
 * @method void __construct(method|string|null $method, ?string $uri, protocol|string|null $protocol,	array $headers,	array $parameters, array $files, bool $environment) Constructor
 * @method response response() Generate response for request
 * @method self header(string $name, string $value) Write a header to the headers property
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class request
{
	/**
	 * Method
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 *
	 * @throws exception_runtime if reinitialize the property
	 * @throws exception_domain if failed to recognize method
	 * 
	 * @var method $method Method
	 */
	public method $method {
		// Write
		set(method|string $value) {
			if (isset($this->{__PROPERTY__})) {
				// The property is already initialized

				// Exit (fail)
				throw new exception_runtime('The property is already initialized: ' . __PROPERTY__, status::internal_server_error->value);
			}

			if ($value instanceof method) {
				// Received implementation of the method

				// Writing
				$this->method = $value;
			} else {
				// Received a string literal (excected name of the method)

				// Initializing implementator of the method
				$method = method::{strtolower($value)};

				if ($method instanceof method) {
					// Initialized implementator of the method

					// Writing
					$this->method = $method;
				} else {
					// Not initialized implementator of the method

					// Exit (fail)
					throw new exception_domain('Failed to recognize method: ' . $value, status::not_implemented->value);
				}
			}
		}
	}

	/**
	 * URI
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 * 
	 * @throws exception_runtime if reinitialize the property
	 *
	 * @var string $uri URI
	 */
	public string $uri {
		// Write
		set(string $value) {
			if (isset($this->{__PROPERTY__})) {
				// The property is already initialized

				// Exit (fail)
				throw new exception_runtime('The property is already initialized: ' . __PROPERTY__, status::internal_server_error->value);
			}

			// Writing
			$this->uri = $value;
		}
	}

	/**
	 * Protocol
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 * 
	 * @throws exception_runtime if reinitialize the property
	 * @throws exception_domain if failed to recognize HTTP version
	 *
	 * @var protocol $protocol Version of HTTP protocol
	 */
	public protocol $protocol {
		// Write
		set(protocol|string $value) {
			if (isset($this->{__PROPERTY__})) {
				// The property is already initialized

				// Exit (fail)
				throw new exception_runtime('The property is already initialized: ' . __PROPERTY__, status::internal_server_error->value);
			}

			if ($value instanceof protocol) {
				// Received implementation of HTTP version

				// Writing
				$this->protocol = $value;
			} else {
				// Received a string literal (excected name of HTTP version)

				// Initializing implementator of HTTP version
				$protocol = protocol::tryFrom($value);

				if ($protocol instanceof protocol) {
					// Initialized implementator of HTTP version

					// Writing
					$this->protocol = $protocol;
				} else {
					// Not initialized implementator of HTTP version

					// Exit (fail)
					throw new exception_domain('Failed to recognize HTTP version: ' . $value, status::http_version_not_supported->value);
				}
			}
		}
	}

	/**
	 * Headers
	 *
	 * @see https://www.rfc-editor.org/rfc/rfc7540
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 *
	 * @var array $headers Headers
	 */
	public array $headers = [] {
		// Read
		&get => $this->headers;
	}

	/**
	 * Parameters
	 *
	 * For "application/json" will store json_decode(file_get_contents('php://input'))
	 * For method::post will store the value from $_POST ?? []
	 * For other methods with $this->method->body() === true and "multipart/form-data" will store the result of executing request_parse_body($this->options)[0] (>=PHP 8.4)
	 * For other methods with $this->method->body() === true and "application/x-www-form-urlencoded" will store the result of executing request_parse_body($this->options)[0] (>=PHP 8.4)
	 * For other methods with $this->method->body() === true and other Content-Type will store $GLOBALS['_' . $this->method->value] ?? []
	 * For other methods with $this->method->body() === false will store the value from $GLOBALS['_' . $this->method->value] ?? []
	 *
	 * @see https://wiki.php.net/rfc/rfc1867-non-post about request_parse_body()
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 *
	 * @throws exception_runtime if reinitialize the property
	 *
	 * @var array $parameters Deserialized parameters from URI and body
	 */
	public array $parameters {
		// Write
		set(array $value) {
			if (isset($this->{__PROPERTY__})) {
				// The property is already initialized

				// Exit (fail)
				throw new exception_runtime('The property is already initialized: ' . __PROPERTY__, status::internal_server_error->value);
			}

			// Writing
			$this->parameters = $value;
		}

		// Read
		get => $this->parameters ?? [];
	}

	/**
	 * Files
	 *
	 * For method::post will store the value from $_FILES ?? []
	 * For other methods with $this->method->body() === true and "multipart/form-data" will store the result of executing request_parse_body($this->options)[1] (>=PHP 8.4)
	 * For other methods with $this->method->body() === true and "application/x-www-form-urlencoded" will store the result of executing request_parse_body($this->options)[1] (>=PHP 8.4)
	 * For other methods with $this->method->body() === true and other Content-Type will store $_FILES ?? []
	 *
	 * @see https://wiki.php.net/rfc/rfc1867-non-post about request_parse_body()
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 *
	 * @throws exception_runtime if reinitialize the property
	 * @throws exception_runtime if $this->method is not initialized
	 * @throws exception_logic if request with that method can not has files
	 * 
	 * @var array $files Deserialized files from body
	 */
	public array $files {
		// Write
		set(array $value) {
			if (isset($this->{__PROPERTY__})) {
				// The property is already initialized

				// Exit (fail)
				throw new exception_runtime('The property is already initialized: ' . __PROPERTY__, status::internal_server_error->value);
			}

			if (isset($this->method)) {
				// Initialized method 

				if ($this->method->body()) {
					// Request with this method can has body

					// Writing
					$this->files = $value;
				} else {
					// Request with this method can not has body

					// Exit (fail)
					throw new exception_logic('Request with ' . $this->method->value . ' method can not has body therefore can not has files', status::internal_server_error->value);
				}
			} else {
				// Not initialized method

				// Exit (fail)
				throw new exception_runtime('Method of the request is not initialized', status::internal_server_error->value);
			}
		}

		// Read
		get => $this->files ?? [];
	}

	/**
	 * Options
	 *
	 * Required if $this->method !== method::post
	 *
	 * @see https://wiki.php.net/rfc/rfc1867-non-post about request_parse_body()
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
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
	 * Smartphone
	 *
	 * @see https://docs.mobiledetect.net/home/usage-composer Documentation
	 *
	 * @var bool $smartphone The request was sent from a smartphone?
	 */
	public bool $smartphone {
		// Read
		get {
			if (!isset($this->smartphone)) {
				// The property is not initialized

				// Writing into the property
				$this->smartphone = new mobile()->isMobile();
			}

			// Exit (success)
			return $this->smartphone;
		}
	}

	/**
	 * Tablet
	 *
	 * @see https://docs.mobiledetect.net/home/usage-composer Documentation
	 *
	 * @var bool $tablet The request was sent from a tablet?
	 */
	public bool $tablet {
		// Read
		get {
			if (!isset($this->tablet)) {
				// The property is not initialized

				// Writing into the property
				$this->tablet = new mobile()->isTablet();
			}

			// Exit (success)
			return $this->tablet;
		}
	}

	/**
	 * Constructor
	 *
	 * @param method|string|null $method Name of the method
	 * @param string|null $uri URI
	 * @param protocol|string|null $protocol Version of HTTP protocol
	 * @param array|null $headers Headers
	 * @param array|null $parameters Deserialized parameters from URI and body
	 * @param array|null $files Deserialized files from body
	 * @param bool $environment Write values from environment to properties?
	 * 
	 * @throws exception_domain if failed to normalize name of header
	 * @throws exception_argument if failed to initialize JSON
	 * @throws exception_argument if failed to initialize a required property
	 *
	 * @return void
	 */
	public function __construct(
		method|string|null $method = null,
		?string $uri = null,
		protocol|string|null $protocol = null,
		?array $headers = null,
		?array $parameters = null,
		?array $files = null,
		bool $environment = false
	) {
		// Writing method from argument into the property
		if (isset($method))
			$this->method = $method;

		// Writing URI from argument into the property
		if (isset($uri))
			$this->uri = $uri;

		// Writing verstion of HTTP protocol from argument into the property
		if (isset($protocol))
			$this->protocol = $protocol;

		// Declaring the buffer of headers
		$buffer = [];

		foreach (
			match (true) {
				isset($headers) => $headers,
				php_sapi_name() !== 'cli' => getallheaders(),
				default => []
			} as $name => $value
		) {
			// Iterating over headers

			// Normalizing name of header (https://www.rfc-editor.org/rfc/rfc7540#section-8.1.2)
			$name = mb_strtolower($name, 'UTF-8');

			if (empty($name)) {
				// Not normalized name of header

				// Exit (fail)
				throw new exception_domain('Failed to normalize name of header', status::internal_server_error->value);
			}

			// Writing into the buffer of headers
			$buffer[$name] = $value;
		}

		if (!empty($buffer)) {
			// Initialized at lease one header

			// Writing headers into the property
			$this->headers = $buffer;
		}

		// Deinitializing the buffer of headers
		unset($buffer);

		// Writing parameters from argument into the property
		if (isset($parameters))
			$this->parameters = $parameters;

		// Writing files from argument into the property
		if (isset($files))
			$this->files = $files;

		if ($environment) {
			// Requested to write values from environment

			// Writing method from environment into the property
			$this->method ??= $_SERVER["REQUEST_METHOD"];

			// Writing URI from environment into the property
			$this->uri ??= $_SERVER['REQUEST_URI'];

			// Writing verstion of HTTP protocol from environment into the property
			$this->protocol ??= $_SERVER['SERVER_PROTOCOL'];

			if (str_starts_with($this->headers['content-type'] ?? '', content::json->value)) {
				// The body contains "application/json"

				// Initializing data from the input buffer
				$input = file_get_contents('php://input');

				if (json_validate($input, 512)) {
					// Validated JSON

					// Decoding JSON and writing parameters into the property (array type for universalization)
					$this->parameters = json_decode($input, true, 512);
				} else {
					// Not validated JSON

					// Exit (false)
					throw new exception_argument('Failed to validate JSON from the input buffer', status::unprocessable_content->value);
				}
			} else if ($this->method === method::post) {
				// POST method

				// Writing parameters from environment into the property
				$this->parameters = $_POST ?? [];

				// Writing files from environment into the property
				$this->files = $_FILES ?? [];
			} else if ($this->method->body()) {
				// Non POST method and can has body

				if (
					str_starts_with($this->headers['content-type'], content::form->value) ||
					str_starts_with($this->headers['content-type'], content::encoded->value)
				) {
					// Non POST method and the body content type is "multipart/form-data" or "application/x-www-form-urlencoded"

					// Writing parameters and files from environment into the properties
					[$this->parameters, $this->files] = request_parse_body($this->options);
				} else {
					// Non POST method and the body content type is not "multipart/form-data" or "application/x-www-form-urlencoded"

					// Writing parameters from environment into the property
					$this->parameters = $GLOBALS['_' . $this->method->value] ?? [];

					// Writing files from environment into the property
					$this->files = $_FILES ?? [];
				}
			} else {
				// Non POST method and can not has body

				// Writing parameters from environment into the property
				$this->parameters = $GLOBALS['_' . $this->method->value] ?? [];
			}
		}

		// Validating of required properties
		if (empty($this->method))
			throw new exception_argument('Failed to initialize method of the request', status::internal_server_error->value);
		if (empty($this->uri))
			throw new exception_argument('Failed to initialize URI of the request', status::internal_server_error->value);
	}

	/**
	 * Response
	 *
	 * Generate response for request
	 * 
	 * @return response Reponse for request
	 */
	public function response(): response
	{
		// Exit (success)
		return new response(protocol: $this->protocol, status: status::ok);
	}

	/**
	 * Header
	 *
	 * Write a header to the headers property
	 *
	 * @see https://www.rfc-editor.org/rfc/rfc7540
	 *
	 * @param string $name Name
	 * @param string $value Value
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function header(string $name, string $value): self
	{
		// Normalizing name of header and writing to the headers property (https://www.rfc-editor.org/rfc/rfc7540#section-8.1.2)
		$this->headers[mb_strtolower($name, 'UTF-8')] = $value;

		// Exit (success)
		return $this;
	}
}
