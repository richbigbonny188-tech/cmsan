<?php
/* --------------------------------------------------------------
 DSGVORepository.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DSGVO\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\DSGVO\Models\Entities\AdminLog as Log;
use Gambio\Admin\Modules\DSGVO\Models\Interfaces\AdminLog;
use Gambio\Admin\Modules\DSGVO\Services\DSGVORepository as Repository;
use Webmozart\Assert\Assert;

/**
 * Class DSGVORepository
 *
 * @package Gambio\Admin\Modules\DSGVO\App\Data
 */
class DSGVORepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * DSGVORepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdminActivityLog(): AdminLog
    {
        Assert::keyExists($_SESSION, 'customer_id');
        Assert::keyExists($_SESSION, 'customer_first_name');
        Assert::keyExists($_SESSION, 'customer_last_name');
        
        $sessionId  = session_id();
        $customerId = (int)$_SESSION['customer_id'];
        
        return new Log(
            $sessionId,
                       $customerId,
                       $_SESSION['customer_first_name'],
                       $_SESSION['customer_last_name'],
            $this->getEmail($customerId)
        );
    }
    
    
    /**
     * Returns the email address of the currently logged in customer.
     * Consecutive calls fetches the address from the session.
     *
     * @param int $customerId
     *
     * @return string
     */
    private function getEmail(int $customerId): string
    {
        if (!array_key_exists('email', $_SESSION)) {
            $_SESSION['email'] = $this->getEmailFor($customerId);
        }
        
        return $_SESSION['email'];
    }
    
    
    /**
     * Fetches the email address that is attached to the given customer id.
     *
     * @param int $customerId
     *
     * @return string
     * @throws Exception
     */
    private function getEmailFor(int $customerId): string
    {
        $qb = $this->connection->createQueryBuilder();
        
        $key   = 'customers_email_address';
        $where = "customers_id = {$qb->createNamedParameter($customerId)}";
        $qb->select($key)->from('customers')->where($where);
        
        $result = $qb->executeQuery()->fetchAssociative();
        
        return $result[$key];
    }
}