<?php
/* --------------------------------------------------------------
   WithdrawalRepository.php 2020-12-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App\Data;

use DateTime;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\WithdrawalIds;
use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;
use Gambio\Admin\Modules\Withdrawal\Model\Events\WithdrawalCreated;
use Gambio\Admin\Modules\Withdrawal\Model\Events\WithdrawalDeleted;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\CustomerDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\OrderDetails;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalRepository as WithdrawalRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class WithdrawalRepository
 *
 * @package Gambio\Admin\Modules\Withdrawal\App\Data
 */
class WithdrawalRepository extends AbstractEventDispatchingRepository implements WithdrawalRepositoryInterface
{
    /**
     * @var WithdrawalMapper
     */
    private $mapper;
    
    /**
     * @var WithdrawalReader
     */
    private $reader;
    
    /**
     * @var WithdrawalWriter
     */
    private $writer;
    
    
    /**
     * WithdrawalRepository constructor.
     *
     * @param WithdrawalMapper         $mapper
     * @param WithdrawalReader         $reader
     * @param WithdrawalWriter         $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        WithdrawalMapper $mapper,
        WithdrawalReader $reader,
        WithdrawalWriter $writer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterWithdrawals(Filters $filters, Sorting $sorting, Pagination $pagination): Withdrawals
    {
        $withdrawalsData = $this->reader->getFilteredWithdrawalsData($filters, $sorting, $pagination);
        
        return $this->mapper->mapWithdrawals($withdrawalsData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getWithdrawalsTotalCount(Filters $criteria): int
    {
        return $this->reader->getWithdrawalsTotalCount($criteria);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllWithdrawals(): Withdrawals
    {
        $withdrawalsData = $this->reader->getAllWithdrawalsData();
        
        return $this->mapper->mapWithdrawals($withdrawalsData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getWithdrawalById(WithdrawalId $id): Withdrawal
    {
        $withdrawalData = $this->reader->getWithdrawalDataById($id);
        
        return $this->mapper->mapWithdrawal($withdrawalData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createWithdrawal(
        OrderDetails $order,
        CustomerDetails $customer,
        string $date = null,
        string $content = '',
        bool $createdByAdmin = true
    ): WithdrawalId {
        $mappedDate = ($date !== null) ? new DateTime($date) : null;
        
        $idValue = $this->writer->createWithdrawal($order, $customer, $mappedDate, $content, $createdByAdmin);
        $id      = $this->mapper->mapWithdrawalId($idValue);
        
        $this->dispatchEvent(WithdrawalCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleWithdrawals(array ...$creationArguments): WithdrawalIds
    {
        Assert::allIsList($creationArguments, 'Provided arguments need to be a list.');
        Assert::allMinCount($creationArguments, 2, 'At least two arguments needed per creation.');
        
        foreach ($creationArguments as $index => $creationArgument) {
            Assert::isInstanceOf($creationArgument[0],
                                 OrderDetails::class,
                                 'Order details need to implement "' . OrderDetails::class . '" interface. Index: '
                                 . $index);
            Assert::isInstanceOf($creationArgument[1],
                                 CustomerDetails::class,
                                 'Customer details need to implement "' . CustomerDetails::class
                                 . '" interface. Index: ' . $index);
            Assert::string($creationArgument[2] ?? '', 'Date must be string or null. Index: ' . $index);
            Assert::string($creationArgument[3] ?? '', 'Content must be string. Index: ' . $index);
            Assert::boolean($creationArgument[4] ?? true, 'Created by admin flag must be boolean. Index: ' . $index);
            
            $creationArguments[$index][2] = $creationArgument[2] ?? null;
            $creationArguments[$index][2] = ($creationArguments[$index][2]
                                             !== null) ? new DateTime($creationArgument[2]) : null;
            $creationArguments[$index][3] = $creationArgument[3] ?? '';
            $creationArguments[$index][4] = $creationArgument[4] ?? true;
        }
        
        $idValues = $this->writer->createMultipleWithdrawals(...$creationArguments);
        $ids      = $this->mapper->mapWithdrawalIds(...$idValues);
        foreach ($ids as $id) {
            $this->dispatchEvent(WithdrawalCreated::create($id));
        }
        
        return $ids;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeWithdrawals(Withdrawal ...$withdrawals): void
    {
        $this->writer->updateWithdrawals(...$withdrawals);
        foreach ($withdrawals as $withdrawal) {
            $this->dispatchEntityEvents($withdrawal);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteWithdrawals(WithdrawalId ...$ids): void
    {
        $this->writer->deleteWithdrawals(...$ids);
        foreach ($ids as $id) {
            $this->dispatchEvent(WithdrawalDeleted::create($id));
        }
    }
}