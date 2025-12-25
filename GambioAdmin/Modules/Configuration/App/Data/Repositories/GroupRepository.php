<?php
/* --------------------------------------------------------------
   GroupRepository.php 2020-08-18
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
use Gambio\Admin\Modules\Configuration\App\Data\Readers\GroupJsonReader;
use Gambio\Admin\Modules\Configuration\App\Exceptions\ConfigurationDoesNotExist;
use Gambio\Admin\Modules\Configuration\App\Exceptions\TypeDoesNotExist;
use Gambio\Admin\Modules\Configuration\Model\Collections\Groups;
use Gambio\Admin\Modules\Configuration\Model\Entities\Group;
use Gambio\Admin\Modules\Configuration\Services\ConfigurationFactory;

/**
 * Class GroupRepository
 *
 * @package Gambio\Admin\Modules\Configuration\Repositories
 */
class GroupRepository
{
    /**
     * @var GroupJsonReader
     */
    private $groupReader;
    
    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;
    
    /**
     * @var ConfigurationMapper
     */
    private $mapper;
    
    /**
     * @var ConfigurationFactory
     */
    private $adminConfigurationFactory;
    
    
    /**
     * GroupRepository constructor.
     *
     * @param GroupJsonReader         $groupReader
     * @param ConfigurationRepository $configurationRepository
     * @param ConfigurationMapper     $mapper
     * @param ConfigurationFactory    $adminConfigurationFactory
     */
    public function __construct(
        GroupJsonReader $groupReader,
        ConfigurationRepository $configurationRepository,
        ConfigurationMapper $mapper,
        ConfigurationFactory $adminConfigurationFactory
    ) {
        $this->groupReader               = $groupReader;
        $this->configurationRepository   = $configurationRepository;
        $this->mapper                    = $mapper;
        $this->adminConfigurationFactory = $adminConfigurationFactory;
    }
    
    
    /**
     * @param string $categoryId
     *
     * @return Groups
     */
    public function getAllGroupsByCategoryId(string $categoryId): Groups
    {
        $groupsData = $this->groupReader->getGroupDataByCategoryId($categoryId);
        $groups     = array_map([$this, 'mapGroup'], $groupsData);
        
        return $this->adminConfigurationFactory->createGroups(...$groups);
    }
    
    
    /**
     * @param array $groupData
     *
     * @return Group
     */
    private function mapGroup(array $groupData): Group
    {
        foreach ($groupData['configurations'] as $index => $configurationKey) {
            try {
                $groupData['configurations'][$index] = $this->configurationRepository->getConfigurationByKey($configurationKey);
            } catch (ConfigurationDoesNotExist|TypeDoesNotExist $e) {
                unset($groupData['configurations'][$index]);
            }
        }
        $groupData['links'] = array_map([$this->mapper, 'mapLink'], $groupData['links']);
        
        return $this->mapper->mapGroup($groupData);
    }
}