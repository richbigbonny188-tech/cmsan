<?php
/*--------------------------------------------------------------
   AdditionalFieldsWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\AdditionalField;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldIds;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\DeletionOfAdditionalFieldsFailedException;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\StorageOfAdditionalFieldsFailedException;

/**
 * Class AdditionalFieldsWriter
 *
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App\Data
 */
class AdditionalFieldsWriter
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
     * @var FieldKeyGenerator
     */
    protected $fieldKeyGenerator;
    
    
    /**
     * AdditionalFieldWriter constructor.
     *
     * @param Connection        $connection
     * @param FieldKeyGenerator $fieldKeyGenerator
     */
    public function __construct(
        Connection        $connection,
        FieldKeyGenerator $fieldKeyGenerator
    ) {
        $this->connection        = $connection;
        $this->fieldKeyGenerator = $fieldKeyGenerator;
    }
    
    
    /**
     * @param array $names
     *
     * @return array
     * @throws CreationOfAdditionalFieldFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createAdditionalField(array $names): array
    {
        $fieldIds = [];
        
        try {
            $this->connection->beginTransaction();
            
            foreach ($names as $localizations) {
                $this->connection->createQueryBuilder()
                    ->insert('additional_fields')
                    ->setValue('field_key', ':field_key')
                    ->setParameter('field_key', $this->fieldKeyGenerator->newFieldKey())
                    ->setValue('multilingual', ':multilingual')
                    ->setParameter('multilingual', 1)
                    ->executeQuery();
                
                $fieldIds[] = $fieldId = (int)$this->connection->lastInsertId();
                
                foreach ($localizations['names'] as $languageCode => $name) {
                    $this->connection->createQueryBuilder()
                        ->insert('additional_field_descriptions')
                        ->setValue('additional_field_id', ':additional_field_id')
                        ->setParameter('additional_field_id', $fieldId)
                        ->setValue('language_id', ':language_id')
                        ->setParameter('language_id', $this->getLanguageIdByCode(strtolower($languageCode)))
                        ->setValue('name', ':name')
                        ->setParameter('name', $name)
                        ->executeQuery();
                }
            }
            
            $this->connection->commit();
            
            return $fieldIds;
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw CreationOfAdditionalFieldFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param FieldIds $additionalFieldIds
     *
     * @throws DeletionOfAdditionalFieldsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteAdditionalFields(FieldIds $additionalFieldIds): void
    {
        try {
            $this->connection->beginTransaction();
            
            if (count($additionalFieldIds->toArray())) {
                $fieldIdsCommaList = implode(', ', $additionalFieldIds->toArray());
                
                $where = sprintf('additional_field_id IN (%s)', $fieldIdsCommaList);
                
                $this->connection->createQueryBuilder()->delete('additional_fields')->where($where)->executeQuery();
                $this->connection->createQueryBuilder()
                    ->delete('additional_field_descriptions')
                    ->where($where)
                    ->executeQuery();
                
                $deleteFieldValueDescriptionsQuery = '
                    DELETE FROM `additional_field_value_descriptions`
                    WHERE `additional_field_value_id` IN (
                        SELECT `additional_field_value_id` FROM `additional_field_values`
                        WHERE additional_field_id IN (%s)
                    )';
                
                $this->connection->executeQuery(sprintf($deleteFieldValueDescriptionsQuery, $fieldIdsCommaList));
                
                $deleteFieldValuesQuery = 'DELETE FROM `additional_field_values` WHERE additional_field_id IN (%s)';
                
                $this->connection->executeQuery(sprintf($deleteFieldValuesQuery, $fieldIdsCommaList));
            }
            
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw DeletionOfAdditionalFieldsFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param AdditionalField ...$fields
     *
     * @throws StorageOfAdditionalFieldsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeAdditionalFields(AdditionalField ...$fields): void
    {
        try {
            $this->connection->beginTransaction();
            
            foreach ($fields as $field) {
                foreach ($field->fieldNames() as $fieldName) {
                    $this->connection->createQueryBuilder()
                        ->update('additional_field_descriptions')
                        ->set('name', ':name')
                        ->where('language_id = :language_id')
                        ->andWhere('additional_field_id = :additional_field_id')
                        ->setParameter('name', $fieldName->name())
                        ->setParameter('language_id', $this->getLanguageIdByCode($fieldName->languageCode()))
                        ->setParameter('additional_field_id', $field->id())
                        ->executeQuery();
                }
            }
            
            $this->connection->commit();
        } catch (Exception $exception) {
            $this->connection->rollBack();
            
            throw StorageOfAdditionalFieldsFailedException::becauseOfException($exception);
        }
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
}