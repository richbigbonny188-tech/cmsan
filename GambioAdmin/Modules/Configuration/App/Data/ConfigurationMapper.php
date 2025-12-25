<?php
/* --------------------------------------------------------------
   ConfigurationMapper.php 2021-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data;

use Gambio\Admin\Modules\Configuration\Model\Entities\Category;
use Gambio\Admin\Modules\Configuration\Model\Entities\Configuration;
use Gambio\Admin\Modules\Configuration\Model\Entities\Group;
use Gambio\Admin\Modules\Configuration\Model\Entities\Tag;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Admin\Modules\Configuration\Model\ValueObjects\Link;
use Gambio\Admin\Modules\Configuration\Services\ConfigurationFactory;

/**
 * Class ConfigurationMapper
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Repositories
 */
class ConfigurationMapper
{
    /**
     * @var ConfigurationFactory
     */
    private $factory;
    
    
    /**
     * ConfigurationMapper constructor.
     *
     * @param ConfigurationFactory $factory
     */
    public function __construct(ConfigurationFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $data
     *
     * @return Type
     */
    public function mapType(array $data): Type
    {
        return $this->factory->createType($data['id'], $data['params']);
    }
    
    
    /**
     * @param array $data
     *
     * @return Link
     */
    public function mapLink(array $data): Link
    {
        return $this->factory->createLink($data['label'], $data['link'], $data['buttonText'], $data['newWindow']);
    }
    
    
    /**
     * @param array $data
     *
     * @return Tag
     */
    public function mapTag(array $data): Tag
    {
        return $this->factory->createTag($data['id'], $data['label']);
    }
    
    
    /**
     * @param array $data
     *
     * @return Category
     */
    public function mapCategory(array $data): Category
    {
        return $this->factory->createCategory($data['id'], $data['label']);
    }
    
    
    /**
     * @param array $data
     *
     * @return Group
     */
    public function mapGroup(array $data): Group
    {
        return $this->factory->createGroup($data['id'],
                                           $data['label'],
                                           $this->factory->createConfigurations(...$data['configurations']),
                                           $this->factory->createLinks(...$data['links']));
    }
    
    
    /**
     * @param array $jsonData
     * @param Type  $type
     * @param mixed $value
     *
     * @return Configuration
     */
    public function mapConfiguration(array $jsonData, Type $type, $value): Configuration
    {
        return $this->factory->createConfiguration($jsonData['key'],
                                                   $value,
                                                   $jsonData['label'],
                                                   $jsonData['tooltip'],
                                                   $type,
                                                   $this->factory->createTags(...($jsonData['tags'] ?? [])));
    }
}