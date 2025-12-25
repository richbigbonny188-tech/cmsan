<?php
/*--------------------------------------------------------------
   AdditionalProductFieldsWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\AdditionalProductField;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldIds;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\StorageOfAdditionalProductFieldsFailedException;

/**
 * Class AdditionalProductFieldsWriter
 *
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\App\Data
 */
class AdditionalProductFieldsWriter
{
    /**
     * @var Connection
     */
    protected $connection;
    
    /**
     * @var array
     */
    protected $languageIdMap = [];
    
    
    /**
     * AdditionalProductFieldsWriter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param ProductId         $productId
     * @param AdditionalFieldId $fieldId
     * @param array             ...$values
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createAdditionalProductField(
        ProductId         $productId,
        AdditionalFieldId $fieldId,
        array             $values
    ): void {
        try {
            $this->connection->beginTransaction();
            
            $this->validateProvidedCreationData($productId, $fieldId, $values);
            
            $this->connection->createQueryBuilder()
                ->insert('additional_field_values')
                ->setValue('additional_field_id', ':additional_field_id')
                ->setValue('item_id', ':product_id')
                ->setParameter('additional_field_id', $fieldId->value())
                ->setParameter('product_id', $productId->value())
                ->executeQuery();
            
            $valueId = (int)$this->connection->lastInsertId();
            
            foreach ($values as $languageCode => $value) {
                $this->connection->createQueryBuilder()
                    ->insert('additional_field_value_descriptions')
                    ->setValue('additional_field_value_id', ':additional_field_value_id')
                    ->setValue('language_id', ':language_id')
                    ->setValue('value', ':value')
                    ->setParameter('additional_field_value_id', $valueId)
                    ->setParameter('language_id', $this->getLanguageIdByCode($languageCode))
                    ->setParameter('value', $value)
                    ->executeQuery();
            }
            
            $this->connection->commit();
        } catch (CreationOfAdditionalProductFieldFailedException $exception) {
            $this->connection->rollBack();
            throw $exception;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw CreationOfAdditionalProductFieldFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param AdditionalProductField ...$additionalProductFields
     *
     * @throws StorageOfAdditionalProductFieldsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeAdditionalProductField(AdditionalProductField ...$additionalProductFields): void
    {
        try {
            $this->connection->beginTransaction();
            
            foreach ($additionalProductFields as $additionalProductField) {
                $languageCodes = array_flip($additionalProductField->values()->languageCodes());
                
                if ($this->providedLanguageCodesExists($languageCodes) === false) {
                    throw StorageOfAdditionalProductFieldsFailedException::invalidLanguageCodesProvided();
                }
                
                $valueId = $this->getAdditionalFieldValueId($additionalProductField->productId(),
                                                            $additionalProductField->id());
                
                foreach ($additionalProductField->values() as $value) {
                    $this->connection->createQueryBuilder()
                        ->update('additional_field_value_descriptions')
                        ->set('value', ':value')
                        ->where('additional_field_value_id = :additional_field_value_id')
                        ->andWhere('language_id = :language_id')
                        ->setParameter('value', $value->value())
                        ->setParameter('language_id', $this->getLanguageIdByCode($value->languageCode()))
                        ->setParameter('additional_field_value_id', $valueId)
                        ->executeQuery();
                }
            }
            
            $this->connection->commit();
        } catch (StorageOfAdditionalProductFieldsFailedException $exception) {
            $this->connection->rollBack();
            throw $exception;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw StorageOfAdditionalProductFieldsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductId          $productId
     * @param AdditionalFieldIds $additionalFieldIds
     *
     * @throws DeletionOfAdditionalProductFieldsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteAdditionalProductField(ProductId $productId, AdditionalFieldIds $additionalFieldIds): void
    {
        try {
            $this->connection->beginTransaction();
            
            foreach ($additionalFieldIds as $additionalFieldId) {
                $valueId = $this->getAdditionalFieldValueId($productId->value(), $additionalFieldId->value());
                
                $this->connection->createQueryBuilder()
                    ->delete('additional_field_value_descriptions')
                    ->where('additional_field_value_id = :additional_field_value_id')
                    ->setParameter('additional_field_value_id', $valueId)
                    ->executeQuery();
                
                $this->connection->createQueryBuilder()
                    ->delete('additional_field_values')
                    ->where('additional_field_value_id = :additional_field_value_id')
                    ->setParameter('additional_field_value_id', $valueId)
                    ->executeQuery();
            }
            
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw DeletionOfAdditionalProductFieldsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ProductId         $productId
     * @param AdditionalFieldId $fieldId
     * @param array             $values
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function validateProvidedCreationData(
        ProductId         $productId,
        AdditionalFieldId $fieldId,
        array             $values
    ): void {
        $this->validateFieldIdIsUnAssigned($productId, $fieldId);
        $this->validateAdditionalFieldIdExists($fieldId);
        $this->validateProductExists($productId);
        
        if ($this->providedLanguageCodesExists($values) === false) {
            throw CreationOfAdditionalProductFieldFailedException::invalidLanguageCodesProvided();
        }
    }
    
    
    /**
     * @param ProductId         $productId
     * @param AdditionalFieldId $fieldId
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function validateFieldIdIsUnAssigned(ProductId $productId, AdditionalFieldId $fieldId): void
    {
        $resultCount = $this->connection->createQueryBuilder()
            ->select('additional_field_value_id')
            ->from('additional_field_values')
            ->where('additional_field_id = :additional_field_id')
            ->andWhere('item_id = :product_id')
            ->setParameter('additional_field_id', $fieldId->value())
            ->setParameter('product_id', $productId->value())
            ->executeQuery()
            ->rowCount();
        
        if ($resultCount !== 0) {
            throw CreationOfAdditionalProductFieldFailedException::valueAlreadyExists($productId, $fieldId);
        }
    }
    
    
    /**
     * @param AdditionalFieldId $fieldId
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function validateAdditionalFieldIdExists(AdditionalFieldId $fieldId): void
    {
        $resultCount = $this->connection->createQueryBuilder()
            ->select('field_key')
            ->from('additional_fields')
            ->where('additional_field_id = :additional_field_id')
            ->setParameter('additional_field_id', $fieldId->value())
            ->executeQuery()
            ->rowCount();
        
        if ($resultCount === 0) {
            throw CreationOfAdditionalProductFieldFailedException::fieldIdDoesNotExists($fieldId);
        }
    }
    
    
    /**
     * @param ProductId $productId
     *
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function validateProductExists(ProductId $productId): void
    {
        $resultCount = $this->connection->createQueryBuilder()
            ->select('products_id')
            ->from('products')
            ->where('products_id = :products_id')
            ->setParameter('products_id', $productId->value())
            ->executeQuery()
            ->rowCount();
        
        if ($resultCount === 0) {
            throw CreationOfAdditionalProductFieldFailedException::productDoesNotExist($productId);
        }
    }
    
    
    /**
     * @param array $values
     *
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    protected function providedLanguageCodesExists(array $values): bool
    {
        $providedLanguageCodes = array_keys($values);
        
        $languageIdsQuery = $this->connection->createQueryBuilder()->select('languages_id')->from('languages');
        
        foreach ($providedLanguageCodes as $index => $languageCode) {
            $method = $index === 0 ? 'where' : 'orWhere';
            
            $languageIdsQuery->$method('code = ?');
            $languageIdsQuery->setParameter($index, $languageCode);
        }
        
        return count($providedLanguageCodes) === $languageIdsQuery->executeQuery()->rowCount();
    }
    
    
    /**
     * @param string $code
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getLanguageIdByCode(string $code): int
    {
        if (isset($this->languageIdMap[$code]) === false) {
            $this->languageIdMap[$code] = (int)$this->connection->createQueryBuilder()
                                                   ->select('languages_id')
                                                   ->from('languages')
                                                   ->where('code = :code')
                                                   ->setParameter('code', $code)
                                                   ->executeQuery()
                                                   ->fetchAssociative()['languages_id'];
        }
        
        return $this->languageIdMap[$code];
    }
    
    
    /**
     * @param int $productId
     * @param int $fieldId
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getAdditionalFieldValueId(int $productId, int $fieldId): int
    {
        return (int)$this->connection->createQueryBuilder()
                        ->select('additional_field_value_id')
                        ->from('additional_field_values')
                        ->where('additional_field_id = :additional_field_id')
                        ->andWhere('item_id = :product_id')
                        ->setParameter('product_id', $productId)
                        ->setParameter('additional_field_id', $fieldId)
                        ->executeQuery()
                        ->fetchAssociative()['additional_field_value_id'];
    }
}