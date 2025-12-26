<?php
/* --------------------------------------------------------------
   ProductReviewsThemeContentView.inc.php 2018-11-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_reviews.php,v 1.47 2003/02/13); www.oscommerce.com
   (c) 2003	 nextcommerce (product_reviews.php,v 1.12 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews.php 1243 2005-09-25 09:33:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * Class ProductReviewsThemeContentView
 */
class ProductReviewsThemeContentView extends ThemeContentView
{
    protected $product;
    protected $reviewsArray = [];
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('products_reviews.html');
        $this->set_flat_assigns(true);
    }
    
    
    public function prepare_data()
    {
        $this->_assignUrls();
        $this->_assignTabContent();
        
        if ($this->product->getReviewsCount() > 0) {
            $this->_assignReviews();
        } else {
            $this->set_content_data('module_content', []);
        }
    }
    
    
    /**
     * @deprecated
     */
    protected function _assignDeprecated()
    {
        //deprecated
    }
    
    
    protected function _assignUrls()
    {
        $this->set_content_data('BUTTON_LINK',
                                xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,
                                              xtc_product_link($this->product->data['products_id'],
                                                               $this->product->data['products_name'])));
    }
    
    
    protected function _assignReviews()
    {
        $this->reviewsArray = $this->product->getReviews(PRODUCT_REVIEWS_VIEW);
        $this->set_content_data('module_content', $this->reviewsArray);
    }
    
    
    protected function _assignTabContent()
    {
        $this->set_content_data('showAsTab', gm_get_conf('SHOW_RATING_AS_TAB'));
    }
    
    
    /**
     * @param product $product
     */
    public function setProduct(product $product)
    {
        $this->product = $product;
    }
    
    
    /**
     * @return product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
