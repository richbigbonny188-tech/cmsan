<?php
/* --------------------------------------------------------------
   GX4Module.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Model\ValueObjects;

use Gambio\Core\Application\Modules\Module;

/**
 * Class GX4Module
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class GX4Module implements GXModuleComponent
{
    /**
     * @var string
     */
    private $className;
    
    
    /**
     * GX4Module constructor.
     *
     * @param string $className
     */
    private function __construct(string $className)
    {
        $this->className = $className;
    }
    
    
    /**
     * @param string $className
     *
     * @return GX4Module
     */
    public static function create(string $className): GX4Module
    {
        return new self($className);
    }
    
    
    /**
     * @return string
     */
    public function className(): string
    {
        return $this->className;
    }
    
    
    /**
     * @return Module
     */
    public function createClass(): Module
    {
        $fqn = $this->className;
        
        return new $fqn();
    }
    
    
    /**
     * @return string
     */
    public static function type(): string
    {
        return 'gx4_module';
    }
}