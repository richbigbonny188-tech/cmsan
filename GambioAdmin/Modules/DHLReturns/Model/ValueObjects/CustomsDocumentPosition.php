<?php
/* --------------------------------------------------------------
   CustomsDocumentPosition.php 2021-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Model\ValueObjects;

class CustomsDocumentPosition
{
    /**
     * @var string
     */
    private $positionDescription;
    /**
     * @var int
     */
    private $count;
    /**
     * @var int
     */
    private $weightInGrams;
    /**
     * @var float
     */
    private $values;
    /**
     * @var string
     */
    private $originCountry;
    /**
     * @var string
     */
    private $articleReference;
    /**
     * @var string
     */
    private $tarifNumber;
    
    
    /**
     * CustomsDocumentPosition constructor.
     */
    public function __construct(string $positionDescription, int $count, int $weightInGrams, float $values)
    {
        
        $this->positionDescription = $positionDescription;
        $this->count               = $count;
        $this->weightInGrams       = $weightInGrams;
        $this->values              = $values;
        $this->originCountry       = '';
        $this->articleReference    = '';
        $this->tarifNumber         = '';
    }
    
    
    /**
     * @return string
     */
    public function getPositionDescription(): string
    {
        return $this->positionDescription;
    }
    
    
    /**
     * @param string $positionDescription
     */
    public function setPositionDescription(string $positionDescription): void
    {
        $this->positionDescription = $positionDescription;
    }
    
    
    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
    
    
    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }
    
    
    /**
     * @return int
     */
    public function getWeightInGrams(): int
    {
        return $this->weightInGrams;
    }
    
    
    /**
     * @param int $weightInGrams
     */
    public function setWeightInGrams(int $weightInGrams): void
    {
        $this->weightInGrams = $weightInGrams;
    }
    
    
    /**
     * @return float
     */
    public function getValues(): float
    {
        return $this->values;
    }
    
    
    /**
     * @param float $values
     */
    public function setValues(float $values): void
    {
        $this->values = $values;
    }
    
    
    /**
     * @return string
     */
    public function getOriginCountry(): string
    {
        return $this->originCountry;
    }
    
    
    /**
     * @param string $originCountry
     */
    public function setOriginCountry(string $originCountry): void
    {
        $this->originCountry = $originCountry;
    }
    
    
    /**
     * @return string
     */
    public function getArticleReference(): string
    {
        return $this->articleReference;
    }
    
    
    /**
     * @param string $articleReference
     */
    public function setArticleReference(string $articleReference): void
    {
        $this->articleReference = $articleReference;
    }
    
    
    /**
     * @return string
     */
    public function getTarifNumber(): string
    {
        return $this->tarifNumber;
    }
    
    
    /**
     * @param string $tarifNumber
     */
    public function setTarifNumber(string $tarifNumber): void
    {
        $this->tarifNumber = $tarifNumber;
    }
}