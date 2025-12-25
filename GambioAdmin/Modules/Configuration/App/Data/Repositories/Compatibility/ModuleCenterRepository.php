<?php
/* --------------------------------------------------------------
 ModuleCenterRepository.php 2020-10-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Repositories\Compatibility;

use Exception;
use Gambio\Admin\Modules\Configuration\App\Data\ConfigurationMapper;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\ConfigurationDbReader;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\TypeRepository;
use Gambio\Admin\Modules\Configuration\Model\Entities\Configuration;

/**
 * Class ModuleCenterRepository
 * @package    Gambio\Admin\Modules\Configuration\App\Data\Repositories\Compatibility
 * @deprecated Only used in admin/modules.php and no one else should depend on this class!
 */
class ModuleCenterRepository
{
    /**
     * @var ConfigurationDbReader
     */
    private $reader;
    
    /**
     * @var ConfigurationMapper
     */
    private $mapper;
    
    /**
     * @var TypeRepository
     */
    private $typeRepository;
    
    
    /**
     * ModuleCenterRepository constructor.
     *
     * @param ConfigurationDbReader $reader
     * @param ConfigurationMapper   $mapper
     * @param TypeRepository        $typeRepository
     */
    public function __construct(
        ConfigurationDbReader $reader,
        ConfigurationMapper $mapper,
        TypeRepository $typeRepository
    ) {
        $this->reader         = $reader;
        $this->mapper         = $mapper;
        $this->typeRepository = $typeRepository;
    }
    
    
    /**
     * Returns the configuration for the provided key.
     *
     * If nothing was found, a configuration is returned with
     * error information as value.
     *
     * @param string $key
     *
     * @return Configuration
     */
    public function getByKey(string $key): Configuration
    {
        try {
            $data            = $this->reader->getConfigurationDataByKey($key);
            $data['type']    = $data['type'] ?? 'text';
            $data['label']   = '';
            $data['tooltip'] = '';
            $value           = $data['value'];
            $type            = $this->typeRepository->getTypeByIdAndParams($data['type'], ['value' => $value]);
        } catch (Exception $e) {
            $data  = [
                'key'     => $key,
                'label'   => $key,
                'tooltip' => $key,
                'type'    => 'textarea',
                'tags'    => [],
            ];
            $type  = $this->mapper->mapType(['id' => 'text', 'params' => []]);
            $value = $e->getMessage();
        }
        
        return $this->mapper->mapConfiguration($data, $type, $value);
    }
}