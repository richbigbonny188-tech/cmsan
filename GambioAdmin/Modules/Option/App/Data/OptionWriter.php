<?php
/* --------------------------------------------------------------
   OptionWriter.php 2023-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use \Exception;
use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Services\Exceptions\CreationOfOptionsFailedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\DeletionOfOptionsFailedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\StorageOfOptionsFailedException;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class OptionWriter
 *
 * @package Gambio\Admin\Modules\Option\App\Data
 */
class OptionWriter
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var LanguageService
     */
    private $languageService;
    
    /**
     * @var LegacyWriterForOptions
     */
    private $legacyInserte;
    
    
    /**
     * OptionWriter constructor.
     *
     * @param Connection             $db
     * @param LanguageService        $languageService
     * @param LegacyWriterForOptions $legacyWriter
     */
    public function __construct(Connection $db, LanguageService $languageService, LegacyWriterForOptions $legacyWriter)
    {
        $this->db              = $db;
        $this->languageService = $languageService;
        $this->legacyInserte   = $legacyWriter;
    }
    
    
    /**
     * @param OptionDetails   $details
     * @param NewOptionValues $newOptionValues
     * @param OptionType      $type
     * @param int             $sortOrder
     *
     * @return int
     *
     * @throws CreationOfOptionsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createOption(
        OptionDetails   $details,
        NewOptionValues $newOptionValues,
        OptionType      $type,
        int             $sortOrder
    ): int {
        try {
            $this->db->beginTransaction();
            $id = $this->insertOption($details, $newOptionValues, $type, $sortOrder);
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw CreationOfOptionsFailedException::becauseOfPreviousException($exception);
        }
        
        return $id;
    }
    
    
    /**
     * @param array ...$creationArgs
     *
     * @return int[]
     *
     * @throws CreationOfOptionsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleOptions(array ...$creationArgs): array
    {
        try {
            $this->db->beginTransaction();
            $ids = [];
            foreach ($creationArgs as $args) {
                [$details, $newOptionValues, $type, $sortOrder] = $args;
                $ids[] = $this->insertOption($details, $newOptionValues, $type, $sortOrder);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw CreationOfOptionsFailedException::becauseOfPreviousException($exception);
        }
        
        return $ids;
    }
    
    
    /**
     * @param Option ...$options
     *
     * @throws StorageOfOptionsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeOptions(Option ...$options): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($options as $option) {
                $this->updateOption($option);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw StorageOfOptionsFailedException::becauseOfPreviousException($exception);
        }
    }
    
    
    /**
     * @param OptionId ...$ids
     *
     * @throws DeletionOfOptionsFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteOptions(OptionId ...$ids): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($ids as $id) {
                $this->deleteOption($id);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw DeletionOfOptionsFailedException::becauseOfPreviousException($exception);
        }
    }
    
    
    /**
     * @param Option ...$options
     *
     * @throws ConnectionException
     * @throws StorageOfOptionsFailedException
     */
    public function storeOptionsSortOrder(Option ...$options): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($options as $option) {
                $this->updateOptionSortOrder($option);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw StorageOfOptionsFailedException::becauseOfPreviousException($exception);
        }
    }
    
    
    /**
     * @param OptionDetails   $details
     * @param NewOptionValues $newOptionValues
     * @param OptionType      $type
     * @param int             $sortOrder
     *
     * @return int
     *
     * @throws Exception
     */
    private function insertOption(
        OptionDetails   $details,
        NewOptionValues $newOptionValues,
        OptionType      $type,
        int             $sortOrder
    ): int {
        $this->db->createQueryBuilder()
            ->insert('properties')
            ->setValue('sort_order', ':sortOrder')
            ->setValue('display_type', ':type')
            ->setParameter('sortOrder', $sortOrder)
            ->setParameter('type', $type->value())
            ->executeQuery();
        
        $optionId = (int)$this->db->lastInsertId();
        
        $this->insertOptionDetails($optionId, $details);
        $newOptionValueIds = $this->insertNewOptionValues($optionId, $newOptionValues);
        
        $this->legacyInserte->insertOption($optionId,
                                           $newOptionValueIds,
                                           $details,
                                           $newOptionValues,
                                           $type,
                                           $sortOrder);
        
        return $optionId;
    }
    
    
    /**
     * @param Option $option
     *
     * @throws Exception
     */
    private function updateOption(Option $option): void
    {
        $this->db->createQueryBuilder()
            ->update('properties')
            ->set('sort_order', ':sortOrder')
            ->set('display_type', ':type')
            ->where('properties_id = :optionId')
            ->setParameter('optionId', $option->id())
            ->setParameter('sortOrder', $option->sortOrder())
            ->setParameter('type', $option->type())
            ->executeQuery();
        
        $this->updateOptionDetails($option);
        $this->updateOptionValues($option);
        $newOptionValueIds = $this->insertNewOptionValues($option->id(), $option->newValues());
        
        $this->legacyInserte->updateOption($option, $newOptionValueIds);
    }
    
    
    /**
     * @param OptionId $optionId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteOption(OptionId $optionId): void
    {
        $this->db->createQueryBuilder()
            ->delete('properties')
            ->where('properties_id = :optionId')
            ->setParameter('optionId', $optionId->value())
            ->executeQuery();
        
        $this->db->createQueryBuilder()
            ->delete('properties_description')
            ->where('properties_id = :optionId')
            ->setParameter('optionId', $optionId->value())
            ->executeQuery();
        
        $this->db->executeQuery('
            DELETE FROM `properties_values_description`
            WHERE `properties_values_id` IN (
                SELECT `properties_values_id`
                FROM `properties_values`
                WHERE `properties_id` = ' . $optionId->value() . '
            );');
        
        $this->db->createQueryBuilder()
            ->delete('properties_values')
            ->where('properties_id = :optionId')
            ->setParameter('optionId', $optionId->value())
            ->executeQuery();
        
        $this->legacyInserte->deleteOption($optionId);
    }
    
    
    /**
     * @param int           $optionId
     * @param OptionDetails $details
     *
     * @throws Exception
     */
    private function insertOptionDetails(int $optionId, OptionDetails $details): void
    {
        foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->insert('properties_description')
                ->setValue('properties_id', ':optionId')
                ->setValue('language_id', ':languageId')
                ->setValue('properties_name', ':label')
                ->setValue('properties_admin_name', ':adminLabel')
                ->setValue('description', ':description')
                ->setParameter('optionId', $optionId)
                ->setParameter('languageId', $language->id())
                ->setParameter('label', $details->label($language->code()))
                ->setParameter('adminLabel', $details->adminLabel($language->code()))
                ->setParameter('description', $details->description($language->code()))
                ->executeQuery();
        }
    }
    
    
    /**
     * @param Option $option
     *
     * @throws Exception
     */
    private function updateOptionDetails(Option $option): void
    {
        foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->update('properties_description')
                ->set('properties_name', ':label')
                ->set('properties_admin_name', ':adminLabel')
                ->set('description', ':description')
                ->where('properties_id = :optionId')
                ->andWhere('language_id = :languageId')
                ->setParameter('optionId', $option->id())
                ->setParameter('languageId', $language->id())
                ->setParameter('label', $option->label($language->code()))
                ->setParameter('adminLabel', $option->adminLabel($language->code()))
                ->setParameter('description', $option->description($language->code()))
                ->executeQuery();
        }
    }
    
    
    /**
     * @param int             $optionId
     * @param NewOptionValues $newOptionValues
     *
     * @return int[]
     *
     * @throws Exception
     */
    private function insertNewOptionValues(int $optionId, NewOptionValues $newOptionValues): array
    {
        $optionValueIds = [];
        foreach ($newOptionValues as $index => $newOptionValue) {
            $this->db->createQueryBuilder()
                ->insert('properties_values')
                ->setValue('properties_id', ':optionId')
                ->setValue('sort_order', ':sortOrder')
                ->setValue('value_model', ':modelNumber')
                ->setValue('weight', ':weight')
                ->setValue('value_price', ':price')
                ->setValue('display_image', ':image')
                ->setValue('stock_type', ':stockType')
                ->setValue('stock', ':stock')
                ->setValue('stock_centrally_managed', ':stockCentrallyManaged')
                ->setParameter('optionId', $optionId)
                ->setParameter('sortOrder', $newOptionValue->sortOrder())
                ->setParameter('modelNumber', $newOptionValue->modelNumber())
                ->setParameter('weight', $newOptionValue->weight())
                ->setParameter('price', $newOptionValue->price())
                ->setParameter('image', $newOptionValue->image())
                ->setParameter('stockType', $newOptionValue->stockType())
                ->setParameter('stock', $newOptionValue->stock())
                ->setParameter('stockCentrallyManaged', $newOptionValue->isStockCentrallyManaged() ? 1 : 0)
                ->executeQuery();
            
            $optionValueId          = (int)$this->db->lastInsertId();
            $optionValueIds[$index] = $optionValueId;
            
            foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
                $this->db->createQueryBuilder()
                    ->insert('properties_values_description')
                    ->setValue('properties_values_id', ':optionValueId')
                    ->setValue('language_id', ':languageId')
                    ->setValue('values_name', ':label')
                    ->setValue('description', ':description')
                    ->setParameter('optionValueId', $optionValueId)
                    ->setParameter('languageId', $language->id())
                    ->setParameter('label', $newOptionValue->label($language->code()))
                    ->setParameter('description', $newOptionValue->description($language->code()))
                    ->executeQuery();
            }
        }
        
        return $optionValueIds;
    }
    
    
    /**
     * @param Option $option
     *
     * @throws Exception
     */
    private function updateOptionValues(Option $option): void
    {
        $optionValueIds = array_map(static function (OptionValue $optionValue): int {
            return $optionValue->id();
        },
            $option->values()->asArray());
        
        $valueIdsWhereStatement = (count($optionValueIds) > 0) ? ' AND `properties_values_id` NOT IN (' . implode(', ',
                                                                                                                  $optionValueIds)
                                                                 . ')' : '';
        
        $this->db->executeQuery('
            DELETE FROM `properties_values_description`
            WHERE `properties_values_id` IN (
                SELECT `properties_values_id`
                FROM `properties_values`
                WHERE `properties_id` = ' . $option->id() . $valueIdsWhereStatement . '
            );');
        
        $this->db->createQueryBuilder()
            ->delete('properties_values')
            ->where('properties_id = :optionId ' . $valueIdsWhereStatement)
            ->setParameter('optionId', $option->id())
            ->executeQuery();
        
        foreach ($option->values() as $optionValue) {
            $this->db->createQueryBuilder()
                ->update('properties_values')
                ->set('properties_id', ':optionId')
                ->set('sort_order', ':sortOrder')
                ->set('value_model', ':modelNumber')
                ->set('weight', ':weight')
                ->set('value_price', ':price')
                ->set('display_image', ':image')
                ->set('stock_type', ':stockType')
                ->set('stock', ':stock')
                ->set('stock_centrally_managed', ':stockCentrallyManaged')
                ->where('properties_values_id = :optionValueId')
                ->setParameter('optionValueId', $optionValue->id())
                ->setParameter('optionId', $option->id())
                ->setParameter('sortOrder', $optionValue->sortOrder())
                ->setParameter('modelNumber', $optionValue->modelNumber())
                ->setParameter('weight', $optionValue->weight())
                ->setParameter('price', $optionValue->price())
                ->setParameter('image', $optionValue->image())
                ->setParameter('stockType', $optionValue->stockType())
                ->setParameter('stock', $optionValue->stock())
                ->setParameter('stockCentrallyManaged', $optionValue->isStockCentrallyManaged() ? 1 : 0)
                ->executeQuery();
            
            foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
                $this->db->createQueryBuilder()
                    ->update('properties_values_description')
                    ->set('values_name', ':label')
                    ->set('description', ':description')
                    ->where('properties_values_id = :optionValueId')
                    ->andWhere('language_id = :languageId')
                    ->setParameter('optionValueId', $optionValue->id())
                    ->setParameter('languageId', $language->id())
                    ->setParameter('label', $optionValue->label($language->code()))
                    ->setParameter('description', $optionValue->description($language->code()))
                    ->executeQuery();
            }
        }
    }
    
    
    /**
     * @param Option $option
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateOptionSortOrder(Option $option): void
    {
        $this->db->createQueryBuilder()
            ->update('properties')
            ->set('sort_order', ':sortOrder')
            ->where('properties_id = :optionId')
            ->setParameter('optionId', $option->id())
            ->setParameter('sortOrder', $option->sortOrder())
            ->executeQuery();
        
        $this->legacyInserte->updateOptionSortOrder($option);
    }
}