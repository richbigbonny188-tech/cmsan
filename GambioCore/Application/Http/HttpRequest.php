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

use Gambio\Core\Application\Http\Request as RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\ServerRequest as SlimRequest;

/**
 * Class HttpRequest
 *
 * @package Gambio\Core\Application\Http
 * @codeCoverageIgnore
 */
class HttpRequest implements RequestInterface
{
    /**
     * @var SlimRequest
     */
    private $internal;
    
    
    /**
     * Request constructor.
     *
     * @param SlimRequest $internal
     */
    public function __construct(SlimRequest $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritdoc
     */
    public function getAttribute($name, $default = null)
    {
        return $this->internal->getAttribute($name, $default);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return $this->internal->getAttributes();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getBody(): StreamInterface
    {
        return $this->internal->getBody();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getCookieParams(): array
    {
        return $this->internal->getCookieParams();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getHeader($name): array
    {
        return $this->internal->getHeader($name);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getHeaderLine($name): string
    {
        return $this->internal->getHeaderLine($name);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->internal->getHeaders();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getMethod(): string
    {
        return $this->internal->getMethod();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getParsedBody()
    {
        return $this->internal->getParsedBody();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getProtocolVersion(): string
    {
        return $this->internal->getProtocolVersion();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getQueryParams(): array
    {
        return $this->internal->getQueryParams();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getRequestTarget(): string
    {
        return $this->internal->getRequestTarget();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getServerParams(): array
    {
        return $this->internal->getServerParams();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getUploadedFiles(): array
    {
        return $this->internal->getUploadedFiles();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getUri(): UriInterface
    {
        return $this->internal->getUri();
    }
    
    
    /**
     * @inheritdoc
     */
    public function hasHeader($name): bool
    {
        return $this->internal->hasHeader($name);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value): RequestInterface
    {
        $serverRequest = $this->internal->withAddedHeader($name, $value);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withAttribute($name, $value): RequestInterface
    {
        $serverRequest = $this->internal->withAttribute($name, $value);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withAttributes(array $attributes): RequestInterface
    {
        $serverRequest = $this->internal->withAttributes($attributes);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withoutAttribute($name): RequestInterface
    {
        $serverRequest = $this->internal->withoutAttribute($name);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body): RequestInterface
    {
        $serverRequest = $this->internal->withBody($body);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withCookieParams(array $cookies): RequestInterface
    {
        $serverRequest = $this->internal->withCookieParams($cookies);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withHeader($name, $value): RequestInterface
    {
        $serverRequest = $this->internal->withHeader($name, $value);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withoutHeader($name): RequestInterface
    {
        $serverRequest = $this->internal->withoutHeader($name);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withMethod($method): RequestInterface
    {
        $serverRequest = $this->internal->withMethod($method);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withParsedBody($data): RequestInterface
    {
        $serverRequest = $this->internal->withParsedBody($data);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version): RequestInterface
    {
        $serverRequest = $this->internal->withProtocolVersion($version);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withQueryParams(array $query): RequestInterface
    {
        $serverRequest = $this->internal->withQueryParams($query);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget): RequestInterface
    {
        $serverRequest = $this->internal->withRequestTarget($requestTarget);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles): RequestInterface
    {
        $serverRequest = $this->internal->withUploadedFiles($uploadedFiles);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        $serverRequest = $this->internal->withUri($uri, $preserveHost);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getContentCharset(): ?string
    {
        return $this->internal->getContentCharset();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getContentType(): ?string
    {
        return $this->internal->getContentType();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getContentLength(): ?int
    {
        return $this->internal->getContentLength();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getCookieParam(string $key, $default = null)
    {
        return $this->internal->getCookieParam($key, $default);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getMediaType(): ?string
    {
        return $this->internal->getMediaType();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getMediaTypeParams(): array
    {
        return $this->internal->getMediaTypeParams();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getParam(string $key, $default = null)
    {
        return $this->internal->getParam($key, $default);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getParams(): array
    {
        return $this->internal->getParams();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getParsedBodyParam(string $key, $default = null)
    {
        return $this->internal->getParsedBodyParam($key, $default);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getQueryParam(string $key, $default = null)
    {
        return $this->internal->getQueryParam($key, $default);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getServerParam(string $key, $default = null)
    {
        return $this->internal->getServerParam($key, $default);
    }
    
    
    /**
     * @inheritdoc
     */
    public function registerMediaTypeParser(string $mediaType, callable $callable): RequestInterface
    {
        $serverRequest = $this->internal->registerMediaTypeParser($mediaType, $callable);
        
        return new static($serverRequest);
    }
    
    
    /**
     * @inheritdoc
     */
    public function isDelete(): bool
    {
        return $this->internal->isDelete();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isGet(): bool
    {
        return $this->internal->isGet();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isHead(): bool
    {
        return $this->internal->isHead();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isMethod(string $method): bool
    {
        return $this->internal->isMethod($method);
    }
    
    
    /**
     * @inheritdoc
     */
    public function isOptions(): bool
    {
        return $this->internal->isOptions();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isPatch(): bool
    {
        return $this->internal->isPatch();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isPost(): bool
    {
        return $this->internal->isPost();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isPut(): bool
    {
        return $this->internal->isPut();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isXhr(): bool
    {
        return $this->internal->isXhr();
    }
}