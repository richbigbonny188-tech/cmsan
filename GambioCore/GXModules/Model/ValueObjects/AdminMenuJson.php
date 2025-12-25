<?php
/* --------------------------------------------------------------
   AdminMenuJson.php 2020-10-26
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
 * Class AdminMenuJson
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class AdminMenuJson implements GXModuleComponent
{
    /**
     * @var string
     */
    private $filePath;
    
    
    /**
     * AdminMenuJson constructor.
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
     * @return AdminMenuJson
     */
    public static function create(string $filePath): AdminMenuJson
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
        return 'menu_json';
    }
}