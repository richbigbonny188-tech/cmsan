<?php
/* --------------------------------------------------------------
   TagRepository.php 2020-08-18
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
use Gambio\Admin\Modules\Configuration\App\Data\Readers\TagJsonReader;
use Gambio\Admin\Modules\Configuration\Model\Collections\Tags;
use Gambio\Admin\Modules\Configuration\Services\ConfigurationFactory;
use Gambio\Admin\Modules\Configuration\Services\Interfaces\TagRepositoryInterface;

/**
 * Class TagRepository
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Repositories
 */
class TagRepository implements TagRepositoryInterface
{
    /**
     * @var TagJsonReader
     */
    private $reader;
    
    /**
     * @var ConfigurationMapper
     */
    private $mapper;
    
    /**
     * @var ConfigurationFactory
     */
    private $factory;
    
    
    /**
     * TagRepository constructor.
     *
     * @param TagJsonReader        $reader
     * @param ConfigurationMapper  $mapper
     * @param ConfigurationFactory $factory
     */
    public function __construct(TagJsonReader $reader, ConfigurationMapper $mapper, ConfigurationFactory $factory)
    {
        $this->reader  = $reader;
        $this->mapper  = $mapper;
        $this->factory = $factory;
    }
    
    
    /**
     * @return Tags
     */
    public function getAllTags(): Tags
    {
        $tagsData = $this->reader->getTagsData();
        $tags     = array_map([$this->mapper, 'mapTag'], $tagsData);
        
        return $this->factory->createTags(...$tags);
    }
}