<?php
/*--------------------------------------------------------------
   AdditionalFieldValue.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects;

use MainFactory;

/**
 * Class AdditionalFieldValue
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects
 */
class AdditionalFieldValue
{
    /**
     * @var string
     */
    protected $languageCode;
    
    /**
     * @var string
     */
    protected $value;
    
    
    /**
     * AdditionalFieldValue constructor.
     *
     * @param string $languageCode
     * @param string $value
     */
    public function __construct(string $languageCode, string $value)
    {
        $this->languageCode = $languageCode;
        $this->value        = $value;
    }
    
    
    /**
     * @param string $languageCode
     * @param string $value
     *
     * @return AdditionalFieldValue
     */
    public static function create(string $languageCode, string $value): AdditionalFieldValue
    {
        return MainFactory::create(AdditionalFieldValue::class, $languageCode, $value);
    }
    
    
    /**
     * @param string $value
     *
     * @return AdditionalFieldValue
     */
    public function withValue(string $value): AdditionalFieldValue
    {
        return MainFactory::create(AdditionalFieldValue::class, $this->languageCode, $value);
    }
    
    
    /**
     * @return string
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'languageCode' => $this->languageCode(),
            'value'        => $this->value(),
        ];
    }
}