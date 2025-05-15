<?php

declare(strict_types=1);

namespace mirzaev\minimal\http;

// Files of the project
use mirzaev\minimal\http\enumerations\method,
	mirzaev\minimal\http\enumerations\protocol,
	mirzaev\minimal\http\enumerations\content,
	mirzaev\minimal\http\enumerations\status,
	mirzaev\minimal\http\header;

// Built-in libraries
use DomainException as exception_domain,
	InvalidArgumentException as exception_argument,
	RuntimeException as exception_runtime,
	LogicException as exception_logic;

/**
 * Response
 *
 * @package mirzaev\minimal\http
 *
 * @param protocol $protocol Version of HTTP protocol
 * @param status $status Status
 * @param array $headers Headers
 * @param string $body Body
 *
 * @method self __construct(protocol|string|null $protocol, ?status $status, ?array $headers,	bool $environment) Constructor
 * @method self sse() Writes headers for SSE implementation
 * @method self json(mixed $content) Writes headers for JSON implementation
 * @method self write(string $value) Concatenates with the response body property
 * @method self body() Transfer the contents of the body property to the output buffer
 * @method self|false validate(request $request) Validate response by request
 * @method string status() Generates the status line (HTTP/2 200 OK)
 * @method self header(string $name, string $value) Write a header to the headers property
 * @method self start() Initializes response headers and output buffer
 * @method self clean() Delete everything in the output buffer
 * @method self end() Initializes response headers and flushes the output buffer
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class response
{
	/**
	 * Protocol
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 * 
	 * @throws exception_domain if failed to recognize HTTP version
	 *
	 * @var protocol $protocol Version of HTTP protocol
	 */
	public protocol $protocol {
		// Write
		set (protocol|string $value) {

			if ($value instanceof protocol) {
				// Received implementation of HTTP version
		
				// Writing
				$this->protocol = $value;
			} else {
				// Received a string literal (excected name of HTTP version)
				
				// Initializing implementator of HTTP version
				$protocol = protocol::{$value};

				if ($protocol instanceof protocol) {
					// Initialized implementator of HTTP version
					
					// Writing
					$this->protocol = $protocol;
				} else {
					// Not initialized implementator of HTTP version
					
					// Exit (fail)
					throw new exception_domain("Failed to recognize HTTP version: $value", status::http_version_not_supported>value);
				}
			}
		}
	}

	/**
	 * Status
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 * 
	 * @throws exception_domain if failed to recognize status
	 *
	 * @var status $status Status
	 */
	public status $status {
		// Write
		set (status|string $value) {

			if ($value instanceof status) {
				// Received implementation of status
		
				// Writing
				$this->status = $value;
			} else {
				// Received a string literal (excected name of status)
				
				// Initializing implementator of status
				$status = status::{$value};

				if ($status instanceof status) {
					// Initialized implementator of status
					
					// Writing
					$this->status = $status;
				} else {
					// Not initialized implementator of status
					
					// Exit (fail)
					throw new exception_domain("Failed to recognize status: $value", status::internal_server_error->value);
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
	 * Body
	 *
	 * @see https://wiki.php.net/rfc/property-hooks (find a table about backed and virtual hooks)
	 *
	 * @var string $body Serialized content
	 */
	public string $body = '' {
		// Write
		set (string $value) {
			// Writing
			$this->body = $value;
		}

		// Read
		get => $this->body;
	}

	/**
	 * Constructor
	 *
	 * @param protocol|string|null $protocol version of http protocol
	 * @param status|null $status Status
	 * @param array|null $headers Headers
	 * @param bool $environment Write values from environment to properties?
	 * 
	 * @throws exception_domain if failed to normalize name of header
	 * @throws exception_argument if failed to initialize a required property
	 *
	 * @return void
	 */
	public function __construct(
		protocol|string|null $protocol = null, 
		?status $status = null,
		?array $headers = null,
		bool $environment = false
	) {	
		// Writing verstion of HTTP protocol from argument into the property
		if (isset($protocol)) $this->protocol = $protocol;
	
		// Writing status from argument into the property
		if (isset($status)) $this->status = $status;

		if (isset($headers)) {
			// Received headers
			
			// Declaring the buffer of headers
			$buffer = [];

			foreach ($headers ?? [] as $name => $value) {
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

			// Writing headers from argument into the property
			$this->headers = $buffer;

			// Deinitializing the buffer of headers
			unset($buffer);
		}

		if ($environment) {
			// Requested to write values from environment

			// Writing verstion of HTTP protocol from environment into the property
			$this->protocol ??= $_SERVER['SERVER_PROTOCOL'];

			// Writing status from environment into the property
			$this->status ??= status::ok;
		}

		// Validating of required properties
		if (!isset($this->protocol)) throw new exception_argument('Failed to initialize HTTP version of the request', status::internal_server_error->value);
		if (!isset($this->status)) throw new exception_argument('Failed to initialize status of the request', status::internal_server_error->value);
	}

	/**
	 * Server-Sent Events (SSE)
	 *
	 * Writes headers for SSE implementation
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function sse(): self 
	{
		// Writing headers to the headers property (https://www.rfc-editor.org/rfc/rfc7540#section-8.1.2)
		$this->headers['x-accel-buffering'] = 'no';
		$this->headers['content-encoding'] = 'none';

		// Exit (success)
		return $this;
	}

	/**
	 * JSON
	 *
	 * Writes headers for JSON implementation
	 * Concatenates with the response body property if $content argument was received
	 *
	 * @param mixed $content JSON or content that will be serialized via json_encode()
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function json(mixed $content): self 
	{
		// Writing headers to the headers property (https://www.rfc-editor.org/rfc/rfc7540#section-8.1.2)
		$this->headers['content-type'] = content::json->value;

		if (!empty($content)) {
			// Received content

			if (is_string($content) && json_validate($content, 512)) {
				// Validated as JSON

				// Writing to the response body property
				$this->body .= $content;
			} else {
				// Not validated as JSON
				
				// Writing to the response body property
				$this->body .= json_encode($content, depth: 512);
			}
		}

		// Exit (success)
		return $this;
	}

	/**
	 * Write
	 *
	 * Concatenates with the response body property
	 *
	 * @param string $value Value that will be concatenated with the response body property
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function write(string $value): self
	{
		// Writing to the response body property
		$this->body .= $value;

		// Exit (success)
		return $this;
	}

	/**
	 * Body
	 *
	 * Transfer the contents of the body property to the output buffer
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function body(): self
	{
		// Writing to the output buffer
		echo $this->body;

		// Exit (success)
		return $this;
	}

	/**
	 * Validate
	 *
	 * Validate response by request
	 *
	 * @param request $request The request for validating with it
	 *
	 * @return self|false The instance from which the method was called if validated (fluent interface)
	 */
	public function validate(request $request): self|false
	{
		if (str_contains($request->headers['accept'] ?? '', $this->headers['content-type'] ?? '')) {
			// Validated with "accept" and "content-type"
	
			// Exit (success)
			return $this;
		}

		// Exit (fail)
		return false;

		// Exit (fail)
		return false;
	}

	/**
	 * Status line
	 *
	 * Generates the status line (HTTP/2 200 OK)
	 *
	 * @param protocol|null $protocol Version of HTTP
	 * @param status|null $status Status code and status text
	 *
	 * @return string The status line
	 */
	public function status(?protocol $protocol = null, ?status $status = null): string 
	{
		// Declaring buffer of status line
		$buffer = '';

		if ($protocol instanceof protocol) {
			// Received version of HTTP

			// Writing to buffer of status line
			$buffer .= $protocol->value . ' ';
		} else {
			// Not received version of HTTP

			// Writing to buffer of status line
			$buffer .= $this->protocol->value . ' ';
		}

		if ($status instanceof status) {
			// Received status

			// Writing to buffer of status line
			$buffer .= $status->value . ' ' . $status->label();
		} else {
			// Not received status

			// Writing to buffer of status line
			$buffer .= $this->status->value . ' ' . $this->status->label();
		}

		// Exit (success)
		return $buffer;
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

	/**
	 * Start
	 *
	 * Initializes response headers and output buffer
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function start(): self 
	{
		// Initializing the heaader string
		header($this->status());

		// Initializing headers
		foreach ($this->headers ?? [] as $name => $value) header("$name: $value", replace: true);

		// Initializing the output buffer
		ob_start();

		// Exit (success)
		return $this;
	}

	/**
	 * Clean
	 *
	 * Delete everything in the output buffer
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function clean(): self
	{
		// Flushing the output buffer
    ob_clean();

		// Exit (success)
		return $this;
	}

	/**
	 * End
	 *
	 * Initializes response headers and flushes the output buffer
	 *
	 * @return self The instance from which the method was called (fluent interface)
	 */
	public function end(): self 
	{
		// Calculate length of the output buffer and write to the header (https://www.rfc-editor.org/rfc/rfc7540#section-8.1.2)
		header('content-length: ' . ob_get_length());

		// Sending response and flushing the output buffer
		ob_end_flush();
		flush();

		// Deinitializing headers property
		$this->headers = [];
	
		// Deinitializing headers
		/* header_remove(); */
		
		// Exit (success)
		return $this;
	}
}
