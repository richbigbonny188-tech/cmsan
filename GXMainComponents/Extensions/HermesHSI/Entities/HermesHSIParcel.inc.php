<?php
/* --------------------------------------------------------------
   HermesHSIParcel.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

class HermesHSIParcel implements JsonSerializable
{
    /** @var string */
    protected $parcelClass;
    
    /** @var int */
    protected $parcelHeight;
    
    /** @var int */
    protected $parcelWidth;
    
    /** @var int */
    protected $parcelDepth;
    
    /** @var int */
    protected $parcelWeight;
    
    
    /**
     * HermesHSIParcel constructor.
     * @throws HermesHSIInvalidDataException
     */
    public function __construct()
    {
        $this->setParcelClass('NONE');
        $this->setParcelWidth(0);
        $this->setParcelHeight(0);
        $this->setParcelDepth(0);
        $this->setParcelWeight(0);
    }
    
    
    /**
     * @return string
     */
    public function getParcelClass(): string
    {
        return $this->parcelClass;
    }
    
    
    /**
     * @param string $parcelClass
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setParcelClass(string $parcelClass): void
    {
        $parcelClass = mb_strtoupper($parcelClass);
        if (!in_array($parcelClass, ['NONE', 'XS', 'S', 'M', 'L', 'XL'])) {
            throw new HermesHSIInvalidDataException('parcelClass must be one of NONE, XS, S, M, L, XL, got ' . $parcelClass);
        }
        if ($parcelClass === 'NONE') {
            $parcelClass = '';
        }
        $this->parcelClass = $parcelClass;
    }
    
    
    /**
     * @return int
     */
    public function getParcelHeight(): int
    {
        return $this->parcelHeight;
    }
    
    
    /**
     * Sets parcel height in mm
     *
     * @param int $parcelHeight
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setParcelHeight(int $parcelHeight): void
    {
        if ($parcelHeight < 0) {
            throw new HermesHSIInvalidDataException('parcelHeight must not be negative');
        }
        $this->parcelHeight = $parcelHeight;
    }
    
    
    /**
     * @return int
     */
    public function getParcelWidth(): int
    {
        return $this->parcelWidth;
    }
    
    
    /**
     * Sets parcel width in mm
     *
     * @param int $parcelWidth
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setParcelWidth(int $parcelWidth): void
    {
        if ($parcelWidth < 0) {
            throw new HermesHSIInvalidDataException('parcelWidth must not be negative');
        }
        $this->parcelWidth = $parcelWidth;
    }
    
    
    /**
     * @return int
     */
    public function getParcelDepth(): int
    {
        return $this->parcelDepth;
    }
    
    
    /**
     * Sets parcel depth in mm
     *
     * @param int $parcelDepth
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setParcelDepth(int $parcelDepth): void
    {
        if ($parcelDepth < 0 ) {
            throw new HermesHSIInvalidDataException('parcelDepth must not be negative');
        }
        $this->parcelDepth = $parcelDepth;
    }
    
    
    /**
     * @return int
     */
    public function getParcelWeight(): int
    {
        return $this->parcelWeight;
    }
    
    
    /**
     * Sets parcel weight in grams.
     *
     * @param int $parcelWeight
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setParcelWeight(int $parcelWeight): void
    {
        if ($parcelWeight < 0) {
            throw new HermesHSIInvalidDataException('parcelWeight must not be negative');
        }
        $this->parcelWeight = $parcelWeight;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
                                'parcelClass'  => $this->parcelClass,
                                'parcelHeight' => $this->parcelHeight,
                                'parcelWidth'  => $this->parcelWidth,
                                'parcelDepth'  => $this->parcelDepth,
                                'parcelWeight' => $this->parcelWeight,
                            ]);
    }
}
