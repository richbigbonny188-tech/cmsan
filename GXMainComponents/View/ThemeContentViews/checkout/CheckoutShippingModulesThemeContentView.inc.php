<?php
/* --------------------------------------------------------------
  CheckoutShippingModulesThemeContentView.inc.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_shipping.php 1037 2005-07-17 15:25:32Z gwinger $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_count_shipping_modules.inc.php');

class CheckoutShippingModulesThemeContentView extends ThemeContentView
{
    protected $coo_xtc_price;
    protected $free_shipping;
    protected $shipping_free_over;
    protected $quotes_array; // shipping modules array
    protected $selected_shipping_method;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('checkout_shipping_modules.html');
        $this->set_flat_assigns(true);
    }
    
    
    public function prepare_data()
    {
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'free_shipping',
                                                                        'shipping_free_over',
                                                                        'quotes_array',
                                                                        'coo_xtc_price'
                                                                    ]);
        if (empty($t_uninitialized_array)) {
            if (xtc_count_shipping_modules() > 0) {
                $this->_assignShippingModules();
                $this->_assignFreeShippingData();
            }
        } else {
            trigger_error("Variable(s) " . implode(', ',
                                                   $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
    }
    
    
    protected function _assignShippingModules()
    {
        $shippingModulesArray = [];
        
        if ($this->free_shipping) {
            if ($this->_getSelfpickupKey() !== false) {
                $shippingModulesArray = $this->_buildShippingMethodsArrayForFreeShipping($this->_getSelfpickupKey());
            }
        } else {
            $shippingModulesArray = $this->_buildShippingModulesArray();
        }
        
        // use setter to allow overloading (compatibility to GX2.1 overloads)
        $this->set_quotes_array($shippingModulesArray);
        
        $this->set_content_data('module_content', $this->quotes_array);
    }
    
    
    /**
     * @return int|bool
     */
    protected function _getSelfpickupKey()
    {
        foreach ($this->quotes_array AS $key => $value) {
            if (strpos($this->quotes_array[$key]['id'], 'selfpickup') === 0) {
                return $key;
            }
        }
        
        return false;
    }
    
    
    /**
     * @param int $p_selfpickupKey
     *
     * @return array
     */
    protected function _buildShippingMethodsArrayForFreeShipping($p_selfpickupKey)
    {
        $shippingModulesArray = [];
        
        // add free shipping data
        $shippingModulesArray[] = [
            'id'      => 'free_free',
            'module'  => FREE_SHIPPING_TITLE,
            'methods' => [
                [
                    'id'            => 'free_free',
                    'title'         => sprintf(FREE_SHIPPING_DESCRIPTION,
                                               $this->coo_xtc_price->xtcFormat($this->shipping_free_over,
                                                                               true,
                                                                               0,
                                                                               true)),
                    'cost'          => 0,
                    'radio_buttons' => 0,
                    'checked'       => 1,
                    'price'         => $this->coo_xtc_price->xtcFormat(0, true),
                    'radio_field_data' => [
                        'shipping',
                        'free_free',
                        true,
                        'id='
                    ]
                ]
            ]
        ];
        
        // copy selfpickup data into shipping methods array
        $shippingModulesArray[1] = $this->quotes_array[$p_selfpickupKey];
        
        // uncheck selfpickup selection
        unset($shippingModulesArray[1]['methods'][0]['checked']);
        
        // add missing selfpickup data
        $shippingModulesArray[1]['id']                          = 'selfpickup_selfpickup';
        $shippingModulesArray[1]['methods'][0]['id']            = 'selfpickup_selfpickup';
        $shippingModulesArray[1]['methods'][0]['radio_buttons'] = 1;
        $shippingModulesArray[1]['methods'][0]['price']         = $this->coo_xtc_price->xtcFormat(0, true);
        $shippingModulesArray[1]['methods'][0]['radio_field_data'] = [
            'shipping',
            'selfpickup_selfpickup',
            false,
            'id='
        ];
        
        if (count($shippingModulesArray) > 0) {
            $this->set_content_data('has_multiple_options', true);
        }
    
        $shippingModulesArray = $this->addShippingOptions($shippingModulesArray);
    
        return $shippingModulesArray;
    }
    
    
    /**
     * @return array
     */
    protected function _buildShippingModulesArray()
    {
        $shippingModulesArray = $this->quotes_array;
        
        $modulesCount = count($shippingModulesArray);
        
        // loop through shipping modules to add missing data
        foreach ($shippingModulesArray as $key => $methodDataArray) {
            $shippingModulesArray[$key] = $this->extendQuotesArray($shippingModulesArray[$key]);
            
            if (!isset($shippingModulesArray[$key]['error'])) {
                for ($j = 0, $methodsCount = count($shippingModulesArray[$key]['methods']); $j < $methodsCount; $j++) {
                    // set the radio button to be checked if it is the method chosen
                    $shippingModulesArray[$key]['methods'][$j]['radioButtons'] = $j;
                    $checked                                                   = (($shippingModulesArray[$key]['id']
                                                                                   . '_'
                                                                                   . $shippingModulesArray[$key]['methods'][$j]['id']
                                                                                   == $this->selected_shipping_method) ? true : false);
                    
                    if (($checked == true) || ($modulesCount == 1 && $methodsCount == 1)) {
                        $shippingModulesArray[$key]['methods'][$j]['checked'] = 1;
                    }
                    
                    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0) {
                        $shippingModulesArray[$key]['tax'] = 0;
                    }
                    
                    if (($modulesCount > 1) || ($methodsCount > 1)) {
                        $this->set_content_data('has_multiple_options', true);
                        $shippingModulesArray[$key]['methods'][$j]['price']            = $this->coo_xtc_price->xtcFormat(xtc_add_tax($shippingModulesArray[$key]['methods'][$j]['cost'],
                                                                                                                                     $shippingModulesArray[$key]['tax'] ?? null),
                                                                                                                         true,
                                                                                                                         0,
                                                                                                                         true);
                        $shippingModulesArray[$key]['methods'][$j]['radio_field_data'] = [
                            xtc_parse_input_field_data('shipping', ['"' => '&quot;']),
                            xtc_parse_input_field_data($shippingModulesArray[$key]['id'],
                                                       ['"' => '&quot;']) . '_'
                            . xtc_parse_input_field_data($shippingModulesArray[$key]['methods'][$j]['id'],
                                                         ['"' => '&quot;']),
                            (bool)xtc_parse_input_field_data($checked, ['"' => '&quot;']),
                            'id='
                        ];
                    } else {
                        $this->set_content_data('has_multiple_options', false);
                        $shippingModulesArray[$key]['methods'][$j]['price'] = $this->coo_xtc_price->xtcFormat(xtc_add_tax($shippingModulesArray[$key]['methods'][$j]['cost'],
                                                                                                                          $shippingModulesArray[$key]['tax'] ?? 0),
                                                                                                              true,
                                                                                                              0,
                                                                                                              true);
                    }
                    $this->set_content_data('selected_shipping_method',
                                            $shippingModulesArray[$key]['id'] . '_'
                                            . $shippingModulesArray[$key]['methods'][$j]['id']);
                }
            }
        }
        
        $shippingModulesArray = $this->addShippingOptions($shippingModulesArray);
        
        return $shippingModulesArray;
    }
    
    protected function addShippingOptions(array $shippingModules): array
    {
        $shippingModules = $this->addGeschaeftskundenversandShippingOptions($shippingModules);

        return $shippingModules;
    }
    
    protected function addGeschaeftskundenversandShippingOptions(array $shippingModules): array
    {
        if ((bool)gm_get_conf('MODULE_CENTER_GESCHAEFTSKUNDENVERSAND_INSTALLED') === false) {
            return $shippingModules;
        }
        /** @var GeschaeftskundenversandConfigurationStorage $configurationStorage */
        $configurationStorage = MainFactory::create('GeschaeftskundenversandConfigurationStorage');
        $optionsModules = explode(',', $configurationStorage->get('checkout_preferences_modules'));
        if (count($optionsModules) === 0) {
            return $shippingModules;
        }
    
        foreach ($shippingModules as $key => $moduleData) {
            $moduleCode = explode('_', $moduleData['id'])[0];
            if (in_array($moduleCode, $optionsModules, true)) {
                $shippingModules[$key]['shipping_options'] = $this->getGeschaeftskundenversandShippingOptionsForModule($moduleData['id']);
            }
        }
    
        return $shippingModules;
    }
    
    protected function getGeschaeftskundenversandShippingOptionsForModule(string $moduleCode): array
    {
        /** @var GeschaeftskundenversandConfigurationStorage $configurationStorage */
        $configurationStorage = MainFactory::create('GeschaeftskundenversandConfigurationStorage');
        $preferredDayOffset   = (int)$configurationStorage->get('preferred_day_offset');
        $nextWorkdays         = self::findNextWorkdays(6, $preferredDayOffset);
        $preferredDays        = [
            ['value' => '', 'label' => 'no_preferred_day'],
        ];
        foreach ($nextWorkdays as $workday) {
            $dt              = new \DateTimeImmutable($workday);
            $label           = xtc_date_short($dt->format('Y-m-d') . ' 00:00:00', $_SESSION['languages_id']);
            $preferredDays[] = [
                'value' => $dt->format('Y-m-d'),
                'label' => $label,
            ];
        }
    
        $usePreferredNeighbour = (bool)$configurationStorage->get('use_preferred_neighbour');
        $usePreferredLocation  = (bool)$configurationStorage->get('use_preferred_location');
        $usePreferredDay       = (bool)$configurationStorage->get('use_preferred_day');
    
        $options = [
            [
                'key'         => 'dhl_heading',
                'type'        => 'heading',
                'label'       => 'shipping_option_dhl_heading',
                'description' => 'shipping_option_dhl_description',
            ],
        ];
        if ($usePreferredNeighbour) {
            $options[] = [
                'key'         => 'dhl_preferred_neighbour',
                'label'       => 'shipping_option_dhl_preferred_neighbour',
                'description' => 'shipping_option_dhl_preferred_neighbour_description',
                'type'        => 'text',
                'value'       => ($_SESSION['shipping_options'][$moduleCode]['dhl_preferred_neighbour'] ?? ''),
                'placeholder' => 'preferred_neighbour_placeholder',
                'maxlength'   => 100,
            ];
        }
        if ($usePreferredLocation) {
            $options[] = [
                'key'         => 'dhl_preferred_location',
                'label'       => 'shipping_option_dhl_preferred_location',
                'description' => 'shipping_option_dhl_preferred_location_description',
                'type'        => 'text',
                'value'       => ($_SESSION['shipping_options'][$moduleCode]['dhl_preferred_location'] ?? ''),
                'placeholder' => 'preferred_location_placeholder',
                'maxlength'   => 100,
            ];
        }
        if ($usePreferredDay) {
            $options[] = [
                'key'         => 'dhl_preferred_day',
                'type'        => 'multi',
                'values'      => $preferredDays,
                'selected'    => ($_SESSION['shipping_options'][$moduleCode]['dhl_preferred_day'] ?? ''),
                'label'       => 'shipping_option_dhl_preferred_day',
                'description' => 'shipping_option_dhl_preferred_day_description',
            ];
        }
    
        return $options;
    }
    
    
    protected static function findNextWorkdays(int $numOfDays = 6, int $offset = 0): array
    {
        $numOfDays = $numOfDays < 0 ? (-1 * $numOfDays) : $numOfDays;
        ++$offset;
        $workdays = [];
        $holidays = [
            '2022-04-15', '2022-04-18', '2022-05-01', '2022-05-26', '2022-06-06', '2022-10-03', '2022-12-25', '2022-12-26',
            '2023-01-01', '2023-04-07', '2023-04-10', '2023-05-01', '2023-05-18', '2023-05-29', '2023-10-03', '2023-12-25', '2023-12-26',
            '2024-01-01', '2024-03-29', '2024-04-01', '2024-05-01', '2024-05-09', '2024-05-20', '2024-10-03', '2024-12-25', '2024-12-26',
        ];
        while (count($workdays) < $numOfDays) {
            $dt = new \DateTimeImmutable("$offset days");
            $date = $dt->format('Y-m-d');
            $weekday = (int)$dt->format('w');
            if ($weekday > 0 && !in_array($date, $holidays)) {
                $workdays[] = $date;
            }
            $offset++;
        }
        
        return $workdays;
    }
    
    
    public function extendQuotesArray($quotesArray)
    {
        $logoFile           = 'default.png';
        $logoExtensions     = ['.svg', '.png', '.gif', '.jpg'];
        $languageExtensions = [strtolower($_SESSION['language_code']), ''];
        foreach ($languageExtensions as $languageExtension) {
            $languageExtension = empty($languageExtension) ? $languageExtension : '_' . $languageExtension;
            foreach ($logoExtensions as $extension) {
                $logoFileCandidate = $quotesArray['id'] . $languageExtension . $extension;
                if (file_exists(DIR_FS_CATALOG . 'images/icons/shipping/' . $logoFileCandidate)) {
                    $logoFile = $logoFileCandidate;
                    break 2;
                }
            }
        }
        
        $defaults = [
            'logo_url' => xtc_href_link('images/icons/shipping/' . $logoFile,
                                        '',
                                        'SSL',
                                        false,
                                        false,
                                        false,
                                        true,
                                        true),
            'logo_alt' => $quotesArray['module']
        ];
        
        if (!empty($quotesArray['icon'])
            && preg_match('/<img((\s+src="(?<src>.+?)")|(\s+alt="(?<alt>.+?)")|(\s+\w+=".*?"))*\s*\/?>/',
                          $quotesArray['icon'],
                          $matches) !== false) {
            if (array_key_exists('src', $matches)) {
                $defaults['logo_url'] = $matches['src'];
            }
            if (array_key_exists('alt', $matches)) {
                $defaults['logo_alt'] = $matches['alt'];
            }
        }
        
        $extendedArray = array_merge($defaults, $quotesArray);
        
        return $extendedArray;
    }
    
    
    protected function _assignFreeShippingData()
    {
        $this->set_content_data('FREE_SHIPPING', $this->free_shipping);
        
        if ($this->free_shipping) {
            $this->set_content_data('FREE_SHIPPING_TITLE', FREE_SHIPPING_TITLE);
            $this->set_content_data('FREE_SHIPPING_DESCRIPTION',
                                    sprintf(FREE_SHIPPING_DESCRIPTION,
                                            $this->coo_xtc_price->xtcFormat($this->shipping_free_over,
                                                                            true,
                                                                            0,
                                                                            true)));
            $this->set_content_data('FREE_SHIPPING_ICON', '');
            $this->set_content_data('FREE_SHIPPING_OPTIONS', $this->getFreeShippingOptions());
        }
    }
    
    protected function getFreeShippingOptions(): array
    {
        $freeShippingOptions = array_merge([], $this->getGeschaeftskundenversandFreeShippingOptions());
        return $freeShippingOptions;
    }
    
    protected function getGeschaeftskundenversandFreeShippingOptions(): array
    {
        if ((bool)gm_get_conf('MODULE_CENTER_GESCHAEFTSKUNDENVERSAND_INSTALLED') === false) {
            return [];
        }
        /** @var GeschaeftskundenversandConfigurationStorage $configurationStorage */
        $configurationStorage = MainFactory::create('GeschaeftskundenversandConfigurationStorage');
        $optionsModules = explode(',', $configurationStorage->get('checkout_preferences_modules'));
        if (count($optionsModules) === 0 || !in_array('free', $optionsModules, true)) {
            return [];
        }
        return $this->getGeschaeftskundenversandShippingOptionsForModule('free');
    }
    
    
    /**
     * @param xtcPrice $xtcPrice
     */
    public function set_coo_xtc_price(xtcPrice $xtcPrice)
    {
        $this->coo_xtc_price = $xtcPrice;
    }
    
    
    /**
     * @return xtcPrice
     */
    public function get_coo_xtc_price()
    {
        return $this->coo_xtc_price;
    }
    
    
    /**
     * @param bool $p_isFreeShipping
     */
    public function set_free_shipping($p_isFreeShipping)
    {
        $this->free_shipping = $p_isFreeShipping;
    }
    
    
    /**
     * @return bool
     */
    public function get_free_shipping()
    {
        return $this->free_shipping;
    }
    
    
    /**
     * @param array $shippingModulesArray
     */
    public function set_quotes_array(array $shippingModulesArray)
    {
        $this->quotes_array = $shippingModulesArray;
    }
    
    
    /**
     * @return array
     */
    public function get_quotes_array()
    {
        return $this->quotes_array;
    }
    
    
    /**
     * @param string $p_shippingMethod
     */
    public function set_selected_shipping_method($p_shippingMethod)
    {
        $this->selected_shipping_method = (string)$p_shippingMethod;
    }
    
    
    /**
     * @return string
     */
    public function get_selected_shipping_method()
    {
        return $this->selected_shipping_method;
    }
    
    
    /**
     * @param double $p_shippingFreePriceLimit
     */
    public function set_shipping_free_over($p_shippingFreePriceLimit)
    {
        $this->shipping_free_over = (double)$p_shippingFreePriceLimit;
    }
    
    
    /**
     * @return double
     */
    public function get_shipping_free_over()
    {
        return $this->shipping_free_over;
    }
}
