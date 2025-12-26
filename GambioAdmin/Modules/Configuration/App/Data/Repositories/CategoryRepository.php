<?php
/* --------------------------------------------------------------
   CategoryRepository.php 2020-08-18
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
use Gambio\Admin\Modules\Configuration\App\Data\Readers\CategoryJsonReader;
use Gambio\Admin\Modules\Configuration\Model\Collections\Categories;
use Gambio\Admin\Modules\Configuration\Services\ConfigurationFactory;
use Gambio\Admin\Modules\Configuration\Services\Interfaces\CategoryRepositoryInterface;

/**
 * Class CategoryRepository
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Repositories
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var CategoryJsonReader
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
     * CategoryRepository constructor.
     *
     * @param CategoryJsonReader   $reader
     * @param ConfigurationMapper  $mapper
     * @param ConfigurationFactory $factory
     */
    public function __construct(CategoryJsonReader $reader, ConfigurationMapper $mapper, ConfigurationFactory $factory)
    {
        $this->reader  = $reader;
        $this->mapper  = $mapper;
        $this->factory = $factory;
    }
    
    
    /**
     * @return Categories
     */
    public function getAllCategories(): Categories
    {
        $categoriesData = $this->reader->getCategoriesData();
        $categories     = array_map([$this->mapper, 'mapCategory'], $categoriesData);
        
        return $this->factory->createCategories(...$categories);
    }
}