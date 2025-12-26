<?php
/* --------------------------------------------------------------
   SpecialOfferMapper.inc.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferMapper
 */
class SpecialOfferMapper implements SpecialOfferMapperInterface
{
    /**
     * @var \SpecialOfferReaderInterface
     */
    protected $reader;
    
    /**
     * @var \SpecialOfferWriterInterface
     */
    protected $writer;
    
    
    /**
     * SpecialOfferMapper constructor.
     *
     * @param \SpecialOfferDataAdapterInterface $dataAdapter
     */
    public function __construct(SpecialOfferDataAdapterInterface $dataAdapter)
    {
        $this->reader = $dataAdapter->reader();
        $this->writer = $dataAdapter->writer();
    }
    
    
    /**
     * Returns a special offer by the given id.
     *
     * @param \SpecialOfferIdInterface $specialOfferId Id of expected special offer.
     *
     * @return \SpecialOfferInterface|null Special offer entity by given id.
     * @throws \Exception
     */
    public function findById(SpecialOfferIdInterface $specialOfferId)
    {
        $data = $this->reader->fetchById(new IdType($specialOfferId->specialOfferId()));
        
        return $data ? $this->_mapSpecialOffer($data) : null;
    }
    
    
    /**
     * Returns all special offers that matches the given search condition.
     *
     * @param \SpecialOfferSearchCondition $searchCondition Condition that must match for found items.
     * @param \Pager|null                  $pager           (Optional) Pager object with pagination information
     * @param array                        $sorters         (Optional) array of Sorter objects with data sorting
     *                                                      information
     *
     * @return \SpecialOfferCollection|null List of special offers by given conditions.
     */
    public function findBy(SpecialOfferSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $data = $this->reader->fetchBy(new StringType($searchCondition->buildSql()), $pager, $sorters);
        
        return $data ? $this->_mapCollection($data) : null;
    }
    
    
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \SpecialOfferCollection|null List of special offers.
     */
    public function findAll(\Pager $pager = null, array $sorters = [])
    {
        $data = $this->reader->fetchAll($pager, $sorters);
        
        return $data ? $this->_mapCollection($data) : null;
    }
    
    
    /**
     * Saves the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be saved.
     *
     * @return SpecialOfferInterface
     * @throws \Exception
     */
    public function save(SpecialOfferInterface $specialOffer)
    {
        $data = [
            'products_id'                 => $specialOffer->productId(),
            'specials_quantity'           => $specialOffer->quantity(),
            'specials_new_products_price' => $specialOffer->price(),
            'expires_date'                => $specialOffer->expires()->format('Y-m-d H:i:s'),
            'status'                      => $specialOffer->status() ? '1' : '0'
        ];
        if ($specialOffer->begins() !== null) {
            $data['begins_date'] = $specialOffer->begins()->format('Y-m-d H:i:s');
        }
        $specialOfferId = $this->writer->insert($data);
        
        $newData = [
            'specials_id'            => $specialOfferId,
            'specials_date_added'    => date('Y-m-d H:i:s'),
            'specials_last_modified' => date('Y-m-d H:i:s')
        ];
        
        return $this->_mapSpecialOffer(array_merge($newData, $data));
    }
    
    
    /**
     * Updates the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be updated.
     *
     * @return \SpecialOfferInterface
     * @throws \Exception
     */
    public function update(SpecialOfferInterface $specialOffer)
    {
        $data = [
            'products_id'                 => $specialOffer->productId(),
            'specials_quantity'           => $specialOffer->quantity(),
            'specials_new_products_price' => $specialOffer->price(),
            'expires_date'                => $specialOffer->expires()->format('Y-m-d H:i:s'),
            'status'                      => $specialOffer->status() ? '1' : '0'
        ];
        if ($specialOffer->begins() !== null) {
            $data['begins_date'] = $specialOffer->begins()->format('Y-m-d H:i:s');
        }
        $newData = [
            'specials_id'            => $specialOffer->id(),
            'specials_last_modified' => date('Y-m-d H:i:s')
        ];
        
        $this->writer->update($data, $specialOffer->id());
        
        return $this->_mapSpecialOffer(array_merge($newData, $data));
    }
    
    
    /**
     * Deletes the given special offer from the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be removed.
     *
     * @return void
     */
    public function delete(SpecialOfferInterface $specialOffer)
    {
        $this->writer->delete($specialOffer->id());
    }
    
    
    /**
     * Maps the given data to a special offer collection.
     *
     * @param array $data Data to be mapped.
     *
     * @return \SpecialOfferCollection Collection with mapped data.
     */
    protected function _mapCollection(array $data)
    {
        return SpecialOfferCollection::collect(array_map([$this, '_mapSpecialOffer'], $data));
    }
    
    
    /**
     * Maps the given data to a special offer entity.
     *
     * @param array $data Data to be mapped.
     *
     * @return \SpecialOffer Entity with mapped data.
     * @throws \Exception
     */
    protected function _mapSpecialOffer(array $data)
    {
        $id = SpecialOfferId::create($data['specials_id']);
        
        $information = SpecialOfferInformation::create($data['specials_quantity'],
                                                       $data['specials_new_products_price'],
                                                       (string)$data['status'] === '1',
                                                       $data['products_id']);
        
        $expires = new DateTime($data['expires_date']);
        $begins  = array_key_exists('begins_date', $data) ? new DateTime($data['begins_date']) : null;
        
        $added    = array_key_exists('specials_date_added', $data) ? new DateTime($data['specials_date_added']) : null;
        $modified = array_key_exists('specials_last_modified',
                                     $data) ? new DateTime($data['specials_last_modified']) : null;
        
        $dates = SpecialOfferDates::create($expires, $added, $modified, $begins);
        
        return SpecialOffer::create($id, $information, $dates);
    }
}
