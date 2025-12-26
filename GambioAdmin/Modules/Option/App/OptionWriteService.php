<?php
/* --------------------------------------------------------------
   OptionWriteService.php 2023-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App;

use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionIds;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionRepository as OptionRepositoryInterface;
use Gambio\Admin\Modules\Option\Services\OptionWriteService as OptionWriteServiceInterface;
use Webmozart\Assert\Assert;

/**
 * Class OptionWriteService
 *
 * @package Gambio\Admin\Modules\Option\App
 */
class OptionWriteService implements OptionWriteServiceInterface
{
    /**
     * @var OptionRepositoryInterface
     */
    private $repository;
    
    /**
     * @var OptionFactory
     */
    private $factory;
    
    
    /**
     * OptionWriteService constructor.
     *
     * @param OptionRepositoryInterface $repository
     * @param OptionFactory             $factory
     */
    public function __construct(OptionRepositoryInterface $repository, OptionFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createOption(
        OptionDetails $details,
        NewOptionValues $newOptionValues,
        OptionType $type,
        int $sortOrder
    ): OptionId {
        return $this->repository->createOption($details,
                                               $newOptionValues,
                                               $type,
                                               $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleOptions(array ...$creationArgs): OptionIds
    {
        foreach ($creationArgs as $index => $args) {
            Assert::isInstanceOf($args[0],
                                 OptionDetails::class,
                                 'First arguments need to be instance of "' . OptionDetails::class . '". Index: '
                                 . $index);
            Assert::isInstanceOf($args[1],
                                 NewOptionValues::class,
                                 'Second arguments need to be instance of "' . OptionDetails::class . '". Index: '
                                 . $index);
            Assert::isInstanceOf($args[2], OptionType::class, 'Third arguments need to be instance of %2$s Index: ' . $index);
            Assert::integer($args[3], 'Fourth arguments need to be an integer. Index: ' . $index);
        }
        
        return $this->repository->createMultipleOptions(...$creationArgs);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeOptions(Option ...$options): void
    {
        $this->repository->storeOptions(...$options);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteOptions(int ...$optionIds): void
    {
        $ids = array_map([$this->factory, 'createOptionId'], $optionIds);
        
        $this->repository->deleteOptions(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeOptionsSortOrder(Option ...$options): void
    {
        $this->repository->storeOptionsSortOrder(...$options);
    }
}