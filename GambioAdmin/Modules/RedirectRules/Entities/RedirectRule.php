<?php
/* --------------------------------------------------------------
   RedirectRule.php 2022-08-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Entities;

use JsonSerializable;

class RedirectRule implements JsonSerializable
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $urlPath;
    /** @var string */
    protected $query;
    /** @var string */
    protected $queryMatchMode;
    /** @var int */
    protected $responseCode;
    /** @var string */
    protected $target;
    /** @var string */
    protected $queryProcessing;
    /** @var bool */
    protected $status;
    
    
    /**
     * RedirectRule constructor.
     *
     * @param int    $id
     * @param string $urlPath
     * @param string $query
     * @param string $queryMatchMode
     * @param int    $responseCode
     * @param string $target
     * @param string $queryProcessing
     * @param bool   $status
     */
    public function __construct(
        int $id,
        string $urlPath,
        string $query,
        string $queryMatchMode,
        int $responseCode,
        string $target,
        string $queryProcessing,
        bool $status
    ) {
        $this->id              = $id;
        $this->urlPath         = $urlPath;
        $this->query           = $query;
        $this->queryMatchMode  = $queryMatchMode;
        $this->responseCode    = $responseCode;
        $this->target          = $target;
        $this->queryProcessing = $queryProcessing;
        $this->status          = $status;
    }
    
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function getUrlPath(): string
    {
        return $this->urlPath;
    }
    
    
    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
    
    
    /**
     * @return string
     */
    public function getQueryMatchMode(): string
    {
        return $this->queryMatchMode;
    }
    
    
    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }
    
    
    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }
    
    
    /**
     * @return string
     */
    public function getQueryProcessing(): string
    {
        return $this->queryProcessing;
    }
    
    
    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }
    
    
    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }
    
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id'              => $this->id,
            'urlPath'         => $this->urlPath,
            'query'           => $this->query,
            'queryMatchMode'  => $this->queryMatchMode,
            'responseCode'    => $this->responseCode,
            'target'          => $this->target,
            'queryProcessing' => $this->queryProcessing,
            'status'          => $this->status,
        ];
    }
}
