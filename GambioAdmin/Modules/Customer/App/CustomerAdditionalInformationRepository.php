<?php
/*--------------------------------------------------------------
   CustomerAdditionalInformationRepository.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;

/**
 * Class CustomerAdditionalInformationRepository
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerAdditionalInformationRepository
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
     * @param string $datetimeFormat
     * @param int    ...$customerIds
     *
     * @return array
     *
     * @throws CustomerDoesNotExistException
     * @throws DBALException
     * @throws Exception
     */
    public function getAdditionalInformation(string $datetimeFormat, int ...$customerIds): array
    {
        if (count($customerIds) === 0) {
            return [];
        }
        
        // "?" in query will be set to $customerIds values in statement execution
        $questionMarks = array_map(fn(): string => '?', range(1, count($customerIds)));
        $questionMarks = implode(',', $questionMarks);
        
        $query = <<<SQL
            SELECT
                   `c`.`customers_id`,
                   `c`.`customers_date_added` AS 'date_added',
                   `ci`.`customers_info_date_of_last_logon` AS 'last_logon'
            FROM `customers` AS c
            LEFT JOIN customers_info AS ci ON `c`.`customers_id`=`ci`.`customers_info_id`
            WHERE `c`.`customers_id` IN ($questionMarks);
        SQL;
        
        $dbResult = $this->connection->prepare($query)->executeQuery($customerIds);
        
        if ($dbResult->rowCount() !== count($customerIds)) {
            throw new CustomerDoesNotExistException(sprintf('One or more customer id\'s does not exist: %s',
                                                            implode(',', $customerIds)));
        }
        
        $result = [];
        
        while ($row = $dbResult->fetchAssociative()) {
            $result[] = [
                'customerId' => (int)$row['customers_id'],
                'dateAdded'  => (new DateTimeImmutable($row['date_added'] ??
                                                       '1000-01-01 00:00:00'))->format($datetimeFormat),
                'lastLogon'  => (new DateTimeImmutable($row['last_logon'] ??
                                                       '1000-01-01 00:00:00'))->format($datetimeFormat),
            ];
        }
        
        return $result;
    }
}