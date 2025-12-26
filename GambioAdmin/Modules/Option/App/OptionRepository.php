<?php
/* --------------------------------------------------------------
   OptionsRepository.php 2021-05-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App;

use Gambio\Admin\Modules\Option\App\Data\OptionMapper;
use Gambio\Admin\Modules\Option\App\Data\OptionReader;
use Gambio\Admin\Modules\Option\App\Data\OptionWriter;
use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionIds;
use Gambio\Admin\Modules\Option\Model\Collections\Options;
use Gambio\Admin\Modules\Option\Model\Events\OptionCreated;
use Gambio\Admin\Modules\Option\Model\Events\OptionDeleted;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionOperationPermitter;
use Gambio\Admin\Modules\Option\Services\OptionRepository as OptionsRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class OptionRepository
 *
 * @package Gambio\Admin\Modules\Option\App
 */
class OptionRepository extends AbstractEventDispatchingRepository implements OptionsRepositoryInterface
{
    /**
     * @var OptionMapper
     */
    private $mapper;
    
    /**
     * @var OptionReader
     */
    private $reader;
    
    /**
     * @var OptionWriter
     */
    private $writer;
    
    /**
     * @var OptionOperationPermitter[]
     */
    private $permitters;
    
    
    /**
     * OptionRepository constructor.
     *
     * @param OptionMapper             $mapper
     * @param OptionReader             $reader
     * @param OptionWriter             $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        OptionMapper $mapper,
        OptionReader $reader,
        OptionWriter $writer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper     = $mapper;
        $this->reader     = $reader;
        $this->writer     = $writer;
        $this->permitters = [];
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterOptions(Filters $filters, Sorting $sorting, Pagination $pagination): Options
    {
        return $this->mapper->mapOptions($this->reader->getFilteredOptionsData($filters, $sorting, $pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getOptionsTotalCount(Filters $filters): int
    {
        return $this->reader->getOptionsTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllOptions(): Options
    {
        return $this->mapper->mapOptions($this->reader->getAllOptionsData());
    }
    
    
    /**
     * @inheritDoc
     */
    public function getOptionById(OptionId $id): Option
    {
        $data = $this->reader->getOptionDataById($id);
        if (count($data) !== 1) {
            throw OptionDoesNotExistException::forOptionId($id->value());
        }
        
        return $this->mapper->mapOption(array_pop($data));
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
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations([$details, $newOptionValues, $type, $sortOrder]) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $idValue = $this->writer->createOption($details, $newOptionValues, $type, $sortOrder);
        $id      = $this->mapper->mapOptionId($idValue);
        
        $this->dispatchEvent(OptionCreated::create($id));
        
        return $id;
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
            Assert::isInstanceOf($args[2],
                                 OptionType::class,
                                 'Third arguments need to be instance of "' . OptionType::class . '". Index: '
                                 . $index);
            Assert::integer($args[3], 'Fourth arguments need to be an integer. Index: ' . $index);
        }
        
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations(...$creationArgs) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $idValues = $this->writer->createMultipleOptions(...$creationArgs);
        $ids      = $this->mapper->mapOptionIds(...$idValues);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(OptionCreated::create($id));
        }
        
        return $ids;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeOptions(Option ...$options): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsStorages(...$options) === false) {
                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
            }
        }
        
        $this->writer->storeOptions(...$options);
        
        foreach ($options as $option) {
            $this->dispatchEntityEvents($option);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteOptions(OptionId ...$ids): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsDeletions(...$ids) === false) {
                throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
            }
        }
        
        $this->writer->deleteOptions(...$ids);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(OptionDeleted::create($id));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(OptionOperationPermitter $permitter): void
    {
        $this->permitters[get_class($permitter)] = $permitter;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeOptionsSortOrder(Option ...$options): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsStorages(...$options) === false) {
                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
            }
        }
        
        $this->writer->storeOptionsSortOrder(...$options);
        
        foreach ($options as $option) {
            $this->dispatchEntityEvents($option);
        }
    }
}