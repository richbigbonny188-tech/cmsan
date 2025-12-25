<?php
/*--------------------------------------------------------------
   CustomerMemoWriter.php 20223-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 20223 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CreatorId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects\CustomerMemoId;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CreationOfCustomerMemoFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\DeletionOfCustomerMemoFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\StorageOfCustomerMemoFailedException;

/**
 * Class CustomerMemoWriter
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data
 */
class CustomerMemoWriter
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Creates a new customer memo and returns its ID.
     *
     * @param CustomerId $customerId
     * @param CreatorId  $creatorId
     * @param string     $content
     *
     * @return int
     *
     * @throws CreationOfCustomerMemoFailedException
     */
    public function createCustomerMemo(CustomerId $customerId, CreatorId $creatorId, string $content): int
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert('customers_memo')
                ->setValue('customers_id', ':customers_id')
                ->setValue('memo_date', ':memo_date')
                ->setValue('memo_text', ':memo_text')
                ->setValue('poster_id', ':poster_id')
                ->setParameter('customers_id', $customerId->value())
                ->setParameter('memo_date', date('Y-m-d H:i:s'))
                ->setParameter('memo_text', $content)
                ->setParameter('poster_id', $creatorId->value())
                ->executeQuery();
            
            return (int)$this->connection->lastInsertId();
        } catch (Exception $exception) {
            throw CreationOfCustomerMemoFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * Creates multiple customer memos and returns their IDs.
     *
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return int[]
     *
     * @throws CreationOfCustomerMemoFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleCustomerMemos(array ...$creationArguments): array
    {
        try {
            $this->connection->beginTransaction();
            $ids = [];
            
            foreach ($creationArguments as $creationArgument) {
                $ids[] = $this->createCustomerMemo(...$creationArgument);
            }
            $this->connection->commit();
        } catch (Exception $exception) {
            $validException = $exception instanceof CreationOfCustomerMemoFailedException;
            $exception      = $validException ? $exception : CreationOfCustomerMemoFailedException::becauseOfException($exception);
            
            $this->connection->rollBack();
            throw $exception;
        }
        
        return $ids;
    }
    
    
    /**
     * Stores multiple customer memos.
     *
     * @param CustomerMemo ...$customerMemos
     *
     * @throws StorageOfCustomerMemoFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeCustomerMemos(CustomerMemo ...$customerMemos): void
    {
        try {
            $this->connection->beginTransaction();
            array_map([$this, 'storeCustomerMemo'], $customerMemos);
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw StorageOfCustomerMemoFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerMemo $customerMemo
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function storeCustomerMemo(CustomerMemo $customerMemo): void
    {
        $this->connection->createQueryBuilder()
            ->update('customers_memo')
            ->set('memo_text', ':memo_text')
            ->setParameter('memo_text', $customerMemo->content())
            ->where('memo_id = :memo_id')
            ->setParameter('memo_id', $customerMemo->id())
            ->executeQuery();
    }
    
    
    /**
     * Deletes customer memos based on the given customer memo IDs.
     *
     * @param CustomerMemoId ...$ids
     *
     * @throws DeletionOfCustomerMemoFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteCustomerMemosByMemoIds(CustomerMemoId ...$ids): void
    {
        try {
            $this->connection->beginTransaction();
            array_map([$this, 'deleteCustomerMemoById'], $ids);
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw DeletionOfCustomerMemoFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerMemoId $id
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteCustomerMemoById(CustomerMemoId $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('customers_memo')
            ->where('memo_id = :memo_id')
            ->setParameter('memo_id', $id->value())
            ->executeQuery();
    }
    
    
    /**
     * Deletes customer memos based on the given customer IDs.
     *
     * @param CustomerId ...$ids
     *
     * @throws DeletionOfCustomerMemoFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteCustomerMemosByCustomerIds(CustomerId ...$ids): void
    {
        try {
            $this->connection->beginTransaction();
            array_walk($ids, [$this, 'deleteCustomerMemoByCustomerId']);
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            throw DeletionOfCustomerMemoFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerId $id
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteCustomerMemoByCustomerId(CustomerId $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('customers_memo')
            ->where('customers_id = :customers_id')
            ->setParameter('customers_id', $id->value())
            ->executeQuery();
    }
}