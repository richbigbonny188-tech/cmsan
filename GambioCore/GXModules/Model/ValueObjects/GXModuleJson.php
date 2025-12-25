<?php
/* --------------------------------------------------------------
   GXModuleJson.php 2020-10-26
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
 * Class GXModuleJson
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class GXModuleJson implements GXModuleComponent
{
    /**
     * @var string
     */
    private $filePath;
    
    
    /**
     * GXModuleJson constructor.
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
     * @return GXModuleJson
     */
    public static function create(string $filePath): GXModuleJson
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
        return 'gxmodule_json';
    }
}