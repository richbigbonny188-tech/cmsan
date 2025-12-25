<?php
/* --------------------------------------------------------------
   ProductRepositoryDeleteHelper.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductRepositoryDeleteHelper
 */
class ProductRepositoryDeleteHelper
{
    /**
     * Removes product contents, if they are not duplicated.
     *
     * @param \IdType                                    $productId                  Product id of content to be
     *                                                                               removed.
     * @param \CI_DB_query_builder                       $db                         Query builder instance to access
     *                                                                               data.
     * @param \ProductsContentFileStorage                $productsContentFileStorage Storage class for product
     *                                                                               contents.
     * @param \ResponsiveFileManagerConfigurationStorage $fileManagerStorage         File manager configuration access.
     *
     * @return $this|\ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    public function productsContent(
        IdType $productId,
        CI_DB_query_builder $db,
        ProductsContentFileStorage $productsContentFileStorage,
        ResponsiveFileManagerConfigurationStorage $fileManagerStorage
    ) {
        $this->_removeProductContentByProductId($productId, $db, $productsContentFileStorage, $fileManagerStorage);
        
        return $this;
    }
    
    
    /**
     * Removes product content data by the given product id.
     *
     * @param \IdType                                    $productId                  Product id of content data to be
     *                                                                               removed.
     * @param \CI_DB_query_builder                       $db                         Query builder instance to access
     *                                                                               data.
     * @param \ProductsContentFileStorage                $productsContentFileStorage Storage class for product
     *                                                                               contents.
     * @param \ResponsiveFileManagerConfigurationStorage $fileManagerStorage         File manager configuration access.
     *
     * @return $this
     */
    protected function _removeProductContentByProductId(
        IdType $productId,
        CI_DB_query_builder $db,
        ProductsContentFileStorage $productsContentFileStorage,
        ResponsiveFileManagerConfigurationStorage $fileManagerStorage
    ) {
        $productContentIdsToBeRemoved = $this->_determinePcIdToBeRemoved($productId, $db);
        
        foreach ($productContentIdsToBeRemoved as $productContentIdToBeRemoved) {
            if (!$fileManagerStorage->isInstalled()) {
                $this->_removeUnusedProductContentFiles($productContentIdToBeRemoved, $db, $productsContentFileStorage);
            }
            
            $productContentDescriptionIdToBeRemoved = $this->_determinePcdIdsToBeRemovedInPcr($db,
                                                                                              $productContentIdToBeRemoved);
            
            $this->_removeProductContentEntries($db, $productContentIdToBeRemoved);
            $this->_removeProductContentResourceEntries($db, $productContentDescriptionIdToBeRemoved);
            $this->_removeProductContentDescriptionEntries($db, $productContentIdToBeRemoved);
        }
        $this->_removeProductContentProductsEntries($db, $productId);
        
        return $this;
    }
    
    
    /**
     * Removes product content entries by the given product content id.
     *
     * @param \CI_DB_query_builder $db   Database access instance.
     * @param int                  $pcId Id of product content record to be removed.
     *
     * @return $this|\ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    protected function _removeProductContentEntries(CI_DB_query_builder $db, $pcId)
    {
        $db->delete('product_contents', ['id' => $pcId]);
        
        return $this;
    }
    
    
    /**
     * Removes product content products entries by the given product id.
     *
     * @param \CI_DB_query_builder $db        Database access instance.
     * @param \IdType              $productId Related product id.
     *
     * @return $this|\ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    protected function _removeProductContentProductsEntries(CI_DB_query_builder $db, IdType $productId)
    {
        $db->delete('product_content_products', ['product_id' => $productId->asInt()]);
        
        return $this;
    }
    
    
    /**
     * Removes product content resource entries by the given product content description ids.
     *
     * @param \CI_DB_query_builder $db     Database access instance.
     * @param array                $pcdIds Related product content description ids.
     *
     * @return $this|\ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    protected function _removeProductContentResourceEntries(
        CI_DB_query_builder $db,
        array $pcdIds
    ) {
        foreach ($pcdIds as $productContentDescriptionId) {
            $db->delete('product_content_resources',
                        ['product_content_description_id' => $productContentDescriptionId]);
        }
        
        return $this;
    }
    
    
    /**
     * Removes product content description entries by the given product content id.
     *
     * @param \CI_DB_query_builder $db   Database access instance.
     * @param int                  $pcId Related product content id.
     *
     * @return $this|\ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    protected function _removeProductContentDescriptionEntries(CI_DB_query_builder $db, $pcId)
    {
        $db->delete('product_content_descriptions', ['product_content_id' => $pcId]);
        
        return $this;
    }
    
    
    /**
     * Determines the product content description ids that are used as indicator to remove
     * the right data sets from the product_content_resources table.
     *
     * @param \CI_DB_query_builder $db               Database access instance.
     * @param int                  $productContentId Product content id to be removed.
     *
     * @return array Product content description ids or an empty array.
     */
    protected function _determinePcdIdsToBeRemovedInPcr(CI_DB_query_builder $db, $productContentId)
    {
        $productContentDescriptionIds = [];
        $pcdDataSet                   = $db->where('product_content_id', $productContentId)
            ->get('product_content_descriptions')
            ->result_array();
        
        foreach ($pcdDataSet as $productContentDescription) {
            $productContentDescriptionIds[] = (int)$productContentDescription['id'];
        }
        
        return $productContentDescriptionIds;
    }
    
    
    /**
     * Determines the product content ids that should be removed by the given product id.
     *
     * @param \IdType              $productId Id of related product of entries that should be removed.
     * @param \CI_DB_query_builder $db        Database access instance.
     *
     * @return array Ids of product contents to be removed, if there are any.
     */
    protected function _determinePcIdToBeRemoved(IdType $productId, CI_DB_query_builder $db)
    {
        $pcIds      = [];
        $pcpDataSet = $db->where('product_id', $productId->asInt())->get('product_content_products')->result_array();
        
        foreach ($pcpDataSet as $productContentProduct) {
            $pcIds[] = (int)$productContentProduct['product_content_id'];
        }
        $productContentIds = [];
        
        // determine product content ids, which are only used by the product to be removed
        foreach ($pcIds as $productContentId) {
            // if no matches are found, the product content entry for that id should be removed
            $resultSet = $db->where('product_id != ' . $productId->asInt())
                ->where('product_content_id',
                        $productContentId)
                ->get('product_content_products')
                ->result_array();
            
            if (count($resultSet) === 0) {
                $productContentIds[] = (int)$productContentId;
            }
        }
        
        return $productContentIds;
    }
    
    
    /**
     * Deletes unused product content files from storage.
     *
     * @param int                         $productContentIdToBeRemoved
     * @param \CI_DB_query_builder        $db
     * @param \ProductsContentFileStorage $productsContentFileStorage
     *
     * @return $this
     */
    protected function _removeUnusedProductContentFiles(
        $productContentIdToBeRemoved,
        CI_DB_query_builder $db,
        ProductsContentFileStorage $productsContentFileStorage
    ) {
        $productContentResourceData = $db->select('pcr.resource as file')
            ->distinct()
            ->from('product_contents as pc')
            ->join('product_content_descriptions as pcd', 'pc.id = pcd.product_content_id')
            ->join('product_content_resources as pcr',
                   'pcd.id = pcr.product_content_description_id')
            ->join('product_content_types as pct', 'pct.id = pcr.product_content_types_id')
            ->where('pc.id', $productContentIdToBeRemoved)
            ->where('pct.name', 'file')
            ->where('pcr.resource != ""')
            ->get()
            ->result_array();
        
        foreach ($productContentResourceData as $productContentResource) {
            $b = $db->from('product_content_resources')
                ->where('resource', $productContentResource['file'])
                ->get()
                ->result_array();
            
            if (count($b) === 1) {
                $productsContentFileStorage->deleteFile(new FilenameStringType($productContentResource['file']));
            }
        }
        
        return $this;
    }
    
    
    /**
     * Removes property combination images.
     *
     * @param \IdType $combinationId Combination id of images to be removed.
     *
     * @return $this|ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    public function propertyCombinationImages(IdType $combinationId)
    {
        $files = glob(DIR_FS_CATALOG . 'images/product_images/properties_combis_images/' . $combinationId->asInt()
                      . '_*') ? : [];
        foreach ($files as $file) {
            file_exists($file) ? unlink($file) : null;
        }
        
        return $this;
    }
    
    
    /**
     * Reset caches for categories and 'also_purchased', if cache is used.
     *
     * @return $this|ProductRepositoryDeleteHelper Same instance for chained method calls.
     */
    public function resetCategoriesAndAlsoPurchasedCache()
    {
        if (USE_CACHE == 'true') {
            xtc_reset_cache_block('categories');
            xtc_reset_cache_block('also_purchased');
        }
        
        return $this;
    }
}