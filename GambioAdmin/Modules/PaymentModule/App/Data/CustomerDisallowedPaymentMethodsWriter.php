<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\PaymentModule\Model\ValueObjects\PaymentMethodId;
use Gambio\Admin\Modules\PaymentModule\Services\Exceptions\CustomerDoesNotExistException;

/**
 * Class CustomerDisallowedPaymentMethodsWriter
 *
 * @package Gambio\Admin\Modules\PaymentModule\App\Data
 */
class CustomerDisallowedPaymentMethodsWriter
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
     * @param int             $customerId
     * @param PaymentMethodId ...$methodsIds
     *
     * @return void
     * @throws CustomerDoesNotExistException
     * @throws Exception
     */
    public function setDisallowedPaymentMethods(int $customerId, PaymentMethodId ...$methodsIds): void
    {
        $result = $this->connection->createQueryBuilder()
            ->select('payment_unallowed')
            ->from('customers')
            ->andWhere('customers_id = :customers_id')
            ->setParameter('customers_id', $customerId)
            ->executeQuery()
            ->fetchAllNumeric();
        
        if (count($result) === 0) {
            throw CustomerDoesNotExistException::withId($customerId);
        }
        
        $disallowedPayments = array_map(fn(PaymentMethodId $id): string => $id->value(), $methodsIds);
        $disallowedPayments = implode(',', $disallowedPayments);
        
        $this->connection->createQueryBuilder()
            ->update('customers')
            ->set('payment_unallowed', ':payment_unallowed')
            ->setParameter('payment_unallowed', $disallowedPayments)
            ->andWhere('customers_id = :customers_id')
            ->setParameter('customers_id', $customerId)
            ->executeQuery();
    }
}