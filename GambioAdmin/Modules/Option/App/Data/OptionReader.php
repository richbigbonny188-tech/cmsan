<?php
/* --------------------------------------------------------------
   OptionReader.php 2023-06-09
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
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class OptionReader
 *
 * @package Gambio\Admin\Modules\Option\App\Data
 */
class OptionReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * OptionsReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getAllOptionsData(): array
    {
        return $this->fetchOptionsData();
    }
    
    
    /**
     * @param OptionId $id
     *
     * @return array
     * @throws Exception
     */
    public function getOptionDataById(OptionId $id): array
    {
        return $this->fetchOptionsData($id->value());
    }
    
    
    /**
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function getFilteredOptionsData(Filters $filters, Sorting $sorting, Pagination $pagination): array
    {
        $query = $this->db->createQueryBuilder()
            ->select('properties.properties_id')
            ->from('properties')
            ->leftJoin('properties',
                       'properties_description',
                       'properties_description',
                       'properties.properties_id = properties_description.properties_id')
            ->leftJoin('properties',
                       'properties_values',
                       'properties_values',
                       'properties.properties_id = properties_values.properties_id')
            ->leftJoin('properties_values',
                       'properties_values_description',
                       'properties_values_description',
                       'properties_values.properties_values_id = properties_values_description.properties_values_id')
            ->leftJoin('properties_description',
                       'languages',
                       'languages',
                       'properties_description.language_id = languages.languages_id')
            ->leftJoin('properties_values_description',
                       'languages',
                       'languages2',
                       'properties_values_description.language_id = languages2.languages_id')
            ->groupBy('properties.properties_id');
        
        $filters->applyToQuery($query);
        $sorting->applyToQuery($query);
        $pagination->applyToQuery($query);
        
        $ids = $query->executeQuery()->fetchAllAssociative();
        $ids = array_map(static function (array $dbData): int {
            return (int)$dbData['properties_id'];
        },
            $ids);
        
        return empty($ids) === false ? $this->fetchOptionsData(...$ids) : [];
    }
    
    
    /**
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getOptionsTotalCount(Filters $filters): int
    {
        $query = $this->db->createQueryBuilder()
            ->select('properties.properties_id')
            ->from('properties')
            ->leftJoin('properties',
                       'properties_description',
                       'properties_description',
                       'properties.properties_id = properties_description.properties_id')
            ->leftJoin('properties',
                       'properties_values',
                       'properties_values',
                       'properties.properties_id = properties_values.properties_id')
            ->leftJoin('properties_values',
                       'properties_values_description',
                       'properties_values_description',
                       'properties_values.properties_values_id = properties_values_description.properties_values_id')
            ->leftJoin('properties_description',
                       'languages',
                       'languages',
                       'properties_description.language_id = languages.languages_id')
            ->leftJoin('properties_values_description',
                       'languages',
                       'languages2',
                       'properties_values_description.language_id = languages2.languages_id')
            ->groupBy('properties.properties_id');
        
        $filters->applyToQuery($query);
        
        return $query->executeQuery()->rowCount();
    }
    
    
    /**
     * @param int ...$optionIds
     *
     * @return array
     * @throws Exception
     */
    private function fetchOptionsData(int ...$optionIds): array
    {
        $options = [];
        $columns = [
            '`p`.`properties_id` as `id`',
            '`p`.`sort_order` as `sortOrder`',
            '`p`.`display_type` as `type`',
            '`pd`.`properties_name` as `label`',
            '`pd`.`properties_admin_name` as `adminLabel`',
            '`pd`.`description` as `description`',
            '`l`.`code` as `languageCode`',
        ];
        
        $query = $this->db->createQueryBuilder()
            ->select(implode(', ', $columns))
            ->from('properties', 'p')
            ->leftJoin('p', 'properties_description', 'pd', '`p`.`properties_id` = `pd`.`properties_id`')
            ->leftJoin('pd', 'languages', 'l', '`pd`.`language_id` = `l`.`languages_id`');
        
        if (count($optionIds) === 1) {
            $query->where('`p`.`properties_id` = :optionId')->setParameter('optionId', $optionIds[0]);
        } elseif (count($optionIds) > 1) {
            $query->where('`p`.`properties_id` IN (' . implode(', ', $optionIds) . ')');
            foreach ($optionIds as $optionId) {
                $options[(string)$optionId] = [];
            }
        }
        
        $rows = $query->executeQuery()->fetchAllAssociative();
        
        foreach ($rows as $row) {
            $options[$row['id']] = [
                'id'        => (int)$row['id'],
                'details'   => $options[$row['id']]['details'] ?? [],
                'values'    => $options[$row['id']]['values'] ?? null,
                'newValues' => [],
                'type'      => $row['type'],
                'sortOrder' => (int)$row['sortOrder'],
            ];
            
            $options[$row['id']]['details'][$row['languageCode']] = [
                'languageCode' => $row['languageCode'],
                'label'        => $row['label'],
                'adminLabel'   => $row['adminLabel'],
                'description'  => $row['description'],
            ];
            
            if ($options[$row['id']]['values'] === null) {
                $options[$row['id']]['values'] = $this->fetchValues((int)$row['id']);
            }
        }
        
        return $options;
    }
    
    
    /**
     * @param int $optionId
     *
     * @return array
     *
     * @throws Exception
     */
    private function fetchValues(int $optionId): array
    {
        $optionValues = [];
        $columns      = [
            '`pv`.`properties_values_id` as `id`',
            '`pv`.`sort_order` as `sortOrder`',
            '`pv`.`display_image` as `image`',
            '`l`.`code` as `languageCode`',
            '`pv`.`value_model` as `modelNumber`',
            '`pv`.`weight` as `weight`',
            '`pv`.`value_price` as `price`',
            '`pv`.`stock_type` as `stockType`',
            '`pv`.`stock` as `stock`',
            '`pv`.`stock_centrally_managed` as `stockCentrallyManaged`',
            '`pvd`.`values_name` as `label`',
            '`pvd`.`description` as `description`',
        ];
        
        $rows = $this->db->createQueryBuilder()
            ->select(implode(', ', $columns))
            ->from('properties_values', 'pv')
            ->leftJoin('pv',
                       'properties_values_description',
                       'pvd',
                       '`pv`.`properties_values_id` = `pvd`.`properties_values_id`')
            ->leftJoin('pvd', 'languages', 'l', '`pvd`.`language_id` = `l`.`languages_id`')
            ->where('`pv`.`properties_id` = :optionId')
            ->setParameter('optionId', $optionId)
            ->orderBy('`pv`.`properties_id`, `l`.`languages_id`')
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($rows as $row) {
            $optionValues[$row['id']] = [
                'id'             => (int)$row['id'],
                'details'        => $optionValues[$row['id']]['details'] ?? [],
                'productDetails' => [
                    'modelNumber' => $row['modelNumber'],
                    'weight'      => (float)$row['weight'],
                    'price'       => (float)$row['price'],
                ],
                'stock'          => [
                    'stockType'             => $row['stockType'],
                    'stock'                 => (float)$row['stock'],
                    'stockCentrallyManaged' => $row['stockCentrallyManaged'] === '1',
                ],
                'sortOrder'      => (int)$row['sortOrder'],
                'image'          => $row['image'],
            ];
            
            $optionValues[$row['id']]['details'][$row['languageCode']] = [
                'languageCode' => $row['languageCode'],
                'label'        => $row['label'],
                'description'  => $row['description'],
            ];
        }
        
        return $optionValues;
    }
}