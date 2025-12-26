<?php
/* --------------------------------------------------------------
   SpecialOfferWriter.inc.php 2023-02-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferWriter
 */
class SpecialOfferWriter implements SpecialOfferWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $table = 'specials';
    
    /**
     * @var string
     */
    protected $productsTable = 'products';
    
    
    /**
     * SpecialOfferWriter constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Inserts the given special offer data to a storage.
     *
     * @param array $specialOfferData Special offer data to be inserted.
     *
     * @return int Insert id.
     *
     * @throws \ProductForSpecialOfferNotFoundException If product could not be found.
     */
    public function insert(array $specialOfferData)
    {
        $product = $this->db->select()
            ->from($this->productsTable)
            ->where('products_id',
                    $specialOfferData['products_id'])
            ->get()
            ->row_array();
        if ($product === null) {
            throw new ProductForSpecialOfferNotFoundException('Could not find product with id "'
                                                              . $specialOfferData['products_id'] . '"');
        }
        
        $this->db->insert($this->table, $specialOfferData);
        
        return $this->db->insert_id();
    }
    
    
    /**
     * Updates the given special offer data to a storage.
     *
     * @param array $specialOfferData Special offer data to be updated.
     * @param int   $specialOfferId   Id of special offer to be updated.
     *
     * @return void
     */
    public function update(array $specialOfferData, $specialOfferId)
    {
        $specialOffer = $this->db->select()
            ->from($this->table)
            ->where('specials_id', $specialOfferId)
            ->get()
            ->row_array();
        if ($specialOffer === null) {
            throw new SpecialOfferNotFoundException("Could not find special offer with id $specialOfferId");
        }
        
        $this->db->update($this->table, $specialOfferData, ['specials_id' => $specialOfferId]);
    }
    
    
    /**
     * Deletes a special offer from the storage by the given id.
     *
     * @param int $specialOfferId Id of special offer to be removed.
     *
     * @return void
     */
    public function delete($specialOfferId)
    {
        $this->db->delete($this->table, ['specials_id' => $specialOfferId]);
    }
}