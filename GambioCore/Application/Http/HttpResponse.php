<?php
/* --------------------------------------------------------------
 Response.php 2021-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Http;

use Gambio\Core\Application\Http\Response as ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Response as SlimResponse;

/**
 * Class HttpResponse
 *
 * @package Gambio\Core\Application\Http
 * @codeCoverageIgnore
 */
class HttpResponse implements ResponseInterface
{
    /**
     * @var SlimResponse
     */
    private $internal;
    
    
    /**
     * Response constructor.
     *
     * @param SlimResponse $internal
     */
    public function __construct(SlimResponse $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * Returns a slim response instance.
     *
     * @return SlimResponse
     */
    public function toSlimResponse(): SlimResponse
    {
        return $this->internal;
    }
    
    
    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->internal->getBody();
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
    public function getProtocolVersion(): string
    {
        return $this->internal->getProtocolVersion();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getReasonPhrase(): string
    {
        return $this->internal->getReasonPhrase();
    }
    
    
    /**
     * @inheritdoc
     */
    public function getStatusCode(): int
    {
        return $this->internal->getStatusCode();
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
    public function withAddedHeader($name, $value): ResponseInterface
    {
        $response = $this->internal->withAddedHeader($name, $value);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body): ResponseInterface
    {
        $response = $this->internal->withBody($body);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withHeader($name, $value): ResponseInterface
    {
        $response = $this->internal->withHeader($name, $value);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withoutHeader($name): ResponseInterface
    {
        $response = $this->internal->withoutHeader($name);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version): ResponseInterface
    {
        $response = $this->internal->withProtocolVersion($version);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        $response = $this->internal->withStatus($code, $reasonPhrase);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withJson($data, ?int $status = null, int $options = 0, int $depth = 512): ResponseInterface
    {
        $response = $this->internal->withJson($data, $status, $options, $depth);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withRedirect(string $url, ?int $status = null): ResponseInterface
    {
        $response = $this->internal->withRedirect($url, $status);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withFileDownload($file, ?string $name = null, $contentType = true): ResponseInterface
    {
        $response = $this->internal->withFileDownload($file, $name, $contentType);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function withFile($file, $contentType = true): ResponseInterface
    {
        $response = $this->internal->withFile($file, $contentType);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function write(string $data): ResponseInterface
    {
        $response = $this->internal->write($data);
        
        return new static($response);
    }
    
    
    /**
     * @inheritdoc
     */
    public function isClientError(): bool
    {
        return $this->internal->isClientError();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return $this->internal->isEmpty();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isForbidden(): bool
    {
        return $this->internal->isForbidden();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isInformational(): bool
    {
        return $this->internal->isInformational();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isOk(): bool
    {
        return $this->internal->isOk();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isNotFound(): bool
    {
        return $this->internal->isNotFound();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isRedirect(): bool
    {
        return $this->internal->isRedirect();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isRedirection(): bool
    {
        return $this->internal->isRedirection();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isServerError(): bool
    {
        return $this->internal->isServerError();
    }
    
    
    /**
     * @inheritdoc
     */
    public function isSuccessful(): bool
    {
        return $this->internal->isSuccessful();
    }
    
    
    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return (string)$this->internal;
    }
}