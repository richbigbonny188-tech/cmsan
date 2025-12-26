<?php
/* --------------------------------------------------------------
  ReadServiceDatabaseReader.php 2020-07-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Repositories;

use CI_DB_query_builder;
use Gambio\ProductImageList\ReadService\Dtos\ImageListDto;
use Gambio\ProductImageList\ReadService\Dtos\ImageListImageDto;
use Gambio\ProductImageList\ReadService\Dtos\ImageListImageTextDto;
use Gambio\ProductImageList\ReadService\Exceptions\AttributeDoesNotHaveAListException;
use Gambio\ProductImageList\ReadService\Exceptions\CombinationDoesNotHaveAListException;
use Gambio\ProductImageList\ReadService\Interfaces\AttributeIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\CombiModelAndProductsIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ImageListDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ImageListImageDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ImageListImageTextDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\PropertiesCombisIdDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceDatabaseReaderInterface;

/**
 * Class ReadServiceDatabaseReader
 * @package Gambio\ProductImageList\ReadService\Repositories
 */
class ReadServiceDatabaseReader implements ReadServiceDatabaseReaderInterface
{
    protected const IMAGE_LIST_TABLE_NAME             = 'product_image_list';
    protected const IMAGE_LIST_IMAGE_TABLE_NAME       = 'product_image_list_image';
    protected const IMAGE_LIST_IMAGE_TEXT_TABLE_NAME  = 'product_image_list_image_text';
    protected const LIST_ID_COLUMN                    = 'product_image_list_id';
    protected const LIST_NAME_COLUMN                  = 'product_image_list_name';
    protected const LIST_IMAGE_ID_COLUMN              = 'product_image_list_image_id';
    protected const LIST_IMAGE_LOCAL_PATH_COLUMN      = 'product_image_list_image_local_path';
    protected const LIST_IMAGE_SORT_ORDER_COLUMN      = 'product_image_list_image_sort_order';
    protected const LIST_IMAGE_TEXT_TYPE_COLUMN       = 'product_image_list_image_text_type';
    protected const LIST_IMAGE_TEXT_VALUE_COLUMN      = 'product_image_list_image_text_value';
    protected const LANGUAGE_ID_COLUMN                = 'language_id';
    protected const PROPERTY_COMBINATION_TABLE_NAME   = 'products_properties_combis';
    protected const PROPERTY_COMBI_MODEL_COLUMN       = 'combi_model';
    protected const PROPERTY_COMBI_PRODUCTS_ID_COLUMN = 'products_id';
    protected const PROPERTY_COMBI_ID_COLUMN          = 'products_properties_combis_id';
    protected const IMAGE_LIST_COMBI_TABLE_NAME       = 'product_image_list_combi';
    protected const ATTRIBUTE_ID_COLUMN               = 'products_attributes_id';
    protected const IMAGE_LIST_ATTRIBUTE_TABLE_NAME   = 'product_image_list_attribute';

    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ReadServiceDatabaseReader constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageLists(int $listId = null): array
    {
        $result  = [];
        $builder = $this->queryBuilder->select()->from(self::IMAGE_LIST_TABLE_NAME);
        
        if ($listId !== null) {
            
            $builder->where([self::LIST_ID_COLUMN => $listId]);
        }
        
        $entries = $builder->get()->result_array();
        
        if (count($entries)) {
            
            foreach ($entries as $entry) {
                
                [
                    self::LIST_ID_COLUMN   => $listId,
                    self::LIST_NAME_COLUMN => $listName
                ] = $entry;
                
                $result[] = $this->createImageListDto($listId, $listName);
            }
        }
        
        return $result;
    }

