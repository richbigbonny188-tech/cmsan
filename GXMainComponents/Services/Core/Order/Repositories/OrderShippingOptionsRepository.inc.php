<?php
/* --------------------------------------------------------------
   OrderShippingOptionsRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class OrderShippingOptionsRepository
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    public function __construct()
    {
        /** @var CI_DB_query_builder db */
        $this->db = StaticGXCoreLoader::getDatabaseQueryBuilder();
    }
    
    
    public function store(int $orderId, OrderShippingOption $option): void
    {
        $optionData = [
            'orders_id' => $orderId,
            'key'       => $option->getKey(),
            'value'     => $option->getValue(),
        ];
        $this->db->replace('orders_shipping_options', $optionData);
    }
    
    
    public function findByOrderId(IdType $orderId): OrderShippingOptionCollection
    {
        $options     = [];
        $optionsRows = $this->db->get_where('orders_shipping_options', 'orders_id = ' . $orderId->asInt())
            ->result_array();
        foreach ($optionsRows as $optionsRow) {
            $dhlOptions = ['dhl_preferred_neighbour', 'dhl_preferred_location', 'dhl_preferred_day'];
            if ($optionsRow['value'] === 'true' && in_array($optionsRow['key'], $dhlOptions, true)) {
                // do not use legacy data
                continue;
            }
            $value = $optionsRow['value'] === 'true' ? true : $optionsRow['value'];
            $options[] = MainFactory::create('OrderShippingOption', $optionsRow['key'], $value);
        }
        
        return MainFactory::create('OrderShippingOptionCollection', $options);
    }
}