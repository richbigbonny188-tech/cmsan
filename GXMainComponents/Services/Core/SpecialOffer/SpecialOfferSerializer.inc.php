<?php
/* --------------------------------------------------------------
   SpecialOfferSerializer.inc.php 2020-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferSerializer
 */
class SpecialOfferSerializer
{
    /**
     * Serializes the given special offer into an array.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be serialized.
     *
     * @return array Serialized special offer.
     */
    public function serialize(SpecialOfferInterface $specialOffer)
    {
        $data = [];
        
        if ($specialOffer->id()) {
            $data['id'] = $specialOffer->id();
        }
        
        $data['price']    = $specialOffer->price();
        $data['quantity'] = $specialOffer->quantity();
        $data['status']   = $specialOffer->status();
        if ($specialOffer->begins() !== null) {
            $data['beginsAt'] = $specialOffer->begins()->format('Y-m-d H:i:s');
        }
        $data['expiresAt'] = $specialOffer->expires()->format('Y-m-d H:i:s');
        $data['productId'] = $specialOffer->productId();
        
        if ($specialOffer->added()) {
            $data['added'] = $specialOffer->added()->format('Y-m-d H:i:s');
        }
        
        if ($specialOffer->modified()) {
            $data['modified'] = $specialOffer->modified()->format('Y-m-d H:i:s');
        }
        
        return $data;
    }
    
    
    /**
     * Encodes the given special offer into a json string.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be serialized.
     *
     * @return string Serialized special offer.
     */
    public function encode(SpecialOfferInterface $specialOffer)
    {
        return json_encode($this->serialize($specialOffer));
    }
    
    
    /**
     * Deserialize the given json string to a special offer entity.
     *
     * @param mixed $specialOffer Special offer.
     *
     * @return \SpecialOffer Special offer entity.
     * @throws InvalidArgumentException If the argument is not an array nor a valid JSON string or "null" JSON string
     * @throws \Exception
     */
    public function deserialize($specialOffer)
    {
        if (!is_string($specialOffer) && !is_array($specialOffer)) {
            throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($specialOffer));
        }
        
        $data = is_string($specialOffer) ? json_decode($specialOffer, true) : $specialOffer;
        
        if ($data === null || json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: '
                                               . $specialOffer);
        }
        
        $id          = SpecialOfferId::create(array_key_exists('id', $data) ? $data['id'] : null);
        $information = SpecialOfferInformation::create($data['quantity'],
                                                       $data['price'],
                                                       $data['status'],
                                                       $data['productId']);
        
        $expires = new DateTime($data['expiresAt']);
        $begins  = !empty($data['beginsAt']) ? new DateTime($data['beginsAt']) : null;
        
        $added    = array_key_exists('added', $data) ? new DateTime($data['added']) : null;
        $modified = array_key_exists('modified', $data) ? new DateTime($data['modified']) : null;
        
        $dates = SpecialOfferDates::create($expires, $added, $modified, $begins);
        
        return SpecialOffer::create($id, $information, $dates);
    }
}
