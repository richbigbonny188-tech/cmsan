<?php
/*--------------------------------------------------------------
   NewsletterWriter.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;

/**
 * Class NewsletterWriter
 *
 * @package Gambio\Admin\Modules\Newsletter\App\Data
 */
class NewsletterWriter
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
     * @param CustomerId $customerId
     * @param CustomerId $adminId
     *
     * @return void
     * @throws Exception
     */
    public function subscribe(CustomerId $customerId, CustomerId $adminId): void
    {
        if ($this->customerIsAlreadySubscribed($customerId)) {
            return;
        }
        
        $query = '
            REPLACE INTO `newsletter_recipients` (
                customers_email_address, customers_id, customers_status, customers_firstname, customers_lastname,
                mail_status, mail_key, date_added, created_by_admin
            )
            SELECT
                   customers_email_address, customers_id, customers_status, customers_firstname,
                   customers_lastname, 1 AS "mail_status", :key AS "mail_key", NOW(), :admin_id AS "created_by_admin"
            FROM `customers` WHERE customers_id = :customers_id
        ';
        
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('key', $this->randomString(32));
        $stmt->bindValue('customers_id', $customerId->value());
        $stmt->bindValue('admin_id', $adminId->value());
        $stmt->executeQuery();
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return void
     * @throws Exception
     */
    public function unsubscribe(CustomerId $customerId): void
    {
        $this->connection->createQueryBuilder()
            ->delete('newsletter_recipients')
            ->where('customers_id=:customers_id')
            ->setParameter('customers_id', $customerId->value())
            ->executeQuery();
    }
    
    
    /**
     * Changes the `customers_status`
     *
     * @param CustomerId    $customerId
     * @param CustomerGroup $customerGroup
     *
     * @return void
     */
    public function changeCustomerGroup(CustomerId $customerId, CustomerGroup $customerGroup): void
    {
        $this->connection->createQueryBuilder()
            ->update('newsletter_recipients')
            ->set('customers_status', ':customerGroup')
            ->where('customers_id = :customersId')
            ->setParameter('customerGroup', $customerGroup->id())
            ->setParameter('customersId', $customerId->value())
            ->executeQuery();
    }
    
    
    /**
     * @param int $length
     *
     * @return string
     * @throws \Exception
     */
    private function randomString(int $length): string
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        
        while ($length--) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return bool
     * @throws Exception
     */
    private function customerIsAlreadySubscribed(CustomerId $customerId): bool
    {
        $result = $this->connection->createQueryBuilder()
            ->select('customers_id')
            ->from('newsletter_recipients')
            ->where('mail_status = :mail_status')
            ->setParameter('mail_status', '1')
            ->orderBy('customers_id')
            ->groupBy('customers_id')
            ->andWhere('customers_id = :customers_id')
            ->setParameter('customers_id', $customerId->value())
            ->executeQuery()
            ->rowCount();
        
        return $result !== 0;
    }
}