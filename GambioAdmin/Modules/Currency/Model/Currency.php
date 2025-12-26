<?php
/*--------------------------------------------------------------
   Currency.php 2022-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Model;

use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencyId;
use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencySymbols;
use Webmozart\Assert\Assert;

/**
 * Class Currency
 *
 * @package Gambio\Admin\Modules\Currency\Model
 */
class Currency
{
    /**
     * @var CurrencyId
     */
    private $id;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $code;
    
    /**
     * @var CurrencySymbols
     */
    private $symbols;
    
    /**
     * @var float
     */
    private $value;
    
    /**
     * @var string
     */
    private $decimalSeparator;
    
    /**
     * @var string
     */
    private $thousandsSeparator;
    
    /**
     * @var int
     */
    private $decimalPlaces;
    
    /**
     * @var bool 
     */
    private $isDefault;
    
    
    /**
     * Currency constructor.
     *
     * @param CurrencyId      $id
     * @param string          $name
     * @param string          $code
     * @param CurrencySymbols $symbols
     * @param float           $value
     * @param string          $decimalSeparator
     * @param string          $thousandsSeparator
     * @param int             $decimalPlaces
     * @param bool            $isDefault
     */
    private function __construct(
        CurrencyId      $id,
        string          $name,
        string          $code,
        CurrencySymbols $symbols,
        float           $value,
        string          $decimalSeparator,
        string          $thousandsSeparator,
        int             $decimalPlaces,
        bool            $isDefault
    ) {
        $this->id                 = $id;
        $this->name               = $name;
        $this->code               = $code;
        $this->symbols            = $symbols;
        $this->value              = $value;
        $this->decimalSeparator   = $decimalSeparator;
        $this->thousandsSeparator = $thousandsSeparator;
        $this->decimalPlaces      = $decimalPlaces;
        $this->isDefault          = $isDefault;
    }
    
    
    /**
     * @param CurrencyId      $id
     * @param string          $name
     * @param string          $code
     * @param CurrencySymbols $symbols
     * @param float           $value
     * @param string          $decimalSeparator
     * @param string          $thousandsSeparator
     * @param int             $decimalPlaces
     * @param bool            $isDefault
     *
     * @return Currency
     */
    public static function create(
        CurrencyId      $id,
        string          $name,
        string          $code,
        CurrencySymbols $symbols,
        float           $value,
        string          $decimalSeparator,
        string          $thousandsSeparator,
        int             $decimalPlaces,
        bool            $isDefault = false
    ): Currency {
        
        Assert::greaterThan($value, 0, 'Given value must be greater than 0. Got: %s');
        Assert::greaterThan($decimalPlaces, 0, 'Given decimal places must be greater than 0. Got: %s');
        
        return new self($id,
                        $name,
                        $code,
                        $symbols,
                        $value,
                        $decimalSeparator,
                        $thousandsSeparator,
                        $decimalPlaces,
                        $isDefault);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
    
    
    /**
     * @return string
     */
    public function symbolLeft(): string
    {
        return $this->symbols->left();
    }
    
    
    /**
     * @return string
     */
    public function symbolRight(): string
    {
        return $this->symbols->right();
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
    
    
    /**
     * @return string
     */
    public function decimalSeparator(): string
    {
        return $this->decimalSeparator;
    }
    
    
    /**
     * @return string
     */
    public function thousandsSeparator(): string
    {
        return $this->thousandsSeparator;
    }
    
    
    /**
     * @return int
     */
    public function decimalPlaces(): int
    {
        return $this->decimalPlaces;
    }
    
    
    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'                 => $this->id(),
            'name'               => $this->name(),
            'code'               => $this->code(),
            'symbols'            => $this->symbols->toArray(),
            'value'              => $this->value(),
            'decimalSeparator'   => $this->decimalSeparator(),
            'thousandsSeparator' => $this->thousandsSeparator(),
            'decimalPlaces'      => $this->decimalPlaces(),
            'isDefault'          => $this->isDefault(),
        ];
    }
}