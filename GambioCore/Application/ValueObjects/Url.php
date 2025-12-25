<?php
/* --------------------------------------------------------------
 Url.php 2022-03-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ValueObjects;

/**
 * Class Url
 *
 * @package Gambio\Core\Application\ValueObjects
 */
class Url
{
    /**
     * @var string
     */
    private $host;
    
    /**
     * @var string
     */
    private $path;
    
    /**
     * @var string
     */
    private $base;
    
    /**
     * @var string
     */
    private $admin;
    
    
    /**
     * Url constructor.
     *
     * @param string $host
     * @param string $path
     */
    public function __construct(string $host, string $path)
    {
        $base = $host . $path;
        
        $this->host  = $host;
        $this->path  = $path;
        $this->base  = $base;
        $this->admin = "{$base}/admin";
    }
    
    
    /**
     * Returns the host URL.
     *
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }
    
    
    /**
     * Returns the path of the currently accessed URL.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }
    
    
    /**
     * Returns the base URL of the shop.
     *
     * @return string
     */
    public function base(): string
    {
        return $this->base;
    }
    
    
    /**
     * Returns the base URL of the admin.
     *
     * @return string
     */
    public function admin(): string
    {
        return $this->admin;
    }
    
    
    /**
     * Returns the base URL of the REST API v3.
     *
     * @return string
     */
    public function restApiV3(): string
    {
        return "{$this->base}/api.php/v3";
    }
}