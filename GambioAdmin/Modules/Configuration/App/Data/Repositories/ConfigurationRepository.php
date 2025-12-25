<?php
/* --------------------------------------------------------------
   ConfigurationRepository.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Repositories;

use Gambio\Admin\Modules\Configuration\App\Data\ConfigurationMapper;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\ConfigurationDbReader;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\ConfigurationJsonReader;
use Gambio\Admin\Modules\Configuration\App\Exceptions\ConfigurationDoesNotExist;
use Gambio\Admin\Modules\Configuration\App\Exceptions\TypeDoesNotExist;
use Gambio\Admin\Modules\Configuration\Model\Entities\Configuration;

/**
 * Class ConfigurationRepository
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Repositories
 */
class ConfigurationRepository
{
    /**
     * @var ConfigurationJsonReader
     */
    private $configurationJsonReader;
    
    /**
     * @var ConfigurationDbReader
     */
    private $configurationDbReader;
    
    /**
     * @var TypeRepository
     */
    private $typeRepository;
    
    /**
     * @var ConfigurationMapper
     */
    private $mapper;
    
    
    public function __construct(
        ConfigurationJsonReader $configurationJsonReader,
        ConfigurationDbReader $configurationDbReader,
        TypeRepository $typeRepository,
        ConfigurationMapper $mapper
    ) {
        $this->configurationJsonReader = $configurationJsonReader;
        $this->configurationDbReader   = $configurationDbReader;
        $this->typeRepository          = $typeRepository;
        $this->mapper                  = $mapper;
    }
    
    
    /**
     * @param string $key
     *
     * @return Configuration
     *
     * @throws ConfigurationDoesNotExist
     * @throws TypeDoesNotExist
     */
    public function getConfigurationByKey(string $key): Configuration
    {
        try {
            $configJsonData         = $this->configurationJsonReader->getConfigurationDataByKey($key);
            $configJsonData['tags'] = array_map([$this->mapper, 'mapTag'], array_values($configJsonData['tags']));
            $configDbData           = $this->configurationDbReader->getConfigurationDataByKey($key);
            $type                   = $this->typeRepository->getTypeByIdAndParams($configJsonData['type']['id'],
                                                                                  $configJsonData['type']['params']);
        } catch (ConfigurationDoesNotExist|TypeDoesNotExist $e) {
            if (!isset($_GET['debugging'])) {
                throw $e;
            }
            
            $configJsonData = [
                'key'     => $key,
                'label'   => $key,
                'tooltip' => $key,
                'type'    => 'textarea',
                'tags'    => [],
            ];
            $type           = $this->mapper->mapType(['id' => 'textarea', 'params' => []]);
            
            return $this->mapper->mapConfiguration($configJsonData, $type, $e->getMessage());
        }
        
        return $this->mapper->mapConfiguration($configJsonData, $type, $configDbData['value']);
    }
}
