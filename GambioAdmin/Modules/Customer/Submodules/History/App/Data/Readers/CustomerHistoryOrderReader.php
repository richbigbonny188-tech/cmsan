<?php
/*--------------------------------------------------------------
   CustomerHistoryOrderReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryFactory;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReader;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\Collections\CustomerHistoryEntryDtos;

/**
 * Class CustomerHistoryOrderReader
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers
 */
class CustomerHistoryOrderReader implements CustomerHistoryReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @var CustomerHistoryFactory
     */
    private CustomerHistoryFactory $factory;
    
    
    /**
     * @param Connection             $connection
     * @param CustomerHistoryFactory $factory
     */
    public function __construct(Connection $connection, CustomerHistoryFactory $factory)
    {
        $this->connection = $connection;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function getCustomerHistoryEntries(CustomerId $id): CustomerHistoryEntryDtos
    {
        $result  = [];
        $entries = $this->connection->createQueryBuilder()
            ->select('orders_id, date_purchased')
            ->from('orders')
            ->groupBy('orders_id, date_purchased')
            ->where('customers_id = :customers_id')
            ->setParameter('customers_id', $id->value())
            ->executeQuery()
            ->fetchAllAssociative();
        
        if (count($entries)) {
            foreach ($entries as $entry) {
                $payload = [
                    'orders_id' => (int)$entry['orders_id'],
                ];
                
                $result[] = $this->factory->createCustomerHistoryEntryDto($id->value(),
                                                                          $payload,
                                                                          $this->getType(),
                                                                          new DateTimeImmutable($entry['date_purchased']));
            }
        }
        
        return $this->factory->createCustomerHistoryEntryDtos(...$result);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'orders';
    }
}