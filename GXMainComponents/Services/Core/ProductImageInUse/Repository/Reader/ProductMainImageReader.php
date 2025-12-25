<?php
/* --------------------------------------------------------------
   ProductMainImageReader.php 2023-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Doctrine\DBAL\Connection;

/**
 * Class ProductMainImageReader
 *
 * @package Gambio\MainComponents\Services\Core\ProductImageInUse\Repository\Reader
 */
class ProductMainImageReader implements ProductMainImageReaderInterface
{
    
    /**
     * @param Connection          $connection
     * @param ProductImageFactory $imageFactory
     */
    public function __construct(protected Connection $connection, protected ProductImageFactory $imageFactory)
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainImagesInUse(?int $exceptByProductId = null): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('products_image')->distinct()->from('products');
        
        if (!is_null($exceptByProductId)) {
            $queryBuilder->where('products_id != :exceptByProductId')
                ->setParameter('exceptByProductId', $exceptByProductId);
        }
        
        $statement = $queryBuilder->executeQuery();
        
        if ($statement->columnCount() === 0) {
            return [];
        }
        
        $result = [];
        foreach ($statement->fetchAllAssociative() as $image) {
            $productsMainImage = $image['products_image'];
            
            $result[] = $this->imageFactory->createImageNameDTO($productsMainImage);
        }
        
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainImageIsInUse(string $filePath, ?int $exceptByProductId = null): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('products_image')
            ->distinct()
            ->from('products')
            ->where('products_image = :filePath')
            ->setParameter('filePath', $filePath);
        
        if (!is_null($exceptByProductId)) {
            $queryBuilder->andWhere('products_id != :exceptByProductId')
                ->setParameter('exceptByProductId', $exceptByProductId);
        }
        
        $statement = $queryBuilder->executeQuery();
        
        return $statement->rowCount() > 0;
    }
}