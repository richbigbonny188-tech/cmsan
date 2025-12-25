<?php
/* --------------------------------------------------------------
  AddressBookThemeContentView.inc.php 2022-08-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(address_book.php,v 1.57 2003/05/29); www.oscommerce.com
  (c) 2003	 nextcommerce (address_book.php,v 1.14 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: address_book.php 867 2005-04-21 18:35:29Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_name.inc.php');
require_once(DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');

class AddressBookThemeContentView extends ThemeContentView
{
    protected $customer_id;
    protected $customer_default_address_id;
    protected $addresses_data_array;
    protected $coo_message_stack;
    protected $immutable_classes;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('address_book.html');
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
    }
    
    
    protected function set_validation_rules()
    {
        $this->validation_rules_array['customer_id']                 = ['type' => 'int'];
        $this->validation_rules_array['customer_default_address_id'] = ['type' => 'int'];
        $this->validation_rules_array['addresses_data_array']        = ['type' => 'array'];
        $this->validation_rules_array['coo_message_stack']           = [
            'type'        => 'object',
            'object_type' => 'messageStack'
        ];
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'customer_id',
                                                                        'customer_default_address_id',
                                                                        'coo_message_stack'
                                                                    ]);
        
        if (empty($t_uninitialized_array)) {
            $this->add_error_messages();
            $this->get_immutable_classes();
            $this->load_addresses_data_array();
            $this->add_data();
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    protected function add_error_messages()
    {
        if ($this->coo_message_stack->size('addressbook') > 0) {
            $this->content_array['error'] = $this->coo_message_stack->output('addressbook');
        }
    }
    
    
    /**
     * determines address_classes which MUST NOT be editable by the customer
     *
     * overload this method to add more class names
     */
    protected function get_immutable_classes()
    {
        $this->immutable_classes = [
            'packstation',   // Postfinder
            'postfiliale',   // Postfinder
            'packstation_2', // Parcel Shop Finder
            'postfiliale_2', // Parcel Shop Finder
            'parcelshop',    // Parcel Shop Finder
        ];
    }
    
    
    protected function load_addresses_data_array()
    {
        $this->addresses_data_array = [];
        
        $t_sql    = "SELECT
						address_book_id,
						entry_firstname as firstname,
						entry_lastname as lastname,
						entry_company as company,
						entry_street_address as street,
						entry_house_number as house_number,
						entry_street_address as street_address,
						entry_additional_info as additional_info,
						entry_suburb as suburb,
						entry_city as city,
						entry_postcode as postcode,
						entry_state as state,
						entry_zone_id as zone_id,
						entry_country_id as country_id,
						address_class
					FROM
						" . TABLE_ADDRESS_BOOK . "
					WHERE
						customers_id = '" . $this->customer_id . "'
					ORDER BY
						firstname, lastname";
        $t_result = xtc_db_query($t_sql);
        while ($t_result_array = xtc_db_fetch_array($t_result)) {
            $t_format_id = xtc_get_address_format_id($t_result_array['country_id']);
            
            if ($t_result_array['address_book_id'] == $this->customer_default_address_id) {
                $t_primary = 1;
            } else {
                $t_primary = 0;
            }
            
            $t_address_data_array                      = [];
            $t_address_data_array['NAME']              = $t_result_array['firstname'] . ' '
                                                         . $t_result_array['lastname'];
            $t_address_data_array['BUTTON_EDIT']       = '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS,
                                                                                     'edit='
                                                                                     . $t_result_array['address_book_id'],
                                                                                     'SSL') . '">'
                                                         . xtc_image_button('small_edit.gif',
                                                                            SMALL_IMAGE_BUTTON_EDIT) . '</a>';
            $t_address_data_array['BUTTON_DELETE']     = '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS,
                                                                                     'delete='
                                                                                     . $t_result_array['address_book_id'],
                                                                                     'SSL') . '">'
                                                         . xtc_image_button('small_delete.gif',
                                                                            SMALL_IMAGE_BUTTON_DELETE) . '</a>';
            $t_address_data_array['ADDRESS']           = xtc_address_format($t_format_id,
                                                                            $t_result_array,
                                                                            true,
                                                                            ' ',
                                                                            '<br />');
            $t_address_data_array['STREET']            = $t_result_array['street'];
            $t_address_data_array['HOUSENUMBER']       = $t_result_array['house_number'];
            $t_address_data_array['CITY']              = $t_result_array['city'];
            $t_address_data_array['POSTCODE']          = $t_result_array['postcode'];
            $t_address_data_array['ADDRESS_CLASS']     = $t_result_array['address_class'];
            $t_address_data_array['ADDITIONAL_INFO']   = $t_result_array['additional_info'];
            $t_address_data_array['PRIMARY']           = $t_primary;
            $t_address_data_array['BUTTON_EDIT_URL']   = xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS,
                                                                       'edit=' . $t_result_array['address_book_id'],
                                                                       'SSL');
            $t_address_data_array['BUTTON_DELETE_URL'] = xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS,
                                                                       'delete=' . $t_result_array['address_book_id'],
                                                                       'SSL');
            
            if (in_array($t_result_array['address_class'], $this->immutable_classes, true)) {
                $t_address_data_array['IS_IMMUTABLE'] = true;
                $immutableSummaryElements = [
                    $t_address_data_array['ADDITIONAL_INFO'],
                    trim($t_address_data_array['STREET'] . ' ' . $t_address_data_array['HOUSENUMBER']),
                    trim($t_address_data_array['POSTCODE'] . ' ' . $t_address_data_array['CITY']),
                ];
                $t_address_data_array['IMMUTABLE_SUMMARY'] = implode(', ', $immutableSummaryElements);
            }
            
            $this->addresses_data_array[] = $t_address_data_array;
        }
    }
    
    
    protected function add_data()
    {
        $this->content_array['ADDRESS_DEFAULT'] = xtc_address_label($this->customer_id,
                                                                    $this->customer_default_address_id,
                                                                    true,
                                                                    ' ',
                                                                    '<br />');
        
        $this->content_array['addresses_data']   = $this->addresses_data_array;
        $this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_ACCOUNT, '', 'SSL');
        $this->content_array['BUTTON_NEW_URL']   = xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL');
        $this->content_array['ADDRESS_COUNT']    = sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES);
        if ((bool)gm_get_conf('MODULE_CENTER_PARCELSHOPFINDER_INSTALLED') === true) {
            $this->content_array['BUTTON_PARCELSHOPFINDER_URL'] = xtc_href_link('shop.php',
                                                                                'do=Parcelshopfinder',
                                                                                'SSL');
        }
    }
}
