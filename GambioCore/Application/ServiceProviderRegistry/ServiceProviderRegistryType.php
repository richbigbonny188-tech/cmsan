<?php
/* --------------------------------------------------------------
 ServiceProviderRegistryType.php 2022-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviderRegistry;

/**
 * Class ServiceProviderRegistryType
 *
 * @package Gambio\Core\Application\ServiceProviderRegistry
 * @deprecated In favor of `Gambio\Core\Application\ModuleRegistry\ServiceProviderLoader`
 */
class ServiceProviderRegistryType
{
    private const TYPE_ADMIN = 'admin';
    private const TYPE_API   = 'api';
    
    private const TYPE_MAPPING = [
        self::TYPE_ADMIN => ['dir' => 'GambioAdmin', 'namespace' => 'Gambio\Admin'],
        self::TYPE_API   => ['dir' => 'GambioApi', 'namespace' => 'Gambio\Api'],
    ];
    
    /**
     * @var string
     */
    private $type;
    
    
    /**
     * ServiceProviderRegistryType constructor.
     *
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    
    
    /**
     * @return static
     */
    public static function admin(): self
    {
        return new static(self::TYPE_ADMIN);
    }
    
    
    /**
     * @return static
     */
    public static function api(): self
    {
        return new static(self::TYPE_API);
    }
    
    
    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * @return string
     */
    public function pattern(): string
    {
        return "{$this->modulesPath()}/*/*ServiceProvider.php";
    }
    
    
    /**
     * @return string
     */
    public function modulesPath(): string
    {
        $root    = dirname(__DIR__, 3);
        $section = self::TYPE_MAPPING[$this->type]['dir'];
        
        return "$root/$section/Modules";
    }
    
    
    /**
     * @return string
     */
    public function namespacePrefix(): string
    {
        return self::TYPE_MAPPING[$this->type]['namespace'] . '\Modules';
    }
}