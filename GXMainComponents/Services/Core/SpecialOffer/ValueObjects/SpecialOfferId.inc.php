<?php
/* --------------------------------------------------------------
   SpecialOfferId.inc.php 2018-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferId
 */
class SpecialOfferId implements SpecialOfferIdInterface
{
    /**
     * @var int|null
     */
    protected $specialOfferId;
    
    
    /**
     * SpecialOfferId constructor.
     *
     * @param \IdType|null $specialOfferId Special offer id.
     */
    public function __construct(IdType $specialOfferId = null)
    {
        $this->specialOfferId = $specialOfferId ? $specialOfferId->asInt() : null;
    }
    
    
    /**
     * Named constructor of special offer id.
     *
     * @param int|null $specialOfferId Special offer id.
     *
     * @return \SpecialOfferId New instance.
     */
    public static function create($specialOfferId = null)
    {
        return MainFactory::create(static::class, $specialOfferId ? new IdType($specialOfferId) : null);
    }
    
    
    /**
     * Returns the special offer id.
     *
     * @return int|null Id of special offer.
     */
    public function specialOfferId()
    {
        return $this->specialOfferId;
    }
}