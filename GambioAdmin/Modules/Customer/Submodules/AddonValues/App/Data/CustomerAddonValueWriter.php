<?php
/* --------------------------------------------------------------
   CustomerAddonValueWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueKey;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CreationOfCustomerAddonValueFailedException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueAlreadyExistsException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\DeletionOfCustomerAddonValueFailedException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\StorageOfCustomerAddonValueFailedException;

/**
 * Class CustomerAddonValueWriter
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\Data
 */
class CustomerAddonValueWriter
{
    /**
     *
     */
    private const LEGACY_CONTAINER_TYPE = 'CustomerInterface';
    
    
    /**
     * @var Connection
     */
    private Connection $db;
    
    
    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param CustomerId            $customerId
     * @param CustomerAddonValueKey $key
     * @param string                $value
     *
     * @return void
     *
     * @throws CreationOfCustomerAddonValueFailedException
     * @throws CustomerAddonValueAlreadyExistsException
     */
    public function createCustomerAddonValue(CustomerId $customerId, CustomerAddonValueKey $key, string $value): void
    {
        try {
            $dbData = $this->db->createQueryBuilder()
                ->select('*')
                ->from('addon_values_storage')
                ->where('container_type = :containerType')
                ->andWhere('container_id = :customerId')
                ->andWhere('addon_key = :key')
                ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
                ->setParameter('customerId', $customerId->value())
                ->setParameter('key', $key->value())
                ->executeQuery()
                ->fetchAssociative();
            
            if ($dbData !== false) {
                throw CustomerAddonValueAlreadyExistsException::forGivenCustomerAndKey($customerId->value(),
                                                                                       $key->value());
            }
            
            $this->db->createQueryBuilder()
                ->insert('addon_values_storage')
                ->setValue('container_type', ':containerType')
                ->setValue('container_id', ':customerId')
                ->setValue('addon_key', ':key')
                ->setValue('addon_value', ':value')
                ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
                ->setParameter('customerId', $customerId->value())
                ->setParameter('key', $key->value())
                ->setParameter('value', $value)
                ->executeQuery();
        } catch (CustomerAddonValueAlreadyExistsException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw CreationOfCustomerAddonValueFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param array ...$creationArguments
     *
     * @return void
     *
     * @throws CreationOfCustomerAddonValueFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createCustomerAddonValues(array ...$creationArguments): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($creationArguments as [$customerId, $key, $value]) {
                $this->createCustomerAddonValue($customerId, $key, $value);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            if ($exception instanceof CreationOfCustomerAddonValueFailedException
                || $exception instanceof CustomerAddonValueAlreadyExistsException) {
                throw $exception;
            }
            
            throw CreationOfCustomerAddonValueFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerAddonValue ...$customerAddonValues
     *
     * @return void
     *
     * @throws StorageOfCustomerAddonValueFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeCustomerAddonValues(CustomerAddonValue ...$customerAddonValues): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($customerAddonValues as $customerAddonValue) {
                $this->db->createQueryBuilder()
                    ->update('addon_values_storage')
                    ->set('addon_value', ':value')
                    ->where('container_type = :containerType')
                    ->andWhere('container_id = :customerId')
                    ->andWhere('addon_key = :key')
                    ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
                    ->setParameter('customerId', $customerAddonValue->customerId())
                    ->setParameter('key', $customerAddonValue->key())
                    ->setParameter('value', $customerAddonValue->value())
                    ->executeQuery();
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            throw StorageOfCustomerAddonValueFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerAddonValueId ...$customerAddonValueIds
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteCustomerAddonValuesByIds(CustomerAddonValueId ...$customerAddonValueIds): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($customerAddonValueIds as $customerAddonValueId) {
                $this->db->createQueryBuilder()
                    ->delete('addon_values_storage')
                    ->where('container_type = :containerType')
                    ->andWhere('container_id = :customerId')
                    ->andWhere('addon_key = :key')
                    ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
                    ->setParameter('customerId', $customerAddonValueId->customerId())
                    ->setParameter('key', $customerAddonValueId->key())
                    ->executeQuery();
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            throw DeletionOfCustomerAddonValueFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerAddonValueKey ...$keys
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteCustomerAddonValuesByKeys(CustomerAddonValueKey ...$keys): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($keys as $key) {
                $this->db->createQueryBuilder()
                    ->delete('addon_values_storage')
                    ->where('container_type = :containerType')
                    ->andWhere('addon_key = :key')
                    ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
                    ->setParameter('key', $key->value())
                    ->executeQuery();
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            throw DeletionOfCustomerAddonValueFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param CustomerId ...$customerIds
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteCustomerAddonValuesByCustomerIds(CustomerId ...$customerIds): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($customerIds as $customerId) {
                $this->db->createQueryBuilder()
                    ->delete('addon_values_storage')
                    ->where('container_type = :containerType')
                    ->andWhere('container_id = :customerId')
                    ->setParameter('containerType', self::LEGACY_CONTAINER_TYPE)
                    ->setParameter('customerId', $customerId->value())
                    ->executeQuery();
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            throw DeletionOfCustomerAddonValueFailedException::becauseOfException($exception);
        }
    }
}