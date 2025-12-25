<?php
/* --------------------------------------------------------------
   ProductPriceReader.inc.php 2024-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceReader
 */
class ProductPriceReader implements ProductPriceReaderInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * ProductPriceReader constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Fetches product price data by the given id.
     *
     * @param \IdType $productId product id.
     *
     * @return array|null Raw data of product prices.
     */
    public function fetchById(IdType $productId)
    {
        $price = $this->db->select('products_price as price')
                     ->from('products')
                     ->where('products_id',
                             $productId->asInt())
                     ->get()
                     ->row_array();
        
        if (!$price) {
            return null;
        }
        
        $price = $price['price'];
        
        $taxClassId = $this->db->select('products_tax_class_id as taxClassId')
                          ->from('products')
                          ->where('products_id',
                                  $productId->asInt())
                          ->get()
                          ->row_array();
        
        $taxClassId = (int)$taxClassId['taxClassId'];
        
        $customerStatusIds   = $this->db->select('customers_status_id as id')
            ->from('customers_status')
            ->where('customers_status_id > 0')
            ->group_by('id')
            ->get()
            ->result_array();
        $personalOfferTables = [];
        foreach ($customerStatusIds as $customerStatusId) {
            $personalOfferTables[] = [
                'table' => 'personal_offers_by_customers_status_' . $customerStatusId['id'],
                'id'    => $customerStatusId['id']
            ];
        }
        
        $groups = [];
        foreach ($personalOfferTables as $personalOfferTable) {
            $personalOfferData = $this->db->select()
                ->from($personalOfferTable['table'])
                ->where('products_id',
                        $productId->asInt())
                ->get()
                ->result_array();
            
            $group = [];
            
            foreach ($personalOfferData as $personalOffer) {
                $group['customerGroupId'] = (int)$personalOfferTable['id'];
                if ($personalOffer['quantity'] === '1.0000' && $personalOffer['personal_offer'] !== '0.0000') {
                    $group['groupPrice'] = $personalOffer['personal_offer'];
                } elseif ((double)$personalOffer['quantity'] > 1) {
                    $group['graduatedPrices'][] = [
                        'threshold'      => $personalOffer['quantity'],
                        'graduatedPrice' => $personalOffer['personal_offer'],
                        'taxClassId'     => (int)$taxClassId
                    ];
                }
            }
            
            if (count($group) > 1) {
                $groups[] = $group;
            }
        }
        $data = [
            'productId'      => $productId->asInt(),
            'price'          => (double)$price,
            'taxClassId'     => (int)$taxClassId,
            'customerGroups' => $groups
        
        ];
        
        return $data;
    }
    
    
    /**
     * Fetches product price data by the given conditions.
     *
     * @param \IdType      $productId       Product id.
     * @param \IdType      $customerGroupId Customer group id.
     * @param \DecimalType $quantity        Quantity.
     *
     * @return array|null Raw data of product prices.
     */
    public function fetchByQuantity(IdType $productId, IdType $customerGroupId, DecimalType $quantity)
    {
        $personalOfferTable = 'personal_offers_by_customers_status_' . $customerGroupId->asInt();
        
        if (!$this->db->table_exists($personalOfferTable)) {
            return null;
        }
        
        $productsData = $this->db->select('po.quantity as threshold, po.personal_offer as graduatedPrice, products_tax_class_id as taxClassId')
            ->from('products as p')
            ->join($personalOfferTable . ' as po', 'po.products_id = p.products_id')
            ->where('p.products_id',
                    $productId->asInt())
            ->get()
            ->result_array();
        if (count($productsData) === 0) {
            return null;
        }
        
        $graduatedPriceData = [];
        
        $graduatedPrices = array_map(function ($data) {
            return (float)$data['threshold'];
        },
            $productsData);
        
        $nearestThreshold = $this->_findNearestLower($graduatedPrices, $quantity->asDecimal());
        
        foreach ($productsData as $productData) {
            if ($nearestThreshold === (float)$productData['threshold']) {
                $graduatedPriceData['graduatedPrice'] = (double)$productData['graduatedPrice'];
                $graduatedPriceData['taxClassId']     = $productData['taxClassId'];
            }
        }
        
        return $graduatedPriceData;
    }
    
    
    /**
     * Finds the nearest number.
     * If the searched number is between two others, the lower value is returned.
     *
     * @param array $numbers Numbers to be searched
     * @param float $search  Number to be searched.
     *
     * @return float|null Nearest (or lower nearest) number.
     */
    protected function _findNearestLower(array $numbers, $search)
    {
        sort($numbers);
        
        $closest = null;
        $numbers = array_map(function ($number) {
            return (float)$number;
        },
            $numbers);
        
        foreach ($numbers as $number) {
            if (null === $closest || abs($search - $closest) > abs($number - $search)) {
                $closest = $number;
            }
        }
        
        return $closest;
    }
}