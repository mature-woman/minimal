<?php

declare(strict_types=1);

namespace mirzaev\minimal\http\enumerations;

/**
 * Protocol
 *
 * Versions of HTTP
 *
 * @package mirzaev\minimal\http\enumerations
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum protocol: string
{
	case http_3 = 'HTTP/3.0';
	case http_2 = 'HTTP/2.0';
	case http_1_1 = 'HTTP/1.1';
	case http_1 = 'HTTP/1.0';
	case http_0_9 = 'HTTP/0.9';
}
