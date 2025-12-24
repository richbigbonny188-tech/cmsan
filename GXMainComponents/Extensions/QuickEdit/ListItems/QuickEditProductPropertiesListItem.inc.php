<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesListItem.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductPropertiesListItem
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditProductPropertiesListItem
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $model;
    
    /**
     * @var string
     */
    protected $ean;
    
    /**
     * @var float
     */
    protected $quantity;
    
    /**
     * @var int
     */
    protected $shippingTimeId;
    
    /**
     * @var string
     */
    protected $shippingStatusName;
    
    /**
     * @var array
     */
    protected $shipmentConfiguration = [];
    
    /**
     * @var float
     */
    protected $weight;
    
    /**
     * @var float
     */
    protected $tax;
    
    /**
     * @var float
     */
    protected $price;
    
    /**
     * @var string
     */
    protected $priceType;
    
    /**
     * @var array
     */
    protected $priceTypeConfiguration = [];
    
    /**
     * @var string
     */
    protected $productsName;
    
    
    /**
     * QuickEditProductPropertiesListItem constructor.
     *
     * @param array $value Contains product properties data.
     */
    public function __construct($value)
    {
        $this->db                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'admin_quick_edit',
                                                         $_SESSION['languages_id']);
        
        if (PRICE_IS_BRUTTO === 'true') {
            $this->setTax(new DecimalType((double)$value['tax_rate']));
        }
        
        $this->setId(new IdType((int)$value['combi_id']));
        $this->setName(new StringType((string)$value['combi_name']));
        $this->setModel(new StringType((string)$value['combi_model']));
        $this->setEan(new StringType((string)$value['combi_ean']));
        $this->setQuantity(new DecimalType((double)$value['combi_quantity']));
        $this->setShippingTimeId(new IntType((int)$value['combi_shipping_status_id']));
        $this->setShipmentConfiguration();
        $this->setWeight(new DecimalType((double)$value['combi_weight']));
        $this->setPriceType(new StringType((string)$value['combi_price_type']));
        $this->setPriceTypeConfiguration();
        $this->setProductsName(new StringType((string)$value['products_name']));
        
        if ($value['combi_price_type'] === 'fix') {
            $this->setPrice($value['combi_price'] * (1 + $this->getTax() / 100));
        } else {
            $this->setPrice($value['values_price']);
        }
    }
    
    
    /**
     * Returns the Id of the product.
     *
     * @return int Returns the Id of the product.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the id of the product.
     *
     * @param IdType $id Product id.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the name of the product.
     *
     * @return int Returns the name of the product.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Sets the name of the product.
     *
     * @param StringType $name Product name.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setName(StringType $name)
    {
        $this->name = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the model of the product.
     *
     * @return int Returns the model of the product.
     */
    public function getModel()
    {
        return $this->model;
    }
    
    
    /**
     * Sets the model of the product.
     *
     * @param StringType $model Product model.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setModel(StringType $model)
    {
        $this->model = $model->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the ean of the product.
     *
     * @return int Returns the ean of the product.
     */
    public function getEan()
    {
        return $this->ean;
    }
    
    
    /**
     * Sets the éan of the product.
     *
     * @param StringType $ean Product $ean.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setEan(StringType $ean)
    {
        $this->ean = $ean->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the quantity of the product.
     *
     * @return int Returns the quantity of the product.
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    
    /**
     * Sets the quantity of the product.
     *
     * @param DecimalType $quantity Product quantity.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setQuantity(DecimalType $quantity)
    {
        $this->quantity = $quantity->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the shipping time id of the product.
     *
     * @return int Returns the shipping time id of the product.
     */
    public function getShippingTimeId()
    {
        return $this->shippingTimeId;
    }
    
    
    /**
     * Sets the shipping time id of the product.
     *
     * @param IntType $shippingTimeId Product shipping time id.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setShippingTimeId(IntType $shippingTimeId)
    {
        $this->shippingTimeId = $shippingTimeId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the shipping configuration of the product.
     *
     * @return int Returns the shipping configuration of the product.
     */
    public function getShipmentConfiguration()
    {
        return $this->shipmentConfiguration;
    }
    
    
    /**
     * Sets the shipment configuration of the product.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setShipmentConfiguration()
    {
        $result = $this->db->select(['shipping_status_id', 'shipping_status_name'])
            ->where('language_id',
                    $_SESSION['languages_id'])
            ->get('shipping_status')
            ->result_array();
        
        $propertiesShipmentConfigured = array_map(function ($value) {
            return [
                'id'    => $value['shipping_status_id'],
                'value' => $value['shipping_status_name']
            ];
        },
            $result);
        
        $this->shipmentConfiguration = array_merge([['id' => '0', 'value' => 'Keine Angabe']],
                                                   $propertiesShipmentConfigured);
        
        return $this;
    }
    
    
    /**
     * Returns the weight of the product.
     *
     * @return int Returns the weight of the product.
     */
    public function getWeight()
    {
        return sprintf('%01.3f', $this->weight);
    }
    
    
    /**
     * Sets the weight of the product.
     *
     * @param DecimalType $weight Product weight.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setWeight(DecimalType $weight)
    {
        $this->weight = $weight->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the tax of the product.
     *
     * @return int Returns the tax of the product.
     */
    public function getTax()
    {
        return $this->tax;
    }
    
    
    /**
     * Sets the tax of the product.
     *
     * @param DecimalType $tax Product tax.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setTax(DecimalType $tax)
    {
        $this->tax = $tax->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the price of the product.
     *
     * @return int Returns the price of the product.
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    
    /**
     * Sets the price of the product.
     *
     * @param DecimalType $price Product price.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setPrice($price)
    {
        $this->price = [
            'values_name'  => $this->getName(),
            'values_price' => sprintf('%01.2f', round($price, 2))
        ];
        
        return $this;
    }
    
    
    /**
     * Returns the price type of the product.
     *
     * @return int Returns the price type of the product.
     */
    public function getPriceType()
    {
        return $this->priceType;
    }
    
    
    /**
     * Sets the price type of the product.
     *
     * @param StringType $priceType Product price type.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setPriceType(StringType $priceType)
    {
        $this->priceType = $priceType->asString();
        
        return $this;
    }
    
    
    /**
     * @return array
     */
    public function getPriceTypeConfiguration()
    {
        return $this->priceTypeConfiguration;
    }
    
    
    /**
     * Sets the price type configuration of the product.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setPriceTypeConfiguration()
    {
        $this->priceTypeConfiguration = [
            [
                'id'    => 'fix',
                'value' => $this->languageTextManager->get_text('COMBI_FIX_PRICE')
            ],
            [
                'id'    => 'calc',
                'value' => $this->languageTextManager->get_text('COMBI_CALC_PRICE')
            ]
        ];
        
        return $this;
    }
    
    
    /**
     * Returns the name of the product.
     *
     * @return int Returns the name of the product.
     */
    public function getProductsName()
    {
        return $this->productsName;
    }
    
    
    /**
     * Sets the name of the product.
     *
     * @param StringType $productsName Product name.
     *
     * @return QuickEditProductPropertiesListItem QuickEdit product collection.
     */
    public function setProductsName(StringType $productsName)
    {
        $this->productsName = $productsName->asString();
        
        return $this;
    }
}