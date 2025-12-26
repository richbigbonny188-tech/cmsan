<?php
/* --------------------------------------------------------------
   WithdrawalReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Withdrawal\Model\ValueObjects\WithdrawalId;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\WithdrawalNotFoundException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class WithdrawalReader
 *
 * @package Gambio\Admin\Modules\Withdrawal\App\Data
 */
class WithdrawalReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * WithdrawalReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param WithdrawalId $id
     *
     * @return array
     *
     * @throws WithdrawalNotFoundException
     * @throws Exception
     */
    public function getWithdrawalDataById(WithdrawalId $id): array
    {
        $withdrawalData = $this->db->createQueryBuilder()
            ->select('*')
            ->from('withdrawals')
            ->where('withdrawal_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($withdrawalData === false) {
            throw WithdrawalNotFoundException::forId($id->value());
        }
        
        return $withdrawalData;
    }
    
    
    /**
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function getFilteredWithdrawalsData(Filters $filters, Sorting $sorting, Pagination $pagination): array
    {
        $query = $this->db->createQueryBuilder()->select('*')->from('withdrawals');
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        return $query->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getWithdrawalsTotalCount(Filters $filters): int
    {
        $query = $this->db->createQueryBuilder()->select('*')->from('withdrawals');
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getAllWithdrawalsData(): array
    {
        return $this->db->createQueryBuilder()->select('*')->from('withdrawals')->executeQuery()->fetchAllAssociative();
    }
}