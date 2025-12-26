<?php
/* --------------------------------------------------------------
  UpdateServiceDatabaseWriter.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Repositories;

use CI_DB_query_builder;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListAttributeAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListCombinationAssignmentDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListNameDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateServiceDatabaseWriterInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateMultipleSortingDtoInterface;
use LanguageProviderInterface;

/**
 * Class UpdateServiceDatabaseWriter
 * @package Gambio\ProductImageList\UpdateService\Repositories
 */
class UpdateServiceDatabaseWriter implements UpdateServiceDatabaseWriterInterface
{
    protected const IMAGE_LIST_TABLE                   = 'product_image_list';
    protected const IMAGE_LIST_COMBI_TABLE             = 'product_image_list_combi';
    protected const IMAGE_LIST_ATTRIBUTE_TABLE         = 'product_image_list_attribute';
    protected const IMAGE_LIST_ID_COLUMN               = 'product_image_list_id';
    protected const IMAGE_LIST_NAME_COLUMN             = 'product_image_list_name';
    protected const IMAGE_LIST_IMAGE_TABLE             = 'product_image_list_image';
    protected const IMAGE_LIST_TEXT_TABLE              = 'product_image_list_image_text';
    protected const IMAGE_LIST_IMAGE_ID_COLUMN         = 'product_image_list_image_id';
    protected const IMAGE_LIST_IMAGE_SORT_ORDER_COLUMN = 'product_image_list_image_sort_order';
    protected const IMAGE_LIST_TEXT_TYPE_COLUMN        = 'product_image_list_image_text_type';
    protected const IMAGE_LIST_TEXT_VALUE_COLUMN       = 'product_image_list_image_text_value';
    protected const IMAGE_LIST_LANGUAGE_ID_COLUMN      = 'language_id';
    protected const PRODUCT_COMBI_ID_COLUMN            = 'products_properties_combis_id';
    protected const PRODUCT_ATTRIBUTE_ID_COLUMN        = 'products_attributes_id';
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * UpdateServiceDatabaseWriter constructor.
     *
     * @param CI_DB_query_builder       $queryBuilder
     * @param LanguageProviderInterface $languageProvider
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProviderInterface $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImagesSort(UpdateMultipleSortingDtoInterface $sortOrders): void
    {
        $dtos = $sortOrders->dtos();
        
        if (count($dtos)) {
            
            foreach ($dtos as $dto) {
                
                $this->queryBuilder->update(self::IMAGE_LIST_IMAGE_TABLE,
                                            [self::IMAGE_LIST_IMAGE_SORT_ORDER_COLUMN => $dto->sortIndex()],
                                            [self::IMAGE_LIST_IMAGE_ID_COLUMN => $dto->imageId()]);
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageText(TextCollection $texts): void
    {
        if (count($texts)) {
            
            foreach ($texts as $text) {
                
                $languageId = $this->languageProvider->getIdByCode(new \LanguageCode(new \StringType($text->languageCode()
                                                                                                         ->value())));
                
                $this->queryBuilder->update(self::IMAGE_LIST_TEXT_TABLE,
                                            [self::IMAGE_LIST_TEXT_VALUE_COLUMN => $text->value()],
                                            [
                                                self::IMAGE_LIST_LANGUAGE_ID_COLUMN => $languageId,
                                                self::IMAGE_LIST_IMAGE_ID_COLUMN    => $text->imageId()->value(),
                                                self::IMAGE_LIST_TEXT_TYPE_COLUMN   => $text->type()
                                            ]);
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListName(UpdateImageListNameDtoInterface $dto): void
    {
        $this->queryBuilder->update(self::IMAGE_LIST_TABLE,
                                    [self::IMAGE_LIST_NAME_COLUMN => $dto->listName()],
                                    [self::IMAGE_LIST_ID_COLUMN => $dto->listId()]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListCombiAssignment(UpdateImageListCombinationAssignmentDtoInterface $dto): void
    {
        $this->queryBuilder->replace(self::IMAGE_LIST_COMBI_TABLE,
                                     [
                                         self::IMAGE_LIST_ID_COLUMN    => $dto->listId(),
                                         self::PRODUCT_COMBI_ID_COLUMN => $dto->combiId()
                                     ]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateImageListAttributeAssigment(UpdateImageListAttributeAssignmentDtoInterface $dto): void
    {
        $this->queryBuilder->replace(self::IMAGE_LIST_ATTRIBUTE_TABLE,
                                     [
                                         self::IMAGE_LIST_ID_COLUMN    => $dto->listId(),
                                         self::PRODUCT_ATTRIBUTE_ID_COLUMN => $dto->attributeId()
                                     ]);
    }
}