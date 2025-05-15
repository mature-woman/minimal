<?php

declare(strict_types=1);

namespace mirzaev\minimal\http\enumerations;

// Files of the project
use mirzaev\minimal\http\enumerations\status;

// Built-in libraries
use InvalidArgumentException as exception_argument,
	DomainException as exception_domain;

/**
 * Content
 *
 * Implementation of "Content-Type" header 
 *
 * @package mirzaev\minimal\http\enumerations
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum content: string
{
	case any = '*/*';

		// Text

	case txt = 'text/plain';
	case css = 'text/css';
	case csv = 'text/csv';
	case html = 'text/html';
	case js = 'text/javascript'; // js + mjs (https://www.rfc-editor.org/rfc/rfc9239#name-text-javascript)

		// Applications

	case binary = 'application/octet-stream';
	case encoded = 'application/x-www-form-urlencoded';
	case json = 'application/json';
	case rdf = 'application/ld+json';
	case xml = 'application/xml';
	case ogx = 'application/ogg';
	case pdf = 'application/pdf';
	case xls = 'application/vnd.ms-excel';
	case xlsx = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	case tar = 'application/x-tar';
	case zip = 'application/zip';
	case zip7 = 'application/x-7z-compressed';
	case rar = 'application/vnd.rar';
	case jar = 'application/java-archive';
	case odp = 'application/vnd.oasis.opendocument.presentation';
	case ods = 'application/vnd.oasis.opendocument.spreadsheet';
	case odt = 'application/vnd.oasis.opendocument.text';
	case php = 'application/x-httpd-php';
	case sh = 'application/x-sh';
	case xhtml = 'application/xhtml+xml';

		// Audio

	case aac = 'audio/aac';
	case mp3 = 'audio/mpeg';
	case wav = 'audio/wav';
	case oga = 'audio/ogg';
	case weba = 'audio/webm';

		// Images

	case gif = 'image/gif';
	case jpeg = 'image/jpeg';
	case png = 'image/png';
	case apng = 'image/apng';
	case tiff = 'image/tiff';
	case svg = ' image/svg+xml';
	case webp = 'image/webp';
	case avif = 'image/avif';
	case bmp = 'image/bmp';
	case ico = 'image/vnd.microsoft.icon';

		// Videos

	case avi = 'video/x-msvideo';
	case mp4 = 'video/mp4';
	case mpeg = 'video/mpeg';
	case ogv = 'video/ogg';
	case ts = 'video/mp2t';

		// Fonts

	case otf = 'font/otf';
	case ttf = 'font/ttf';
	case woff = 'font/woff';
	case woff2 = 'font/woff2';

		// Multipart

	case form = 'multipart/form-data';
	case mixed = 'multipart/mixed';
	case alternative = 'multipart/alternative';
	case related = 'multipart/related';

	/**
	 * Extension
	 *
	 * Returns the file extension without a dot
	 *
	 * @throws exception_argument if content can not have file extension
	 * @throws exception_domain if failed to recognize content
	 *
	 * @return string File extension
	 */
	public function extension(): string
	{
		// Exit (success)
		return match ($this) {
			self::jpeg => 'jpg',
			self::png => 'png',
			self::webp => 'webp',
			self::form, self::mixed, self::alternative, self::related => throw new exception_argument('Content can not have file extension', status::internal_server_error->value),
			default => throw new exception_domain('Failed to recognize content: ' . $this->value, status::not_found->value)
		};
	}
}
