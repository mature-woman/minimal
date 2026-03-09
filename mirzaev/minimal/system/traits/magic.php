<?php

declare(strict_types=1);

namespace mirzaev\minimal\traits;

// Files of the project
use mirzaev\minimal\http\enumerations\status;

// Built-in libraries
use Exception as exception,
	DomainException as exception_domain;

/**
 * Trait of magical methods
 *
 * @package mirzaev\minimal\traits
 *
 * @method void __set(string $name, mixed $value) Write property
 * @method mixed __get(string $name) Read property
 * @method void __unset(string $name) Delete property
 * @method bool __isset(string $name) Check property for initialization
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
trait magic
{
	/**
	 * Write property
	 *
	 * @throws exception_domain Not found the proprty
	 *
	 * @param string $name Name of the property
	 * @param mixed $value Value of the property
	 *
	 * @return void
	 */
	public function __set(string $name, mixed $value = null): void
	{
		if (property_exists(static, $name)) {
			// Exist the property

			// Writing the property
			$this->{$name} = $value;
		} else {
			// Not exist the property

			// Exit (fail)
			throw new exception_domain('Not found the property: ' . static::class . "::\$$name", status::not_found->value);
		}
	}

	/**
	 * Read property
	 *
	 * @throws exception_domain Not found the property
	 *
	 * @param string $name Name of the property
	 *
	 * @return mixed Value of the property
	 */
	public function __get(string $name): mixed
	{
		return match ($name) {
			default => throw new exception_domain('Not found the property: ' . static::class . "::\$$name", status::not_found->value)
		};
	}

	/**
	 * Delete property
	 *
	 * @param string $name Name of the property
	 *
	 * @return void
	 */
	public function __unset(string $name): void
	{
		match ($name) {
			default => (function () use ($name) {
				unset($this->{$name});
			})()
		};
	}

	/**
	 * Check property for initialization
	 *
	 * @param string $name Name of the property
	 *
	 * @return bool Is the property initialized?
	 */
	public function __isset(string $name): bool
	{
		return match ($name) {
			default => isset($this->{$name})
		};
	}
}
