<?php
/*--------------------------------------------------------------
   CustomerConfigurationProvider.php 2022-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Gambio\Core\Configuration\Services\ConfigurationService;
use Webmozart\Assert\Assert;

/**
 * Class CustomerConfigurationProvider
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerConfigurationProvider
{
    private ConfigurationService $service;
    
    /**
     * @var string[]
     */
    private array $keys;
    
    
    /**
     * @param ConfigurationService $service
     * @param string               ...$keys
     */
    public function __construct(
        ConfigurationService $service,
        string               ...$keys
    ) {
        $this->service = $service;
        $this->keys    = $keys;
        
        Assert::isNonEmptyList($keys, sprintf('%s\'s keys can\'t be empty', self::class));
    }
    
    
    /**
     * @return array
     */
    public function getConfigurations(): array
    {
        $result = [];
    
        foreach (array_map([$this->service, 'find'], $this->keys) as $value) {
    
            $key          = $this->removeNamespaceFromKey($value->key());
            $result[$key] = $this->parseValue($value->value());
        }
        
        return $result;
    }
    
    
    /**
     * @param string $key
     *
     * @return string
     */
    private function removeNamespaceFromKey(string $key): string
    {
        $namespaces = ['#^(gm_)?configuration/#'];
        
        return preg_replace($namespaces, '', $key);
    }
    
    
    /**
     * @param string $value
     *
     * @return mixed
     */
    private function parseValue(string $value)
    {
        if (is_numeric($value)) {
            
            return (int)$value;
        }
    
        if (in_array(strtolower($value), ['true', 'false'])) {
            
            return strtolower($value) === 'true';
        }
        
        return $value;
    }
}