<?php
/*--------------------------------------------------------------
   ProductImageReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Doctrine\DBAL\Connection;

/**
 * Class ProductImageReader
 */
class ProductImageReader implements ProductImageReaderInterface
{
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * ProductImageReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @return ProductImageNameDTO[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function imagesInUse(): array
    {
        return array_merge($this->productImages(), $this->productImageListsImages());
    }
    
    
    /**
     * @return ProductImageNameDTO[]
     * @throws \Doctrine\DBAL\Exception
     */
    protected function productImages(): array
    {
        $result    = [];
        $builder   = $this->connection->createQueryBuilder();
        $statement = $builder->select('image_name')->distinct()->from('products_images')->executeQuery();
        
        if ($statement->columnCount() !== 0) {
            foreach ($statement->fetchAllAssociative() as $image) {
                $result[] = $this->createDto($image['image_name']);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @return ProductImageNameDTO[]
     * @throws \Doctrine\DBAL\Exception
     */
    protected function productImageListsImages(): array
    {
        $result    = [];
        $builder   = $this->connection->createQueryBuilder();
        $statement = $builder->select('product_image_list_image_local_path')
            ->distinct()
            ->from('product_image_list_image')
            ->executeQuery();
        
        if ($statement->columnCount() !== 0) {
            foreach ($statement->fetchAllAssociative() as $image) {
                $result[] = $this->createDto($image['product_image_list_image_local_path']);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param string $filePath
     *
     * @return ProductImageNameDTO
     */
    protected function createDto(string $filePath): ProductImageNameDTO
    {
        return new ProductImageNameDTO($filePath);
    }
}