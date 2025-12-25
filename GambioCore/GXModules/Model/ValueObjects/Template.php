<?php
/* --------------------------------------------------------------
   Template.php 2020-10-26
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
 * Class Template
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class Template implements GXModuleComponent
{
    /**
     * @var string
     */
    private $filePath;
    
    
    /**
     * Template constructor.
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
     * @return Template
     */
    public static function create(string $filePath): Template
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
        return 'template';
    }
}