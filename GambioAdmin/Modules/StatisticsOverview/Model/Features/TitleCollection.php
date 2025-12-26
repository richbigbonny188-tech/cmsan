<?php
/*--------------------------------------------------------------
   TitleCollection.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Features;

use ArrayIterator;
use IteratorAggregate;
use Webmozart\Assert\Assert;

/**
 * Class representing a collection multilingual titles.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Features
 */
abstract class TitleCollection implements IteratorAggregate
{
    /**
     * German language code.
     */
    private const GERMAN_LANGUAGE_CODE = "de";
    
    /**
     * English language code.
     */
    private const ENGLISH_LANGUAGE_CODE = "en";
    
    /**
     * Values.
     *
     * @var WithLanguageCode[]
     */
    private $values;
    
    
    /**
     * Constructor.
     *
     * @param WithLanguageCode[] $values Values.
     */
    protected function __construct(array $values)
    {
        $hasGermanTitle  = false;
        $hasEnglishTitle = false;
        
        $this->values = [];
        
        foreach ($values as $value) {
            $languageCode = $value->languageCode()->value();
            
            if (!$hasGermanTitle && $languageCode === self::GERMAN_LANGUAGE_CODE) {
                $hasGermanTitle = true;
            }
            
            if (!$hasEnglishTitle && $languageCode === self::ENGLISH_LANGUAGE_CODE) {
                $hasEnglishTitle = true;
            }
            
            $this->values[$languageCode] = $value;
        }
        
        Assert::true($hasGermanTitle);
        Assert::true($hasEnglishTitle);
    }
    
    
    /**
     * Return item by language code.
     *
     * @param string $languageCode Language code.
     *
     * @return WithLanguageCode Item with provided language code.
     */
    public function getByLanguageCode(string $languageCode): WithLanguageCode
    {
        Assert::stringNotEmpty($languageCode);
        
        return $this->values[$languageCode] ?? $this->values[self::GERMAN_LANGUAGE_CODE];
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator(array_values($this->values));
    }
}
