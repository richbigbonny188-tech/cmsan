<?php
/* --------------------------------------------------------------
 Path.php 2021-05-14
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
 * Class Path
 *
 * @package Gambio\Core\Application\ValueObjects
 */
class Path
{
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
     * @param string $base
     */
    public function __construct(string $base)
    {
        $this->base  = $base;
        $this->admin = "{$base}/admin";
    }
    
    
    /**
     * Returns base path of the shop root directory.
     *
     * @return string
     */
    public function base(): string
    {
        return $this->base;
    }
    
    
    /**
     * Returns the base path of the admins root directory.
     *
     * @return string
     */
    public function admin(): string
    {
        return $this->admin;
    }
}