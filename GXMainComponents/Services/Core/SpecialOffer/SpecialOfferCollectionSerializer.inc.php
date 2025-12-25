<?php
/* --------------------------------------------------------------
   SpecialOfferCollectionSerializer.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferCollectionSerializer
 */
class SpecialOfferCollectionSerializer
{
    protected $specialOfferSerializer;
    
    
    public function __construct(SpecialOfferSerializer $specialOfferSerializer)
    {
        $this->specialOfferSerializer = $specialOfferSerializer;
    }
    
    
    public function serialize(SpecialOfferCollection $collection)
    {
        $specialOffers = [];
        
        foreach ($collection as $specialOffer) {
            $specialOffers[] = $this->specialOfferSerializer->serialize($specialOffer);
        }
        
        return $specialOffers;
    }
}