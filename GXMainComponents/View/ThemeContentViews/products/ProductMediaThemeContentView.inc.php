<?php
/* --------------------------------------------------------------
   ProductMediaThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (products_media.php,v 1.8 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_media.php 1259 2005-09-29 16:11:19Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_filesize.inc.php');
require_once(DIR_FS_INC . 'xtc_in_array.inc.php');

/**
 * Class ProductMediaThemeContentView
 */
class ProductMediaThemeContentView extends ThemeContentView
{
    /**
     * @var int|null
     */
    protected $languageId = null;
    
    /**
     * @var int|null
     */
    protected $productId = null;
    
    /**
     * @var int|null
     */
    protected $customerStatusId = null;
    
    /**
     * @var array
     */
    protected $moduleDataArray = [];
    
    /**
     * @var array
     */
    protected $mediaFileExtensions = ['txt', 'bmp', 'jpg', 'gif', 'png', 'tif', 'jpeg', 'pjpeg'];
    
    
    // ########## CONSTRUCTOR ##########
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('product_info_media.html');
        $this->set_flat_assigns(true);
    }
    
    
    // ########## GETTER & SETTER ##########
    
    
    /**
     * Sets the language id.
     *
     * @param int $langId Language id to be set.
     *
     * @return $this|\ProductMediaThemeContentView Same instance for chained method calls.
     */
    public function setLanguageId($langId)
    {
        $this->languageId = (int)$langId;
        
        return $this;
    }
    
    
    /**
     * Returns the language id.
     *
     * @return int Language id.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Sets the product id.
     *
     * @param int $productId Product id to be set.
     *
     * @return $this|\ProductMediaThemeContentView Same instance for chained method calls.
     */
    public function setProductId($productId)
    {
        $this->productId = (int)$productId;
        
        return $this;
    }
    
    
    /**
     * Returns the product id.
     *
     * @return int Product id.
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    
    /**
     * Sets the customer status id.
     *
     * @param int $customerStatusId Customer status id to be set.
     *
     * @return $this|\ProductMediaThemeContentView Same instance for chained method calls.
     */
    public function setCustomerStatusId($customerStatusId)
    {
        $this->customerStatusId = (int)$customerStatusId;
        
        return $this;
    }
    
    
    /**
     * Returns the customer status id.
     *
     * @return int Customer status id.
     */
    public function getCustomerStatusId()
    {
        return $this->customerStatusId;
    }
    
    
    // ########## PUBLIC METHODS ##########
    
    
    /**
     * Prepare data method, used by content control to set up the content data.
     */
    public function prepare_data()
    {
        $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $selects      = 'title, content, resource, pct.name as type, pcd.id as pcd_id';
        
        $queryBuilder->select($selects)
            ->from('product_content_products as pcp')
            ->join('product_content_descriptions as pcd',
                   'pcp.product_content_id = pcd.product_content_id')
            ->join('product_content_resources as pcr', 'pcd.id = pcr.product_content_description_id')
            ->join('product_content_types as pct',
                   'pcr.product_content_types_id = pct.id');
        
        if (GROUP_CHECK === 'true') {
            $queryBuilder->join('product_content_customer_status as pccs',
                                'pcp.product_content_id = pccs.product_content_id')
                ->where('pccs.customer_status_id', (int)$_SESSION['customers_status']['customers_status_id']);
        }
        
        $queryResult = $queryBuilder->where('pcp.product_id', $this->productId)
            ->where('pcd.language_id',
                    $this->languageId)
            ->get()
            ->result_array() ? : [];
        
        $data = [];
        
        foreach ($queryResult as $result) {
            $contentFilePath = DIR_FS_CATALOG . 'media/products/' . $result['resource'];
            
            $file          = $result['type'] === 'file' ? $result['resource'] : null;
            $fileArray     = $file ? explode('.', $file) : null;
            $fileExtension = $fileArray ? $fileArray[count($fileArray) - 1] : 'link';
            
            $data[] = [
                'TYPE'         => $result['type'],
                'FA_ICON'      => FontAwesomeIconClassProvider::getClass($fileExtension),
                'BUTTON_URL'   => $this->_generateContentUrl($result),
                'CONTENT_NAME' => $result['title'],
                'FILESIZE'     => is_file($contentFilePath) ? xtc_filesize($result['resource']) : null,
                'DESCRIPTION'  => $result['content'],
            ];
        }
        
        $this->set_content_data('module_content', $data);
    }
    
    
    /**
     * Generates the redirect url for the product contents by the given product content descriptions id.
     *
     * @param array $result Result data set of query against the product content descriptions table.
     *
     * @return string Redirect url for given content.
     */
    protected function _generateContentUrl(array $result)
    {
        if ($result['type'] === 'link') {
            return xtc_href_link('shop.php', 'do=ProductMediaContent/link&pcdId=' . $result['pcd_id']);
        }
        $fileExtension = substr($result['resource'], strpos($result['resource'], '.') + 1);
        
        $contentLink  = xtc_href_link('shop.php', 'do=ProductMediaContent&pcdId=' . $result['pcd_id']);
        $downloadLink = xtc_href_link('media/products/' . $result['resource'],
                                      '',
                                      'NONSSL',
                                      true,
                                      true,
                                      false,
                                      true,
                                      true);
        
        return in_array($fileExtension, $this->mediaFileExtensions) ? $contentLink : $downloadLink;
    }
}
