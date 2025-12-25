<?php
/* --------------------------------------------------------------
   ServiceProvider.php 2020-10-26
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
 * Class ServiceProvider
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class ServiceProvider implements GXModuleComponent
{
    /**
     * @var string
     */
    private $className;
    
    
    /**
     * ServiceProvider constructor.
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
     * @return ServiceProvider
     */
    public static function create(string $className): ServiceProvider
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
     * @return string
     */
    public static function type(): string
    {
        return 'service_provider';
    }
}