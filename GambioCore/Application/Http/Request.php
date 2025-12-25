<?php
/* --------------------------------------------------------------
 Request.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Request
 *
 * @package Gambio\Core\Application\Http
 */
interface Request extends ServerRequestInterface
{
    /**
     * Create a new instance with the specified derived request attributes.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method allows setting all new derived request attributes as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * updated attributes.
     *
     * @param array $attributes New attributes
     *
     * @return static
     */
    public function withAttributes(array $attributes): Request;
    
    
    /**
     * Get serverRequest content character set, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string|null
     */
    public function getContentCharset(): ?string;
    
    
    /**
     * Get serverRequest content type.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string|null The serverRequest content type, if known
     */
    public function getContentType(): ?string;
    
    
    /**
     * Get serverRequest content length, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return int|null
     */
    public function getContentLength(): ?int;
    
    
    /**
     * Fetch cookie value from cookies sent by the client to the server.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key     The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return mixed
     */
    public function getCookieParam(string $key, $default = null);
    
    
    /**
     * Get serverRequest media type, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string|null The serverRequest media type, minus content-type params
     */
    public function getMediaType(): ?string;
    
    
    /**
     * Get serverRequest media type params, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return array
     */
    public function getMediaTypeParams(): array;
    
    
    /**
     * Fetch serverRequest parameter value from body or query string (in that order).
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string      $key     The parameter key.
     * @param string|null $default The default value.
     *
     * @return mixed The parameter value.
     */
    public function getParam(string $key, string $default = null);
    
    
    /**
     * Fetch associative array of body and query string parameters.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return array
     */
    public function getParams(): array;
    
    
    /**
     * Fetch parameter value from serverRequest body.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParsedBodyParam(string $key, $default = null);
    
    
    /**
     * Fetch parameter value from query string.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getQueryParam(string $key, $default = null);
    
    
    /**
     * Retrieve a server parameter.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getServerParam(string $key, $default = null);
    
    
    /**
     * Register media type parser.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string   $mediaType A HTTP media type (excluding content-type params).
     * @param callable $callable  A callable that returns parsed contents for media type.
     *
     * @return static
     */
    public function registerMediaTypeParser(string $mediaType, callable $callable): Request;
    
    
    /**
     * Is this a DELETE serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isDelete(): bool;
    
    
    /**
     * Is this a GET serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isGet(): bool;
    
    
    /**
     * Is this a HEAD serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isHead(): bool;
    
    
    /**
     * Does this serverRequest use a given method?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $method HTTP method
     *
     * @return bool
     */
    public function isMethod(string $method): bool;
    
    
    /**
     * Is this a OPTIONS serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isOptions(): bool;
    
    
    /**
     * Is this a PATCH serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isPatch(): bool;
    
    
    /**
     * Is this a POST serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isPost(): bool;
    
    
    /**
     * Is this a PUT serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isPut(): bool;
    
    
    /**
     * Is this an XHR serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isXhr(): bool;
}