<?php
/* --------------------------------------------------------------
   CategoryListProviderInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryListProviderInterface
 *
 * This interface defines methods for creating a list of flattened categories with just its essential data.
 * The list of categories is filtered by its parent category ID and customer status permissions and for simplicity
 * it contains language specific data only in one language.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryListProviderInterface
{
    /**
     * Returns a category list based the parent ID provided.
     *
     * @param IdType $parentId Category parent ID.
     *
     * @return CategoryListItemCollection
     */
    public function getByParentId(IdType $parentId);
    
    
    /**
     * Filters category records by a given CategorySearchCondition object and returns an collection with results.
     *
     * @param \CategorySearchCondition $condition Conditions object for search.
     * @param \Pager|null              $pager     (Optional) Pager object with pagination information
     * @param array                    $sorters   (Optional) array of Sorter objects with data sorting information
     *
     * @return \CategoryListItemCollection
     */
    public function searchCategories(CategorySearchCondition $condition, \Pager $pager = null, array $sorters = []);
}