<?php
/*--------------------------------------------------------------
   NextCustomerNumberRepository.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Class NextCustomerNumberRepository
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class NextCustomerNumberRepository
{
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @return int
     * @throws Exception
     */
    public function next(): int
    {
        $this->clearCache();
        
        return $this->fetchCustomersAutoIncrement();
    }
    
    
    /**
     * @return void
     * @throws Exception
     */
    private function clearCache(): void
    {
        $this->connection->executeQuery('ANALYZE TABLE customers');
    }
    
    /**
     * @return int
     * @throws Exception
     */
    private function fetchCustomersAutoIncrement(): int
    {
        $stmt = $this->connection->prepare('select auto_increment from information_schema.TABLES where TABLE_NAME =:tablename and TABLE_SCHEMA=:database_name;');
        $stmt->bindValue('tablename', 'customers');
        $stmt->bindValue('database_name', $this->connection->getDatabase());
        $result = $stmt->executeQuery();
        
        return (int)($result->fetchNumeric()[0]);
    }
}