<?php
/* --------------------------------------------------------------
   CategoryRepositoryReader.inc.php 2022-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryRepositoryReader
 *
 * This class provides methods for fetching specific category records from the database and is used in the category
 * repository among the classes for writing and deleting category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategoryRepositoryReader implements CategoryRepositoryReaderInterface
{
    /**
     * Database connector.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * Category factory.
     *
     * @var CategoryFactoryInterface
     */
    protected $categoryFactory;
    
    
    /**
     * CategoryRepositoryReader constructor.
     *
     * @param CI_DB_query_builder      $db              Database connector.
     * @param CategoryFactoryInterface $categoryFactory Category factory.
     */
    public function __construct(CI_DB_query_builder $db, CategoryFactoryInterface $categoryFactory)
    {
        $this->db              = $db;
        $this->categoryFactory = $categoryFactory;
    }
    
    
    /**
     * Returns a category.
     *
     * @param IdType $categoryId Category ID.
     *
     * @return StoredCategoryInterface
     * @throws UnexpectedValueException if no category record for the provided category ID was found.
     *
     */
    public function getById(IdType $categoryId)
    {
        // Query the categories table.
        $categoryData = $this->db->get_where('categories',
                                             [
                                                 'categories_id' => $categoryId->asInt()
                                             ])->row_array();
        
        // Get language specific context.
        $categoryDescriptionQuery = $this->db->select('categories_description.*, languages.code AS language_code')
            ->from('categories_description')
            ->join('languages',
                   'languages.languages_id = categories_description.language_id',
                   'inner')
            ->where('categories_description.categories_id', $categoryId->asInt());
        
        $categoryDescription = $categoryDescriptionQuery->get()->result_array();
        
        if ($categoryData === null) {
            throw new UnexpectedValueException('The requested category was not found in database (ID:'
                                               . $categoryId->asInt() . ')');
        }
        
        if ($categoryDescription === null) {
            throw new UnexpectedValueException('The requested category description was not found in database (ID:'
                                               . $categoryId->asInt() . ')');
        }
        
        $category = $this->_createCategoryByArray($categoryData, $categoryDescription);
        
        return $category;
    }
    
    
    /**
     * Returns all Categories with the provided parent ID.
     *
     * @param IdType $parentId
     *
     * @return IdCollection
     */
    public function getByParentId(IdType $parentId)
    {
        $subCategories = [];
        $result        = $this->db->select('categories_id')
            ->get_where('categories',
                        ['parent_id' => $parentId->asInt()])
            ->result_array();
        
        foreach ($result as $row) {
            $categoryId      = new IdType($row['categories_id']);
            $subCategories[] = $categoryId;
        }
        
        $idCollection = new IdCollection($subCategories);
        
        return $idCollection;
    }
    
    
    /**
     * Returns an id collection with the ids of subcategories.
     *
     * @param \IdType $parentCategoryId Parent category id.
     *
     * @return IdCollection
     */
    public function getCategoryIdsTree(IdType $parentCategoryId)
    {
        $categoryIds     = array_merge([$parentCategoryId->asInt()],
                                       $this->_getCategoryIdsTreeArray($parentCategoryId));
        $categoryIdTypes = [];
        
        foreach ($categoryIds as $categoryId) {
            $categoryIdTypes[] = new IdType($categoryId);
        }
        
        $categoryIdCollection = MainFactory::create('IdCollection', $categoryIdTypes);
        
        return $categoryIdCollection;
    }
    
    
    /**
     * Returns an id collection with the ids of the active subcategories.
     *
     * @param \IdType $parentCategoryId Parent category id.
     *
     * @return IdCollection
     */
    public function getActiveCategoryIdsTree(IdType $parentCategoryId)
    {
        $categoryIds     = array_merge([$parentCategoryId->asInt()],
                                       $this->_getActiveCategoryIdsTreeArray($parentCategoryId));
        $categoryIdTypes = [];
        
        foreach ($categoryIds as $categoryId) {
            $categoryIdTypes[] = new IdType($categoryId);
        }
        
        return MainFactory::create('IdCollection', $categoryIdTypes);
    }
    
    
    /**
     * Fetches the ids of the subcategories from the passed parent category.
     *
     * @param \IdType $parentCategoryId Parent id from category of subcategory ids to be fetched.
     *
     * @return array Contains unsorted ids of sub categories from passed parent category id.
     */
    protected function _getCategoryIdsTreeArray(IdType $parentCategoryId)
    {
        $categoryIds = array_map(function ($el) {
            return (int)$el['categories_id'];
        },
            $this->db->select('categories_id')
                ->from('categories')
                ->where('parent_id', $parentCategoryId->asInt())
                //->where('categories_status', 1)
                ->get()
                ->result_array());
        
        foreach ($categoryIds as $categoryId) {
            $categoryIds = array_merge($categoryIds, $this->_getCategoryIdsTreeArray(new IdType($categoryId)));
        }
        
        return $categoryIds;
    }
    
    
    /**
     * Fetches the ids of the active subcategories from the passed parent category.
     *
     * @param \IdType $parentCategoryId Parent id from category of subcategory ids to be fetched.
     *
     * @return array Contains unsorted ids of sub categories from passed parent category id.
     */
    protected function _getActiveCategoryIdsTreeArray(IdType $parentCategoryId)
    {
        $categoryIds = array_map(function ($el) {
            return (int)$el['categories_id'];
        },
            $this->db->select('categories_id')
                ->from('categories')
                ->where('parent_id', $parentCategoryId->asInt())
                ->where('categories_status', 1)
                ->get()
                ->result_array());
        
        $catIds = [];
        foreach ($categoryIds as $categoryId) {
            foreach ($this->_getActiveCategoryIdsTreeArray(new IdType($categoryId)) as $subCategoryId) {
                $catIds[] = $subCategoryId;
            }
        }
        
        return array_merge($categoryIds, $catIds);
    }
    
    
    /**
     * Creates a category instance.
     *
     * @param array $categoryData            Category query result.
     * @param array $categoryDescriptionData Category description query result.
     *
     * @return StoredCategory Returns the complete category object.
     *
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    protected function _createCategoryByArray(array $categoryData, array $categoryDescriptionData)
    {
        $category = $this->categoryFactory->createStoredCategory(new IdType($categoryData['categories_id']));
        $category->setActive(new BoolType((boolean)$categoryData['categories_status']));
        $category->setParentId(new IdType($categoryData['parent_id']));
        $category->setSortOrder(new IntType((int)$categoryData['sort_order']));
        $category->setAddedDateTime(new EmptyDateTime($categoryData['date_added']));
        $category->setLastModifiedDateTime(new EmptyDateTime($categoryData['last_modified']));
        $category->setImage(new StringType((string)$categoryData['categories_image']));
        $category->setIcon(new StringType((string)$categoryData['categories_icon']));
        $category->setOpenGraphImage(new StringType((string)$categoryData['categories_ogimage']));
        
        // Set language specific data.
        foreach ($categoryDescriptionData as $row) {
            $languageCode = new LanguageCode(new NonEmptyStringType((string)$row['language_code']));
            
            $category->setName(new StringType((string)$row['categories_name']), $languageCode);
            $category->setHeadingTitle(new StringType((string)$row['categories_heading_title']), $languageCode);
            $category->setDescription(new StringType((string)$row['categories_description']), $languageCode);
            $category->setDescriptionBottom(new StringType((string)$row['categories_description_bottom']),
                                            $languageCode);
            $category->setMetaTitle(new StringType((string)$row['categories_meta_title']), $languageCode);
            $category->setMetaDescription(new StringType((string)$row['categories_meta_description']), $languageCode);
            $category->setMetaKeywords(new StringType((string)$row['categories_meta_keywords']), $languageCode);
            $category->setUrlKeywords(new StringType((string)$row['gm_url_keywords']), $languageCode);
            $category->setImageAltText(new StringType((string)$row['gm_alt_text']), $languageCode);
        }
        
        return $category;
    }
}
