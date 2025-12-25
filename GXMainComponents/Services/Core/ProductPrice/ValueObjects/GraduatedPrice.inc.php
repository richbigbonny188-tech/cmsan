<?php
/* --------------------------------------------------------------
   GraduatedPrice.inc.php 2018-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GraduatedPrice
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage ValueObjects
 */
class GraduatedPrice implements GraduatedPriceInterface
{
    /**
     * @var double
     */
    protected $threshold;
    
    /**
     * @var double
     */
    protected $graduatedPrice;
    
    /**
     * @var int
     */
    protected $taxClassId;
    
    
    /**
     * GraduatedPrice constructor.
     *
     * @param \DecimalType $threshold      Threshold.
     * @param \DecimalType $graduatedPrice Graduated price.
     * @param \IdType      $taxClassId     Tax class id,
     */
    public function __construct(DecimalType $graduatedPrice, IdType $taxClassId = null, DecimalType $threshold = null)
    {
        $this->graduatedPrice = $graduatedPrice->asDecimal();
        $this->taxClassId     = $taxClassId !== null ? $taxClassId->asInt() : 0;
        $this->threshold      = $threshold !== null ? $threshold->asDecimal() : 0.0;
    }
    
    
    /**
     * Named Constructor of graduated price.
     *
     * @param \Double $graduatedPrice Graduated price.
     * @param \int    $taxClassId
     *
     * @return \GraduatedPrice New instance.
     */
    public static function create($graduatedPrice, $taxClassId)
    {
        $graduatedPrice = new DecimalType($graduatedPrice);
        $taxClassId     = new IdType($taxClassId);
        
        return MainFactory::create(static::class, $graduatedPrice, $taxClassId, null);
    }
    
    
    /**
     * Creates a new instance with threshold value.
     *
     * @param \Double $graduatedPrice Graduated price.
     * @param \Double $threshold      Threshold.
     *
     * @return \GraduatedPrice New instance.
     */
    public static function createWithThreshold($graduatedPrice, $threshold)
    {
        $graduatedPrice = new DecimalType($graduatedPrice);
        $threshold      = new DecimalType($threshold);
        
        return MainFactory::create(static::class, $graduatedPrice, null, $threshold);
    }
    
    
    /**
     * Returns the threshold of the graduated price.
     *
     * @return double Graduated price´s quantity.
     */
    public function threshold()
    {
        return $this->threshold;
    }
    
    
    /**
     * Returns the graduated price.
     *
     * @return double Graduated price.
     */
    public function graduatedPrice()
    {
        return $this->graduatedPrice;
    }
    
    
    /**
     * Returns the tax class id.
     *
     * @return int Tax class id.
     */
    public function taxClassId()
    {
        return $this->taxClassId;
    }
}