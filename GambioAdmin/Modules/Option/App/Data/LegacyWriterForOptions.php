<?php
/* --------------------------------------------------------------
   LegacyWriterForOptions.php 2023-04-24
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
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class LegacyWriterForOptions
 *
 * @package Gambio\Admin\Modules\Option\App\Data\LegacyAdapter
 */
class LegacyWriterForOptions
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
     * LegacyWriterForOptions constructor.
     *
     * @param Connection      $db
     * @param LanguageService $languageService
     */
    public function __construct(Connection $db, LanguageService $languageService)
    {
        $this->db              = $db;
        $this->languageService = $languageService;
    }
    
    
    /**
     * @param int             $optionId
     * @param array           $newOptionValueIds
     * @param OptionDetails   $details
     * @param NewOptionValues $newOptionValues
     * @param OptionType      $type
     * @param int             $sortOrder
     *
     * @throws Exception
     */
    public function insertOption(
        int             $optionId,
        array           $newOptionValueIds,
        OptionDetails   $details,
        NewOptionValues $newOptionValues,
        OptionType      $type,
        int             $sortOrder
    ): void {
        $legacyOptionId = $this->getNextLegacyOptionId();
        
        foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->insert('products_options')
                ->setValue('products_options_id', ':legacyOptionId')
                ->setValue('language_id', ':languageId')
                ->setValue('options_id', ':optionId')
                ->setValue('products_options_name', ':label')
                ->setValue('products_option_display_type', ':type')
                ->setValue('admin_label', ':adminLabel')
                ->setValue('description', ':description')
                ->setValue('sort_order', ':sortOrder')
                ->setParameter('legacyOptionId', $legacyOptionId)
                ->setParameter('languageId', $language->id())
                ->setParameter('optionId', $optionId)
                ->setParameter('label', $details->label($language->code()))
                ->setParameter('adminLabel', $details->adminLabel($language->code()))
                ->setParameter('description', $details->description($language->code()))
                ->setParameter('type', $type->value())
                ->setParameter('sortOrder', $sortOrder)
                ->executeQuery();
        }
        
        $this->insertOptionValues($optionId, $legacyOptionId, $newOptionValueIds, $newOptionValues);
    }
    
    
    /**
     * @param Option $option
     * @param array  $newOptionValueIds
     *
     * @throws Exception
     */
    public function updateOption(Option $option, array $newOptionValueIds): void
    {
        $legacyOptionId = $this->getLegacyOptionId($option->id());
        
        foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->update('products_options')
                ->set('products_options_id', ':legacyOptionId')
                ->set('options_id', ':optionId')
                ->set('products_options_name', ':label')
                ->set('products_option_display_type', ':type')
                ->set('admin_label', ':adminLabel')
                ->set('description', ':description')
                ->set('sort_order', ':sortOrder')
                ->where('products_options_id = :legacyOptionId')
                ->andWhere('language_id = :languageId')
                ->setParameter('legacyOptionId', $legacyOptionId)
                ->setParameter('languageId', $language->id())
                ->setParameter('optionId', $option->id())
                ->setParameter('label', $option->label($language->code()))
                ->setParameter('adminLabel', $option->adminLabel($language->code()))
                ->setParameter('description', $option->description($language->code()))
                ->setParameter('type', $option->type())
                ->setParameter('sortOrder', $option->sortOrder())
                ->executeQuery();
        }
        
        $this->updateOptionValues($option);
        $this->insertOptionValues($option->id(), $legacyOptionId, $newOptionValueIds, $option->newValues());
    }
    
    
    /**
     * @param int             $optionId
     * @param int             $legacyOptionId
     * @param int[]           $newOptionValueIds
     * @param NewOptionValues $newOptionValues
     *
     * @throws Exception
     */
    private function insertOptionValues(
        int             $optionId,
        int             $legacyOptionId,
        array           $newOptionValueIds,
        NewOptionValues $newOptionValues
    ): void {
        foreach ($newOptionValues as $index => $newOptionValue) {
            $legacyOptionValueId = $this->getNextLegacyOptionValueId();
            
            foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
                $this->db->createQueryBuilder()
                    ->insert('products_options_values')
                    ->setValue('products_options_values_id', ':legacyOptionValueId')
                    ->setValue('language_id', ':languageId')
                    ->setValue('option_value_id', ':optionValueId')
                    ->setValue('products_options_values_name', ':label')
                    ->setValue('description', ':description')
                    ->setValue('sort_order', ':sortOrder')
                    ->setValue('value_model', ':modelNumber')
                    ->setValue('weight', ':weight')
                    ->setValue('value_price', ':price')
                    ->setValue('display_image', ':image')
                    ->setValue('stock_type', ':stockType')
                    ->setValue('stock', ':stock')
                    ->setValue('stock_centrally_managed', ':stockCentrallyManaged')
                    ->setParameter('legacyOptionValueId', $legacyOptionValueId)
                    ->setParameter('languageId', $language->id())
                    ->setParameter('optionValueId', $newOptionValueIds[$index])
                    ->setParameter('label', $newOptionValue->label($language->code()))
                    ->setParameter('description', $newOptionValue->description($language->code()))
                    ->setParameter('sortOrder', $newOptionValue->sortOrder())
                    ->setParameter('modelNumber', $newOptionValue->modelNumber())
                    ->setParameter('weight', $newOptionValue->weight())
                    ->setParameter('price', $newOptionValue->price())
                    ->setParameter('image', $newOptionValue->image())
                    ->setParameter('stockType', $newOptionValue->stockType())
                    ->setParameter('stock', $newOptionValue->stock())
                    ->setParameter('stockCentrallyManaged', $newOptionValue->isStockCentrallyManaged() ? 1 : 0)
                    ->executeQuery();
            }
            
            $this->db->createQueryBuilder()
                ->insert('products_options_values_to_products_options')
                ->setValue('products_options_id', ':legacyOptionId')
                ->setValue('products_options_values_id', ':legacyOptionValueId')
                ->setValue('options_id', ':optionId')
                ->setValue('option_value_id', ':optionValueId')
                ->setParameter('legacyOptionId', $legacyOptionId)
                ->setParameter('legacyOptionValueId', $legacyOptionValueId)
                ->setParameter('optionId', $optionId)
                ->setParameter('optionValueId', $newOptionValueIds[$index])
                ->executeQuery();
        }
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
        
        $valueIdsWhereStatement = (count($optionValueIds) > 0) ? ' AND `option_value_id` NOT IN (' . implode(', ',
                                                                                                             $optionValueIds)
                                                                 . ')' : '';
        
        $this->db->executeQuery('
            DELETE FROM `products_options_values`
            WHERE `products_options_values_id` IN (
                SELECT `products_options_values_id`
                FROM `products_options_values_to_products_options`
                WHERE `options_id` = ' . $option->id() . $valueIdsWhereStatement . '
            );');
        
        $this->db->createQueryBuilder()
            ->delete('products_options_values_to_products_options')
            ->where('options_id = :optionId ' . $valueIdsWhereStatement)
            ->setParameter('optionId', $option->id())
            ->executeQuery();
        
        foreach ($option->values() as $optionValue) {
            $legacyOptionValueId = $this->getLegacyOptionValueId($optionValue->id());
            
            foreach ($this->languageService->getAvailableAdminLanguages() as $language) {
                $this->db->createQueryBuilder()
                    ->update('products_options_values')
                    ->set('products_options_values_name', ':label')
                    ->set('description', ':description')
                    ->set('sort_order', ':sortOrder')
                    ->set('value_model', ':modelNumber')
                    ->set('weight', ':weight')
                    ->set('value_price', ':price')
                    ->set('display_image', ':image')
                    ->set('stock_type', ':stockType')
                    ->set('stock', ':stock')
                    ->set('stock_centrally_managed', ':stockCentrallyManaged')
                    ->where('products_options_values_id = :legacyOptionValueId')
                    ->andWhere('language_id = :languageId')
                    ->setParameter('legacyOptionValueId', $legacyOptionValueId)
                    ->setParameter('languageId', $language->id())
                    ->setParameter('label', $optionValue->label($language->code()))
                    ->setParameter('description', $optionValue->description($language->code()))
                    ->setParameter('sortOrder', $optionValue->sortOrder())
                    ->setParameter('modelNumber', $optionValue->modelNumber())
                    ->setParameter('weight', $optionValue->weight())
                    ->setParameter('price', $optionValue->price())
                    ->setParameter('image', $optionValue->image())
                    ->setParameter('stockType', $optionValue->stockType())
                    ->setParameter('stock', $optionValue->stock())
                    ->setParameter('stockCentrallyManaged', $optionValue->isStockCentrallyManaged() ? 1 : 0)
                    ->executeQuery();
            }
        }
    }
    
    
    /**
     * @param OptionId $optionId
     *
     * @throws Exception
     */
    public function deleteOption(OptionId $optionId): void
    {
        $this->db->createQueryBuilder()
            ->delete('products_options')
            ->where('options_id = :optionId')
            ->setParameter('optionId', $optionId->value())
            ->executeQuery();
        
        $this->db->executeQuery('
            DELETE FROM `products_options_values`
            WHERE `products_options_values_id` IN (
                SELECT `products_options_values_id`
                FROM `products_options_values_to_products_options`
                WHERE `options_id` = ' . $optionId->value() . '
            );');
        
        $this->db->createQueryBuilder()
            ->delete('products_options_values_to_products_options')
            ->where('options_id = :optionId')
            ->setParameter('optionId', $optionId->value())
            ->executeQuery();
    }
    
    
    /**
     * @return int
     *
     * @throws Exception
     */
    private function getNextLegacyOptionId(): int
    {
        $result = $this->db->createQueryBuilder()
            ->select('max(`products_options_id`)+1 as `nextId`')
            ->from('products_options')
            ->executeQuery()
            ->fetchAssociative();
        
        return (int)($result['nextId'] ?? 0);
    }
    
    
    /**
     * @return int
     *
     * @throws Exception
     */
    private function getNextLegacyOptionValueId(): int
    {
        $result = $this->db->createQueryBuilder()
            ->select('max(`products_options_values_id`) + 1 as `nextId`')
            ->from('products_options_values')
            ->executeQuery()
            ->fetchAssociative();
        
        return (int)($result['nextId'] ?? 0);
    }
    
    
    /**
     * @param int $optionId
     *
     * @return int
     *
     * @throws Exception
     */
    private function getLegacyOptionId(int $optionId): int
    {
        $result = $this->db->createQueryBuilder()
            ->select('products_options_id')
            ->from('products_options')
            ->where('options_id = :optionId')
            ->setParameter('optionId', $optionId)
            ->executeQuery()
            ->fetchAssociative();
        
        return (int)$result['products_options_id'];
    }
    
    
    /**
     * @param int $optionValueId
     *
     * @return int
     *
     * @throws Exception
     */
    private function getLegacyOptionValueId(int $optionValueId): int
    {
        $query = $this->db->createQueryBuilder()
            ->select('products_options_values_id')
            ->from('products_options_values')
            ->where('option_value_id = :optionValueId')
            ->setParameter('optionValueId', $optionValueId);
        
        $result = $query->executeQuery()->fetchAssociative();
        
        return (int)$result['products_options_values_id'];
    }
    
    
    /**
     * @param Option $option
     *
     * @throws Exception
     */
    public function updateOptionSortOrder(Option $option): void
    {
        $legacyOptionId = $this->getLegacyOptionId($option->id());
        $availableAdminLanguages = $this->languageService->getAvailableAdminLanguages();
        
        foreach ($availableAdminLanguages as $language) {
            $this->db->createQueryBuilder()
                ->update('products_options')
                ->set('sort_order', ':sortOrder')
                ->where('products_options_id = :legacyOptionId')
                ->andWhere('language_id = :languageId')
                ->setParameter('legacyOptionId', $legacyOptionId)
                ->setParameter('languageId', $language->id())
                ->setParameter('sortOrder', $option->sortOrder())
                ->executeQuery();
        }
    }
}