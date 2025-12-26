<?php
/* --------------------------------------------------------------
   SpecialOffer.inc.php 2018-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SpecialOffer implements SpecialOfferInterface
{
    /**
     * @var SpecialOfferIdInterface
     */
    protected $specialOfferId;
    
    /**
     * @var SpecialOfferInformationInterface
     */
    protected $information;
    
    /**
     * @var SpecialOfferDatesInterface
     */
    protected $dates;
    
    
    /**
     * SpecialOffer constructor.
     *
     * @param \SpecialOfferIdInterface          $specialOfferId Ids of special offer entity.
     * @param \SpecialOfferInformationInterface $information    Special offer information.
     * @param \SpecialOfferDatesInterface       $dates          Special offer dates.
     */
    public function __construct(
        SpecialOfferIdInterface $specialOfferId,
        SpecialOfferInformationInterface $information,
        SpecialOfferDatesInterface $dates
    ) {
        $this->specialOfferId = $specialOfferId;
        $this->information    = $information;
        $this->dates          = $dates;
    }
    
    
    /**
     * @param \SpecialOfferIdInterface          $specialOfferId Ids of special offer entity.
     * @param \SpecialOfferInformationInterface $information    Special offer information.
     * @param \SpecialOfferDatesInterface       $dates          Special offer dates.
     *
     * @return SpecialOffer New instance.
     */
    public static function create(
        SpecialOfferIdInterface $specialOfferId,
        SpecialOfferInformationInterface $information,
        SpecialOfferDatesInterface $dates
    ) {
        return MainFactory::create(static::class, $specialOfferId, $information, $dates);
    }
    
    
    /**
     * Returns the special offer id.
     *
     * @return int|null Id of special offer.
     */
    public function id()
    {
        return $this->specialOfferId->specialOfferId();
    }
    
    
    /**
     * Returns the product id of the special.
     *
     * @return int Id of special offer's product.
     */
    public function productId()
    {
        return $this->information->productId();
    }
    
    
    /**
     * Returns the special offer's price.
     *
     * @return double Price of special offer.
     */
    public function price()
    {
        return $this->information->price();
    }
    
    
    /**
     * Returns the quantity of the special offer.
     *
     * @return double Special offer's quantity.
     */
    public function quantity()
    {
        return $this->information->quantity();
    }
    
    
    /**
     * Returns the special offer status.
     *
     * @return bool True if special offer is active.
     */
    public function status()
    {
        return $this->information->status();
    }
    
    
    /**
     * Returns the creation date of the special offer.
     *
     * @return \DateTime Creation date of special offer.
     */
    public function added()
    {
        return $this->dates->added();
    }
    
    
    /**
     * Returns the last modification date of the special offer.
     *
     * @return \DateTime Last modification date of special offer.
     */
    public function modified()
    {
        return $this->dates->modified();
    }
    
    
    /**
     * Returns the expiration date of the special offer.
     *
     * @return \DateTime Expiration date of special offer.
     */
    public function expires()
    {
        return $this->dates->expires();
    }
    
    
    /**
     * Returns the start date of the special offer.
     *
     * @return \DateTime Start date of special offer.
     */
    public function begins()
    {
        return $this->dates->begins();
    }
}