    /**
     * @param $lists
     *
     * @return array
     */
    protected function getTexts($lists = []) {
        $sql = "select languages.languages_id language_id,
                       product_image_list_image.product_image_list_id,
                       product_image_list_image.product_image_list_image_id,
                       types.type product_image_list_image_text_type,
                       coalesce(product_image_list_image_text.product_image_list_image_text_value, '') product_image_list_image_text_value 
                from product_image_list_image
                inner join languages
                    on languages.status_admin = 1
                    or languages.status = 1
                inner join (
                    select 'alt_title' as type
                    union
                    select 'title' as type
                    ) types
                left join product_image_list_image_text
                    on product_image_list_image_text.language_id = languages.languages_id
                    and product_image_list_image_text.product_image_list_image_id = product_image_list_image.product_image_list_image_id
                    and product_image_list_image_text.product_image_list_image_text_type = types.type
";
        if (count($lists)) {
            $sqlList = implode(",", $lists);
            $sql .=" where product_image_list_image.product_image_list_image_id in ($sqlList)";
        }
        return $this->queryBuilder->query($sql)->result_array();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImages(int $listId = null): array
    {
        $result  = [];
        $builder = $this->queryBuilder->select()->from(self::IMAGE_LIST_IMAGE_TABLE_NAME);
        
        if ($listId !== null) {
            
            $builder->where([self::LIST_ID_COLUMN => $listId]);
        }
        
        $entries = $builder->get()->result_array();
        
        if (count($entries)) {
            
            foreach ($entries as $entry) {
                
                [
                    self::LIST_IMAGE_ID_COLUMN         => $imageId,
                    self::LIST_ID_COLUMN               => $actualListId,
                    self::LIST_IMAGE_LOCAL_PATH_COLUMN => $localPath,
                    self::LIST_IMAGE_SORT_ORDER_COLUMN => $sortOrder
                ] = $entry;
                
                $result[] = $this->createListImageDto($imageId, $actualListId, $localPath, $sortOrder);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageTexts(): array
    {
        $result = [];
        
        $entries = $this->getTexts();
        
        if (count($entries)) {
            
            foreach ($entries as $entry) {
                
                [
                    self::LIST_IMAGE_ID_COLUMN         => $imageId,
                    self::LIST_IMAGE_TEXT_TYPE_COLUMN  => $textType,
                    self::LIST_IMAGE_TEXT_VALUE_COLUMN => $textValue,
                    self::LANGUAGE_ID_COLUMN           => $languageId
                ] = $entry;
                
                $result[] = $this->createListImageTextDto($imageId,
                                                          $textType,
                                                          $textValue,
                                                          $languageId);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param int    $listId
     * @param string $listName
     *
     * @return ImageListDtoInterface
     */
    protected function createImageListDto(int $listId, string $listName): ImageListDtoInterface
    {
        return new ImageListDto($listId, $listName);
    }
    
    
    /**
     * @param int    $imageId
     * @param int    $listId
     * @param string $localPath
     * @param int    $sortOder
     *
     * @return ImageListImageDtoInterface
     */
    protected function createListImageDto(
        int $imageId,
        int $listId,
        string $localPath,
        int $sortOder
    ): ImageListImageDtoInterface {
        
        return new ImageListImageDto($imageId, $listId, $localPath, $sortOder);
    }
    
    
    /**
     * @param int    $imageId
     * @param string $textType
     * @param string $textValue
     * @param int    $languageId
     *
     * @return ImageListImageTextDtoInterface
     */
    protected function createListImageTextDto(
        int $imageId,
        string $textType,
        string $textValue,
        int $languageId
    ): ImageListImageTextDtoInterface {
        
        return new ImageListImageTextDto($imageId, $textType, $textValue, $languageId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListById(int $id): array
    {
        return $this->getImageLists($id);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImagesByListId(int $listId): array
    {
        return $this->getImages($listId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageTextsByListId(int $listId): array
    {
        $imagesInList = $this->getImagesByListId($listId);
        $result       = $imageIds = [];
        
        if (count($imagesInList)) {
            
            foreach ($imagesInList as $imageDto) {
                
                $imageIds[] = $imageDto->imageId();
            }
        }
        
        if (count($imageIds)) {
            
            $entries = $this->getTexts(array_unique($imageIds));
        } else {
            
            $entries = [];
        }
        
        if (count($entries)) {
            
            foreach ($entries as $entry) {
                
                [
                    self::LIST_IMAGE_ID_COLUMN         => $imageId,
                    self::LIST_IMAGE_TEXT_TYPE_COLUMN  => $textType,
                    self::LIST_IMAGE_TEXT_VALUE_COLUMN => $textValue,
                    self::LANGUAGE_ID_COLUMN           => $languageId
                ] = $entry;
                
                $result[] = $this->createListImageTextDto($imageId,
                                                          $textType,
                                                          $textValue,
                                                          $languageId);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListIdByCombiModelAndProductsId(CombiModelAndProductsIdDtoInterface $dto): int
    {
        $combiId = $this->queryBuilder->select(self::PROPERTY_COMBI_ID_COLUMN)
            ->from(self::PROPERTY_COMBINATION_TABLE_NAME)
            ->where([
                        self::PROPERTY_COMBI_MODEL_COLUMN       => $dto->combiModel(),
                        self::PROPERTY_COMBI_PRODUCTS_ID_COLUMN => $dto->productsId(),
                    ])
            ->get()
            ->result_array();
        
        if (count($combiId) === 0) {
            
            throw new CombinationDoesNotHaveAListException;
        }
        
        $combiId = (int)current($combiId)[self::PROPERTY_COMBI_ID_COLUMN];
    
        $listId = $this->queryBuilder->select(self::LIST_ID_COLUMN)
            ->from(self::IMAGE_LIST_COMBI_TABLE_NAME)
            ->where([
                        self::PROPERTY_COMBI_ID_COLUMN => $combiId
                    ])
            ->get()
            ->result_array();
        
        if (count($listId) === 0) {
    
            throw new CombinationDoesNotHaveAListException;
        }
        
        return (int)current($listId)[self::LIST_ID_COLUMN];
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListIdByCombiId(PropertiesCombisIdDtoInterface $dto): int
    {
        $result = $this->queryBuilder->select(self::LIST_ID_COLUMN)
            ->from(self::IMAGE_LIST_COMBI_TABLE_NAME)
            ->where([self::PROPERTY_COMBI_ID_COLUMN => $dto->combiId()])
            ->get()
            ->result_array();
        
        if (count($result) === 0) {
            
            throw new CombinationDoesNotHaveAListException;
        }
        
        return (int)current($result)[self::LIST_ID_COLUMN];
    }

    /**
     * @inheritDoc
     */
    public function getImageListIdByAttributeId(AttributeIdDtoInterface $dto): int
    {
        $result = $this->queryBuilder->select(self::LIST_ID_COLUMN)
            ->from(self::IMAGE_LIST_ATTRIBUTE_TABLE_NAME)
            ->where([self::ATTRIBUTE_ID_COLUMN => $dto->attributeId()])
            ->get()
            ->result_array();

        if (count($result) === 0) {

            throw new AttributeDoesNotHaveAListException();
        }

        return (int)current($result)[self::LIST_ID_COLUMN];
    }
}