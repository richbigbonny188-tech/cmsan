<?php
/*--------------------------------------------------------------
   CustomerHistoryNewsletterReader.php 2022-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryFactory;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReader;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\DTO\Collections\CustomerHistoryEntryDtos;

/**
 * Class CustomerHistoryNewsletterReader
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\App\Data\Readers
 */
class CustomerHistoryNewsletterReader implements CustomerHistoryReader
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
     * @throws Exception
     */
    public function getCustomerHistoryEntries(CustomerId $id): CustomerHistoryEntryDtos
    {
        $result  = [];
        $entries = $this->connection->createQueryBuilder()
            ->select('date_added, created_by_admin')
            ->from('newsletter_recipients')
            ->groupBy('date_added')
            ->where('customers_id = :customers_id')
            ->setParameter('customers_id', $id->value())
            ->andWhere('mail_status = :mail_status')
            ->setParameter('mail_status', 1)
            ->executeQuery()
            ->fetchAllAssociative();
        
        if (count($entries)) {
            foreach ($entries as $entry) {
                $payload = [
                    'action'         => 'signed up for receiving newsletters',
                    'createdByAdmin' => (int)$entry['created_by_admin'] >= 1,
                ];
                
                if ($payload['createdByAdmin']) {
                    $payload['adminId'] = (int)$entry['created_by_admin'];
                }
                
                $result[] = $this->factory->createCustomerHistoryEntryDto($id->value(),
                                                                          $payload,
                                                                          $this->getType(),
                                                                          new DateTimeImmutable($entry['date_added']));
            }
        }
        
        return $this->factory->createCustomerHistoryEntryDtos(...$result);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'newsletter';
    }
}