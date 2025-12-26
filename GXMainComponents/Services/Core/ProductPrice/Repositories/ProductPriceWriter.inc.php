<?php
/* --------------------------------------------------------------
   ProductPriceWriter.inc.php 2018-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceWriter
 */
class ProductPriceWriter implements ProductPriceWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * ProductPriceWriter constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Updates the given product price data to a storage.
     *
     * @param array   $productPrice Product price data to be updated.
     * @param \IdType $productId    Id of product price to be updated.
     *
     * @return void
     */
    public function update(array $productPrice, IdType $productId)
    {
        $this->delete($productId);
        
        $this->db->update('products',
                          ['products_price' => $productPrice['price']],
                          ['products_id' => $productId->asInt()]);
        
        foreach ($productPrice['customerGroups'] as $productPriceData) {
            $data               = [
                [
                    'products_id'    => $productId->asInt(),
                    'quantity'       => '1.0000',
                    'personal_offer' => $productPriceData['groupPrice']
                ]
            ];
            $personalOfferTable = 'personal_offers_by_customers_status_' . $productPriceData['customerGroupId'];
            
            foreach ($productPriceData['graduatedPrices'] as $graduatedPrice) {
                $data[] = [
                    'products_id'    => $productId->asInt(),
                    'quantity'       => $graduatedPrice['threshold'],
                    'personal_offer' => $graduatedPrice['graduatedPrice']
                ];
            }
            
            $this->db->insert_batch($personalOfferTable, $data);
        }
    }
    
    
    /**
     * Deletes a product price from the storage by the given id.
     *
     * @param \IdType $productId Id of product price to be removed.
     *
     * @return void
     */
    public function delete(IdType $productId)
    {
        $customerStatusIds = $this->db->select('customers_status_id as id')
            ->from('customers_status')
            ->where('customers_status_id > 0')
            ->group_by('id')
            ->get()
            ->result_array();
        foreach ($customerStatusIds as $customerStatusId) {
            $personalOfferTable = 'personal_offers_by_customers_status_' . $customerStatusId['id'];
            $this->db->delete($personalOfferTable, ['products_id' => $productId->asInt()]);
        }
    }
}