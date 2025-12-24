<?php
/* --------------------------------------------------------------
   Routes.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Model\ValueObjects;

/**
 * Class Routes
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class Routes implements GXModuleComponent
{
    /**
     * @var string
     */
    private $filePath;
    
    
    /**
     * Route constructor.
     *
     * @param string $filePath
     */
    private function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }
    
    
    /**
     * @param string $filePath
     *
     * @return Routes
     */
    public static function create(string $filePath): Routes
    {
        return new self($filePath);
    }
    
    
    /**
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }
    
    
    /**
     * @return string
     */
    public static function type(): string
    {
        return 'routes';
    }
}