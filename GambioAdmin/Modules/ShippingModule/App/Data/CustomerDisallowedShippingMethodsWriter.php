<?php
/*--------------------------------------------------------------
   CustomerDisallowedShippingMethodsWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\ShippingModule\Model\ValueObjects\ShippingMethodId;
use Gambio\Admin\Modules\ShippingModule\Services\Exceptions\CustomerDoesNotExistException;

/**
 * Class CustomerDisallowedShippingMethodsWriter
 *
 * @package Gambio\Admin\Modules\ShippingModule\App\Data
 */
class CustomerDisallowedShippingMethodsWriter
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
     * @param int              $customerId
     * @param ShippingMethodId ...$methodsIds
     *
     * @return void
     *
     * @throws CustomerDoesNotExistException
     * @throws Exception
     */
    public function setDisallowedShippingMethods(int $customerId, ShippingMethodId ...$methodsIds): void
    {
        $result = $this->connection->createQueryBuilder()
            ->select('shipping_unallowed')
            ->from('customers')
            ->andWhere('customers_id = :customers_id')
            ->setParameter('customers_id', $customerId)
            ->executeQuery()
            ->fetchAllNumeric();
        
        if (count($result) === 0) {
            throw CustomerDoesNotExistException::withId($customerId);
        }
        
        $disallowedShippingMethods = array_map(fn(ShippingMethodId $id): string => $id->value(), $methodsIds);
        $disallowedShippingMethods = implode(',', $disallowedShippingMethods);
        
        $this->connection->createQueryBuilder()
            ->update('customers')
            ->set('shipping_unallowed', ':shipping_unallowed')
            ->setParameter('shipping_unallowed', $disallowedShippingMethods)
            ->andWhere('customers_id = :customers_id')
            ->setParameter('customers_id', $customerId)
            ->executeQuery();
    }
}