<?php
/*--------------------------------------------------------------
   ImageListReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageListDoesNotExistException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class ImageListReader
 *
 * @package Gambio\Admin\Modules\ImageList\App\Data
 */
class ImageListReader
{
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * ImageListReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param array|null $columns
     * @param array|null $order
     *
     * @return QueryBuilder
     */
    private function getQuery(array $columns = null, array $order = null): QueryBuilder
    {
        $columns = $columns ?? [
            '`pil`.`product_image_list_id`',
            '`pil`.`product_image_list_name`',
            '`pili`.`product_image_list_image_id`',
            '`pili`.`product_image_list_image_local_path`',
            '`pili`.`product_image_list_image_sort_order`',
            '`pilit`.`product_image_list_image_text_type`',
            '`pilit`.`product_image_list_image_text_value`',
            '`pilit`.`language_id`',
            '`l`.`code`',
        ];
        
        $order = $order ?? [
            '`pil`.`product_image_list_id`',
            '`pili`.`product_image_list_image_sort_order`',
            '`pilit`.`product_image_list_image_text_type`',
            '`pilit`.`language_id`',
        ];
        
        return $this->connection->createQueryBuilder()
            ->select(implode(', ', $columns))
            ->from('product_image_list', 'pil')
            ->leftJoin('pil',
                       'product_image_list_image',
                       'pili',
                       '`pil`.`product_image_list_id`=`pili`.`product_image_list_id`')
            ->leftJoin('pili',
                       'product_image_list_image_text',
                       'pilit',
                       '`pili`.`product_image_list_image_id`=`pilit`.`product_image_list_image_id`')
            ->leftJoin('pilit', 'languages', 'l', '`pilit`.`language_id`=`l`.`languages_id`')
            ->orderBy(implode(', ', $order))
            ->groupBy(implode(', ', $columns));
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function getCountListIdQuery(): QueryBuilder
    {
        return $this->getQuery(['`pil`.`product_image_list_id`'], ['id_count'])
            ->select('COUNT( DISTINCT `pil`.`product_image_list_id`) as id_count');
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function getImageListIdsQuery(): QueryBuilder
    {
        $imageListIdColumns = ['`pil`.`product_image_list_id`'];
        
        return $this->getQuery($imageListIdColumns, $imageListIdColumns);
    }
    
    
    /**
     * Returns a collection of all image lists.
     *
     * @return string[]
     * @throws Exception
     */
    public function getAllImageLists(): array
    {
        $data = $this->getQuery()->executeQuery()->fetchAllAssociative();
        
        return $this->restructureDataArray($data);
    }
    
    
    /**
     * Returns a specific image list based on the given image list ID.
     *
     * @param ImageListId $imageListId
     *
     * @return string[]
     *
     * @throws ImageListDoesNotExistException
     * @throws Exception
     */
    public function getImageListById(ImageListId $imageListId): array
    {
        $result = $this->getQuery()
            ->where('`pil`.`product_image_list_id` = :product_image_list_id')
            ->setParameter('product_image_list_id', $imageListId->value())
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            throw ImageListDoesNotExistException::forImageListId($imageListId->value());
        }
        
        $data = $result->fetchAllAssociative();
        
        return $this->restructureDataArray($data)[0];
    }
    
    
    /**
     * Returns a filtered, sorted, paginated collection of image lists.
     *
     * @param Filters|SqlFilters       $filters
     * @param Sorting|SqlSorting       $sorting
     * @param Pagination|SqlPagination $pagination
     *
     * @return array
     * @throws Exception
     */
    public function filterImageLists(Filters $filters, Sorting $sorting, Pagination $pagination): array
    {
        $idQuery = $this->getImageListIdsQuery();
        
        $filters->applyToQuery($idQuery);
        $sorting->applyToQuery($idQuery);
        $pagination->applyToQuery($idQuery);
        
        $ids = $idQuery->executeQuery()->fetchAllNumeric();
        
        $idsCache = [];
        foreach ($ids as $id) {
            $idsCache[] = array_shift($id);
        }
        $ids = $idsCache;
        
        if (count($ids) === 0) {
            return [];
        }
        
        $where = sprintf('pil.product_image_list_id IN (%s)', implode(',', $ids));
        $data  = $this->getQuery()->where($where)->executeQuery()->fetchAllAssociative();
        
        return $this->restructureDataArray($data);
    }
    
    
    /**
     * Returns the total count of filtered image lists.
     *
     * @param Filters|SqlFilters $filters
     *
     * @return int
     * @throws Exception
     */
    public function getImageListsTotalCount(Filters $filters): int
    {
        $queryBuilder = $this->getCountListIdQuery();
        $filters->applyToQuery($queryBuilder);
        
        return $queryBuilder->executeQuery()->rowCount();
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    public function getAvailableLanguageCodes(): array
    {
        $data = $this->connection->createQueryBuilder()
            ->select('code')
            ->from('languages')
            ->executeQuery()
            ->fetchAllNumeric();
        
        $dataCache = [];
        foreach ($data as $dataset) {
            $dataCache[] = array_shift($dataset);
        }
        
        return $dataCache;
    }
    
    
    /**
     * Repacks query result in a more suitable structure for later use by the mapper / factory
     *
     * @param array $rows
     *
     * @return array
     */
    private function restructureDataArray(array $rows): array
    {
        $result = [];
        
        if (count($rows)) {
            foreach ($rows as $row) {
                $imageListId  = $row['product_image_list_id'];
                $imageId      = $row['product_image_list_image_id'];
                $textType     = $row['product_image_list_image_text_type'];
                $languageCode = $row['code'];
                
                if (isset($result[$imageListId]) === false) {
                    $result[$imageListId] = [
                        'product_image_list_id'     => $imageListId,
                        'product_image_list_name'   => $row['product_image_list_name'],
                        'product_image_list_images' => [],
                    ];
                }
                
                $images =& $result[$imageListId]['product_image_list_images'];
                
                if ($imageId === null) {
                    continue;
                }
                
                if (isset($images[$imageId]) === false) {
                    $images[$imageId] = [
                        'product_image_list_image_id'         => $imageId,
                        'product_image_list_image_local_path' => $row['product_image_list_image_local_path'],
                        'product_image_list_image_sort_order' => $row['product_image_list_image_sort_order'],
                        'product_image_list_image_texts'      => [],
                    ];
                }
                
                $texts =& $images[$imageId]['product_image_list_image_texts'];
                
                $texts[$textType][$languageCode] = $row['product_image_list_image_text_value'];
            }
        }
        
        return array_values($result); // image id keys need to be removed before passing on the data
    }
}