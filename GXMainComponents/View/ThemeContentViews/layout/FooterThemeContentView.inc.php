<?php
/* --------------------------------------------------------------
   FooterThemeContentView.inc.php 2022-08-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(add_a_quickie.php,v 1.10 2001/12/19); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: add_a_quickie.php,v 1.1 2004/04/26 20:26:42 fanta2k Exp $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class FooterThemeContentView extends ThemeContentView
{
    protected $language_id;
    protected $customer_status_id;
    protected $content_data_array = [];
    protected $footerColumns      = [];
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('layout_footer.html');
    }
    
    
    protected function set_validtaion_rules()
    {
        $this->validation_rules_array['language_id']        = ['type' => 'int'];
        $this->validation_rules_array['customer_status_id'] = ['type' => 'int'];
        $this->validation_rules_array['content_data_array'] = ['type' => 'array'];
        $this->validation_rules_array['footerColumns']      = ['type' => 'array'];
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'customer_status_id',
                                                                        'language_id',
                                                                        'footerColumns'
                                                                    ]);
        
        if (empty($t_uninitialized_array)) {
            $this->get_data();
            $this->set_data();
            $this->assignMoreAboutColumnContent();
            $this->assignColumnContent($this->footerColumns);
        } else {
            trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    protected function get_data()
    {
        $t_query         = $this->get_sql_query('199');
        $t_content_query = xtc_db_query($t_query);
        if (xtc_db_num_rows($t_content_query) == 1) {
            $this->content_data_array = xtc_db_fetch_array($t_content_query);
        }
        $this->build_html = true;
    }
    
    
    protected function set_data()
    {
        if (is_array($this->content_data_array) && empty($this->content_data_array) == false) {
            $t_content = '';
            
            if ($this->content_data_array['content_file'] != '') {
                ob_start();
                
                if (strpos($this->content_data_array['content_file'], '.txt')) {
                    echo '<pre>';
                }
                
                include(DIR_FS_CATALOG . 'media/content/' . $this->content_data_array['content_file']);
                
                if (strpos($this->content_data_array['content_file'], '.txt')) {
                    echo '</pre>';
                }
                
                $t_content = ob_get_contents();
                ob_end_clean();
            } else {
                $t_content = $this->content_data_array['content_text'];
            }
            
            $this->content_array['HTML'] = $t_content;
        }
        
        if ($this->build_html == true) {
            $t_footer_html = '';
            
            $footer        = gm_get_content('GM_FOOTER', $_SESSION['languages_id']) ? : gm_get_conf('GM_FOOTER');
            $t_footer_html .= $footer;
            
            // COPYRIGHT
            $this->content_array['COPYRIGHT_FOOTER'] = $t_footer_html;
        }
    }
    
    
    /**
     * Used to get the content of the first footer column. Overload this if you want to include an other menu box
     */
    public function assignMoreAboutColumnContent()
    {
        $contentBoxContent = MainFactory::create_object('ContentBoxThemeContentView');
        $contentBoxContent->setFileFlagName('content');
        $contentBoxContent->setRequestUri($_SERVER['REQUEST_URI']);
        $contentBoxContent->setCustomerStatusId($_SESSION['customers_status']['customers_status_id']);
        $contentBoxContent->setLanguagesId($_SESSION['languages_id']);

        $boxHtml = $contentBoxContent->get_html();
        $boxData = $contentBoxContent->get_content_array()["CONTENT_LINKS_DATA"];
        
        $this->content_array['MORE_ABOUT'] = $boxHtml;
        $this->content_array['MORE_ABOUT_DATA'] = $boxData;
    }
    
    
    /**
     * Gets the contents of the other footer columns
     *
     * @param array $footerColumns
     */
    public function assignColumnContent(array $footerColumns)
    {
        foreach ($footerColumns as $column) {
            $query        = $this->get_sql_query($column);
            $contentQuery = xtc_db_query($query);
            if (xtc_db_num_rows($contentQuery) == 1) {
                $result = xtc_db_fetch_array($contentQuery);
                $contentFile = strpos($result['content_file'], '..') === false ? $result['content_file'] : '';
                
                if ($contentFile !== '' && file_exists(DIR_FS_CATALOG . 'media/content/' . $contentFile)) {
                    ob_start();
                    
                    if (strpos($contentFile, '.txt')) {
                        echo '<pre>';
                    }
                    
                    include(DIR_FS_CATALOG . 'media/content/' . $contentFile);
                    
                    if (strpos($contentFile, '.txt')) {
                        echo '</pre>';
                    }
                    
                    $content = ob_get_contents();
                    ob_end_clean();
                } else {
                    $content = $result['content_text'];
                }
                
                $this->content_array['FOOTER_COL_' . $column]        = $content;
                $this->content_array['FOOTER_COL_HEADER_' . $column] = $result['content_heading'];
            }
        }
    }
    
    
    protected function get_sql_query($group_id)
    {
        $t_group_check = '';
        if (GROUP_CHECK == 'true') {
            $t_group_check = ' AND group_ids LIKE "%c_' . $this->customer_status_id . '_group%"';
        }
        
        $t_query = 'SELECT
						*
					FROM
						' . TABLE_CONTENT_MANAGER . '
					WHERE
						content_group = "' . $group_id . '"
					AND
						content_status = 1
					AND
						languages_id = "' . $this->language_id . '"
						' . $t_group_check;
        
        return $t_query;
    }
}