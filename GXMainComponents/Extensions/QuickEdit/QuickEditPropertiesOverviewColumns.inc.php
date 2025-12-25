<?php

/* --------------------------------------------------------------
   QuickEditPropertiesOverviewColumns.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditPropertiesOverviewColumns
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditPropertiesOverviewColumns implements QuickEditPropertiesOverviewColumnsInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var array
     */
    protected $columns = [];
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    
    /**
     * QuickEditPropertiesOverviewColumns constructor.
     */
    public function __construct()
    {
        $this->db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'admin_quick_edit',
                                                         $_SESSION['languages_id']);
        
        // Products Name
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('NAME')))
            ->setName(new StringType('productsName'))
            ->setField(new StringType('products_description.products_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Combination
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('COMBINATION')))
            ->setName(new StringType('combiName'))
            ->setField(new StringType('products_properties_index.values_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Model
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('MODEL')))
            ->setName(new StringType('combiModel'))
            ->setField(new StringType('products_properties_combis.combi_model'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // EAN
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('EAN')))
            ->setName(new StringType('combiEan'))
            ->setField(new StringType('products_properties_combis.combi_ean'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Quantity
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('QUANTITY')))
            ->setName(new StringType('combiQuantity'))
            ->setField(new StringType('products_properties_combis.combi_quantity'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Shipping time
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('SHIPPINGTIME')))
            ->setName(new StringType('combiShippingStatusName'))
            ->setField(new StringType('products_properties_combis.combi_shipping_status_id'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getShipmentConfiguration());
        
        // Weight
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('WEIGHT')))
            ->setName(new StringType('combiWeight'))
            ->setField(new StringType('products_properties_combis.combi_weight'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Combi Price
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('COMBI_PRICE')))
            ->setName(new StringType('combiPrice'))
            ->setField(new StringType('products_properties_combis.combi_price'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Combi Pricetype
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('PRICETYPE')))
            ->setName(new StringType('combiPriceType'))
            ->setField(new StringType('products_properties_combis.combi_price_type'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getPriceTypeConfiguration());
    }
    
    
    /**
     * Get the DataTableColumnCollection of the table.
     *
     * @return DataTableColumnCollection Returns the DataTableColumnCollection of the table.
     */
    public function getColumns()
    {
        return MainFactory::create('DataTableColumnCollection', $this->columns);
    }
    
    
    /**
     * Serializes the data of a table column.
     *
     * @return array Returns the serialized table column data.
     */
    public function serializeColumns()
    {
        return array_map(function ($column) {
            /** @var DataTableColumn $column */
            return [
                'title'   => $column->getTitle(),
                'name'    => $column->getName(),
                'field'   => $column->getField(),
                'type'    => $column->getType(),
                'source'  => $column->getSource(),
                'options' => $column->getOptions(),
                'tooltip' => $this->_getTooltipValues($column->getName())
            ];
        },
            $this->columns);
    }
    
    
    /**
     * Returns the text phrase for the desired value.
     *
     * @param string $name identifier of the wanted text phrase.
     *
     * @return array Returns the text phrase for the desired value.
     */
    protected function _getTooltipValues($name)
    {
        $tooltipsValues = [
            'filter' => [
                'productsName'   => $this->languageTextManager->get_text('TOOLTIP_FILTER_NAME'),
                'combiName'      => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_NAME'),
                'combiModel'     => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_MODEL'),
                'combiEan'       => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_EAN'),
                'combiQuantity'  => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_QUANTITY'),
                'combiWeight'    => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_WEIGHT'),
                'combiPrice'     => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_PRICE'),
                'combiPriceType' => $this->languageTextManager->get_text('TOOLTIP_FILTER_COMBI_PRICETYPE'),
            ],
            'edit'   => [
                'combiQuantity' => $this->languageTextManager->get_text('TOOLTIP_EDIT_COMBI_QUANTITY'),
                'combiWeight'   => $this->languageTextManager->get_text('TOOLTIP_EDIT_COMBI_WEIGHT'),
                'combiPrice'    => $this->languageTextManager->get_text('TOOLTIP_EDIT_COMBI_PRICE'),
            ]
        ];
        
        return [
            'filter' => array_key_exists($name, $tooltipsValues['filter']) ? $tooltipsValues['filter'][$name] : '',
            'edit'   => array_key_exists($name, $tooltipsValues['edit']) ? $tooltipsValues['edit'][$name] : ''
        ];
    }
    
    
    /**
     * Returns a list of all shipment names and shipment identifiers.
     *
     * @return array Returns a list of all shipment names and shipment identifiers.
     */
    protected function _getShipmentConfiguration()
    {
        $result = $this->db->select(['shipping_status_id', 'shipping_status_name'])
            ->where('language_id',
                    $_SESSION['languages_id'])
            ->get('shipping_status')
            ->result_array();
        
        $shipmentConfigured = array_map(function ($value) {
            return [
                'id'    => $value['shipping_status_id'],
                'value' => $value['shipping_status_name']
            ];
        },
            $result);
        
        return array_merge([['id' => '0', 'value' => 'Keine Angaben']], $shipmentConfigured);
    }
    
    
    /**
     * Returns a list of all price types names and price type identifiers.
     *
     * @return array Returns a list of all price types names and price type identifiers.
     */
    protected function _getPriceTypeConfiguration()
    {
        return [
            [
                'id'    => 'fix',
                'value' => $this->languageTextManager->get_text('COMBI_FIX_PRICE')
            ],
            [
                'id'    => 'calc',
                'value' => $this->languageTextManager->get_text('COMBI_CALC_PRICE')
            ]
        ];
    }
}