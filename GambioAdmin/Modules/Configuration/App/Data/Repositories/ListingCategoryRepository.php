<?php
/* --------------------------------------------------------------
   ListingCategoryRepository.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Repositories;

use Gambio\Admin\Modules\Configuration\Model\Collections\ListingCategories;
use Gambio\Admin\Modules\Configuration\Services\ConfigurationFactory;
use Gambio\Admin\Modules\Configuration\Services\Interfaces\ListingCategoryRepositoryInterface;

/**
 * Class ListingCategoryRepository
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Repositories
 */
class ListingCategoryRepository implements ListingCategoryRepositoryInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    
    /**
     * @var ConfigurationFactory
     */
    private $factory;
    
    
    /**
     * ListingCategoryRepository constructor.
     *
     * @param CategoryRepository   $categoryRepository
     * @param GroupRepository      $groupRepository
     * @param ConfigurationFactory $factory
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        GroupRepository $groupRepository,
        ConfigurationFactory $factory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->groupRepository    = $groupRepository;
        $this->factory            = $factory;
    }
    
    
    /**
     * @return ListingCategories
     */
    public function getAllListingCategories(): ListingCategories
    {
        $listingCategories = [];
        $categories        = $this->categoryRepository->getAllCategories();
        foreach ($categories as $category) {
            $groups              = $this->groupRepository->getAllGroupsByCategoryId($category->id());
            $listingCategories[] = $this->factory->createListingCategory($category, $groups);
        }
        
        return $this->factory->createListingCategories(...$listingCategories);
    }
}