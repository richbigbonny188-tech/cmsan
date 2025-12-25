<?php

/* --------------------------------------------------------------
   QuickEditSpecialPricesOverviewColumns.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditSpecialPriceOverviewColumns
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditSpecialPricesOverviewColumns implements QuickEditSpecialPricesOverviewColumnsInterface
{
    /**
     * @var array
     */
    protected $columns = [];
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    
    /**
     * QuickEditSpecialPricesOverviewColumns constructor.
     */
    public function __construct()
    {
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'admin_quick_edit',
                                                         $_SESSION['languages_id']);
        
        // Products Name
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('NAME')))
            ->setName(new StringType('productsName'))
            ->setField(new StringType('products_description.products_name'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Products Model
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('MODEL')))
            ->setName(new StringType('productsModel'))
            ->setField(new StringType('products.products_model'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING));
        
        // Products Price
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('PRICE')))
            ->setName(new StringType('productsPrice'))
            ->setField(new StringType('products.products_price'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Special Price
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('SPECIAL_PRICE')))
            ->setName(new StringType('specialPrice'))
            ->setField(new StringType('specials.specials_new_products_price'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Special Quantity
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('SPECIAL_QUANTITY')))
            ->setName(new StringType('specialPriceQuantity'))
            ->setField(new StringType('specials.specials_quantity'))
            ->setType(new DataTableColumnType(DataTableColumnType::NUMBER));
        
        // Special Expires Date
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('EXPIRES_DATE')))
            ->setName(new StringType('specialPriceExpiresDate'))
            ->setField(new StringType('specials.expires_date'))
            ->setType(new DataTableColumnType(DataTableColumnType::DATE));
        
        // Special Status
        $this->columns[] = MainFactory::create('DataTableColumn')
            ->setTitle(new StringType($this->languageTextManager->get_text('STATUS')))
            ->setName(new StringType('specialPriceStatus'))
            ->setField(new StringType('specials.status'))
            ->setType(new DataTableColumnType(DataTableColumnType::STRING))
            ->setOptions($this->_getStatusConfiguration());
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
                'productsName'         => $this->languageTextManager->get_text('TOOLTIP_FILTER_NAME'),
                'productsModel'        => $this->languageTextManager->get_text('TOOLTIP_FILTER_MODEL'),
                'productsPrice'        => $this->languageTextManager->get_text('TOOLTIP_FILTER_PRICE'),
                'specialPriceQuantity' => $this->languageTextManager->get_text('TOOLTIP_FILTER_QUANTITY'),
                'specialPrice'         => $this->languageTextManager->get_text('TOOLTIP_FILTER_SPECIAL_PRICE')
            ],
            'edit'   => [
                'specialPrice'         => $this->languageTextManager->get_text('TOOLTIP_EDIT_SPECIAL_PRICE'),
                'specialPriceQuantity' => $this->languageTextManager->get_text('TOOLTIP_EDIT_QUANTITY')
            ]
        ];
        
        return [
            'filter' => array_key_exists($name, $tooltipsValues['filter']) ? $tooltipsValues['filter'][$name] : '',
            'edit'   => array_key_exists($name, $tooltipsValues['edit']) ? $tooltipsValues['edit'][$name] : ''
        ];
    }
    
    
    /**
     * Returns a list of all status names and status identifiers.
     *
     * @return array Returns a list of all status names and status identifiers.
     */
    protected function _getStatusConfiguration()
    {
        return [
            [
                'id'    => 1,
                'value' => $this->languageTextManager->get_text('ACTIVE')
            ],
            [
                'id'    => 0,
                'value' => $this->languageTextManager->get_text('INACTIVE')
            ]
        ];
    }
}