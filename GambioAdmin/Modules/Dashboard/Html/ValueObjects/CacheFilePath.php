<?php
/*--------------------------------------------------------------
   CacheFilePath.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Html\ValueObjects;

/**
 * Class CacheFilePath
 * @package Gambio\Admin\Modules\Dashboard\Html\ValueObjects
 */
class CacheFilePath
{
    /**
     * @var string
     */
    protected $path;
    
    
    /**
     * CacheFilePath constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->path;
    }
}