<?php
/* --------------------------------------------------------------
   GoogleRichSnippetThemeContentView.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GoogleRichSnippetThemeContentView
 */
class GoogleRichSnippetThemeContentView extends ThemeContentView
{
    protected $active;
    protected $fsk18_purchasable;
    protected $price_status;
    protected $quantity;
    protected $date_available;
    protected $price;
    protected $currency;
    protected $products_name;
    protected $breadcrumb_array;
    protected $breadcrumb_separator;
    protected $review_date_created;
    protected $rating;
    protected $rating_count;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->active = true;
    }
    
    
    /**
     * @return bool
     */
    protected function is_rich_snippet_active()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'fsk18_purchasable',
                                                                        'quantity',
                                                                        'price_status',
                                                                        'active'
                                                                    ]);
        
        if (!empty($t_uninitialized_array)) {
            trigger_error('Variable(s) ' . implode(', ', $t_uninitialized_array) . ' do(es) not exist in class '
                          . get_class($this) . ' or is/are null',
                          E_USER_ERROR);
        }
        
        $isActive = ((bool)$this->fsk18_purchasable === false
                     && (STOCK_ALLOW_CHECKOUT === 'true' || (($this->quantity > 0) && STOCK_ALLOW_CHECKOUT === 'false'))
                     && (string)$this->price_status === '0'
                     && (bool)$this->active === true);
        
        return $isActive;
    }
    
    
    /**
     * @return string
     */
    public function get_breadcrumb_snippet()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'breadcrumb_array',
                                                                        'breadcrumb_separator',
                                                                        'active'
                                                                    ]);
        if (!empty($t_uninitialized_array)) {
            trigger_error('Variable(s) ' . implode(', ', $t_uninitialized_array) . ' do(es) not exist in class '
                          . get_class($this) . ' or is/are null',
                          E_USER_ERROR);
            
            return '';
        }
    
        $ldJsonData = [];
        if ($this->active) {
            $currentUrl = GM_HTTP_SERVER . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
            $ldJsonData = [
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => []
            ];
            $position   = 0;
            foreach ($this->breadcrumb_array as $breadcrumbItem) {
                $position++;
                $link                            = $position
                                                   === count($this->breadcrumb_array) ? $currentUrl : $breadcrumbItem['link'];
                $ldJsonData['itemListElement'][] = [
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'name'     => $breadcrumbItem['title'],
                    'item'     => $link,
                ];
            }
            $ldJson = json_encode($ldJsonData);
        }
    
        $this->set_content_template('layout_breadcrumb_content.html');
        $this->set_flat_assigns(true);
        $this->set_content_data('is_active', $this->active);
        $this->set_content_data('breadcrumb_array', $this->breadcrumb_array);
        $this->set_content_data('breadcrumb_ldjson', $ldJson);
        $this->set_content_data('breadcrumb_separator', $this->breadcrumb_separator);
        $this->set_content_data('current_url', $currentUrl);
        
        return $this->get_html();
    }
    
    
    /**
     * @return array
     * @link https://developers.google.com/structured-data/rich-snippets/reviews
     */
    public function get_review_snippet()
    {
        $t_rich_snippet_array                                  = [];
        $t_rich_snippet_array['product_itemprop_reviews']      = '';
        $t_rich_snippet_array['review_itemscope']              = '';
        $t_rich_snippet_array['review_itemprop_about']         = '';
        $t_rich_snippet_array['review_itemprop_reviewBody']    = '';
        $t_rich_snippet_array['review_itemprop_author']        = '';
        $t_rich_snippet_array['author_itemscope']              = '';
        $t_rich_snippet_array['author_itemprop_name']          = '';
        $t_rich_snippet_array['review_itemprop_datePublished'] = '';
        $t_rich_snippet_array['review_itemprop_reviewRating']  = '';
        $t_rich_snippet_array['rating_itemscope']              = '';
        $t_rich_snippet_array['rating_itemprop_ratingValue']   = '';
        
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'products_name',
                                                                        'review_date_created',
                                                                        'rating'
                                                                    ]);
        
        if (empty($t_uninitialized_array)) {
            if ($this->is_rich_snippet_active()) {
                $t_rich_snippet_array['product_itemprop_reviews'] = ' itemprop="review"';
                
                $t_rich_snippet_array['review_itemscope']           = ' itemscope="itemscope" itemtype="http://schema.org/Review"';
                $t_rich_snippet_array['review_itemprop_about']      = ' <meta itemprop="about" content="'
                                                                      . $this->products_name . '">';
                $t_rich_snippet_array['review_itemprop_reviewBody'] = ' itemprop="reviewBody"';
                $t_rich_snippet_array['review_itemprop_author']     = ' itemprop="author"';
                
                $t_rich_snippet_array['author_itemscope']     = ' itemscope="itemscope" itemtype="http://schema.org/Person"';
                $t_rich_snippet_array['author_itemprop_name'] = ' itemprop="name"';
                
                $t_rich_snippet_array['review_itemprop_datePublished'] = '<meta itemprop="datePublished" content="'
                                                                         . $this->review_date_created . '">';
                $t_rich_snippet_array['review_itemprop_reviewRating']  = ' itemprop="reviewRating"';
                
                $t_rich_snippet_array['rating_itemscope']            = ' itemscope="itemscope" itemtype="http://schema.org/Rating"';
                $t_rich_snippet_array['rating_itemprop_ratingValue'] = '<meta itemprop="ratingValue" content="'
                                                                       . $this->rating . '">';
            }
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
        
        return $t_rich_snippet_array;
    }
    
    
    /**
     * @param boolean $p_active
     */
    public function set_active($p_active)
    {
        $this->active = (bool)$p_active;
    }
    
    
    /**
     * @return boolean
     */
    public function get_active()
    {
        return $this->active;
    }
    
    
    /**
     * @param array $p_breadcrumb_array
     */
    public function set_breadcrumb_array(array $p_breadcrumb_array)
    {
        $this->breadcrumb_array = $p_breadcrumb_array;
    }
    
    
    /**
     * @return array
     */
    public function get_breadcrumb_array()
    {
        return $this->breadcrumb_array;
    }
    
    
    /**
     * @param string $p_breadcrumb_separator
     */
    public function set_breadcrumb_separator($p_breadcrumb_separator)
    {
        $this->breadcrumb_separator = (string)$p_breadcrumb_separator;
    }
    
    
    /**
     * @return string
     */
    public function get_breadcrumb_separator()
    {
        return $this->breadcrumb_separator;
    }
    
    
    /**
     * @param string $p_currency
     */
    public function set_currency($p_currency)
    {
        $this->currency = (string)$p_currency;
    }
    
    
    /**
     * @return string
     */
    public function get_currency()
    {
        return $this->currency;
    }
    
    
    /**
     * @param string $p_date_available
     */
    public function set_date_available($p_date_available)
    {
        $this->date_available = (string)$p_date_available;
    }
    
    
    /**
     * @return string
     */
    public function get_date_available()
    {
        return $this->date_available;
    }
    
    
    /**
     * @param boolean $p_fsk18_purchasable
     */
    public function set_fsk18($p_fsk18_purchasable)
    {
        $this->fsk18_purchasable = (bool)$p_fsk18_purchasable;
    }
    
    
    /**
     * @return boolean
     */
    public function get_fsk18_purchasable()
    {
        return $this->fsk18_purchasable;
    }
    
    
    /**
     * @param double $p_price
     */
    public function set_price($p_price)
    {
        $this->price = (double)$p_price;
    }
    
    
    /**
     * @return double
     */
    public function get_price()
    {
        return $this->price;
    }
    
    
    /**
     * @param int $p_price_status
     */
    public function set_price_status($p_price_status)
    {
        $this->price_status = (int)$p_price_status;
    }
    
    
    /**
     * @return int
     */
    public function get_price_status()
    {
        return $this->price_status;
    }
    
    
    /**
     * @param string $p_products_name
     */
    public function set_products_name($p_products_name)
    {
        $this->products_name = (string)$p_products_name;
    }
    
    
    /**
     * @return string
     */
    public function get_products_name()
    {
        return $this->products_name;
    }
    
    
    /**
     * @param double $p_quantity
     */
    public function set_quantity($p_quantity)
    {
        $this->quantity = (double)$p_quantity;
    }
    
    
    /**
     * @return double
     */
    public function get_quantity()
    {
        return $this->quantity;
    }
    
    
    /**
     * @param int $p_rating
     */
    public function set_rating($p_rating)
    {
        $this->rating = (int)$p_rating;
    }
    
    
    /**
     * @return int
     */
    public function get_rating()
    {
        return $this->rating;
    }
    
    
    /**
     * @param int $p_rating_count
     */
    public function set_rating_count($p_rating_count)
    {
        $this->rating_count = (int)$p_rating_count;
    }
    
    
    /**
     * @return int
     */
    public function get_rating_count()
    {
        return $this->rating_count;
    }
    
    
    /**
     * @param string $p_review_date_created
     */
    public function set_review_date_created($p_review_date_created)
    {
        $this->review_date_created = (string)$p_review_date_created;
    }
    
    
    /**
     * @return string
     */
    public function get_review_date_created()
    {
        return $this->review_date_created;
    }
}
