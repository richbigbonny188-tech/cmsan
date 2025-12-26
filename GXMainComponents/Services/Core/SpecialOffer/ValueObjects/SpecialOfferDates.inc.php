<?php
/* --------------------------------------------------------------
   SpecialOfferDates.inc.php 2018-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferDates
 */
class SpecialOfferDates implements SpecialOfferDatesInterface
{
    /**
     * @var \DateTime
     */
    protected $begins;
    
    /**
     * @var \DateTime
     */
    protected $expires;
    
    /**
     * @var \DateTime|null
     */
    protected $added;
    
    /**
     * @var \DateTime|null
     */
    protected $modified;
    
    
    /**
     * SpecialOfferDates constructor.
     *
     * @param \DateTime      $expires  Expiration date.
     * @param \DateTime      $added    Creation date.
     * @param \DateTime      $modified Modification date.
     * @param \DateTime|null $begins
     */
    public function __construct(
        DateTime $expires,
        DateTime $added = null,
        DateTime $modified = null,
        DateTime $begins = null
    ) {
        $this->begins   = $begins;
        $this->expires  = $expires;
        $this->added    = $added;
        $this->modified = $modified;
    }
    
    
    /**
     * Named constructor of special offer dates.
     *
     * @param \DateTime      $expires  Expiration date.
     * @param \DateTime      $added    Creation date.
     * @param \DateTime      $modified Modification date.
     *
     * @param \DateTime|null $begins
     *
     * @return SpecialOfferDates New instance.
     */
    public static function create(
        DateTime $expires,
        DateTime $added = null,
        DateTime $modified = null,
        DateTime $begins = null
    ) {
        return MainFactory::create(static::class, $expires, $added, $modified, $begins);
    }
    
    
    /**
     * Returns the creation date of the special offer.
     *
     * @return \DateTime Creation date of special offer.
     */
    public function added()
    {
        return $this->added;
    }
    
    
    /**
     * Returns the last modification date of the special offer.
     *
     * @return \DateTime|null Last modification date of special offer.
     */
    public function modified()
    {
        return $this->modified;
    }
    
    
    /**
     * Returns the expiration date of the special offer.
     *
     * @return \DateTime|null Expiration date of special offer.
     */
    public function expires()
    {
        return $this->expires;
    }
    
    
    /**
     * Returns the expiration date of the special offer.
     *
     * @return \DateTime|null Expiration date of special offer.
     */
    public function begins()
    {
        return $this->begins;
    }
}
