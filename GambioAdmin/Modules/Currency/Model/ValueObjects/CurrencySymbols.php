<?php
/*--------------------------------------------------------------
   CurrencySymbols.php 2022-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Model\ValueObjects;

/**
 * Class CurrencySymbols
 *
 * @package Gambio\Admin\Modules\Currency\Model\ValueObjects
 */
class CurrencySymbols
{
    /**
     * @var string
     */
    private $left;
    
    /**
     * @var string
     */
    private $right;
    
    
    /**
     * CurrencySymbols constructor.
     *
     * @param string $left
     * @param string $right
     */
    private function __construct(string $left, string $right)
    {
        $this->left  = $left;
        $this->right = $right;
    }
    
    
    /**
     * @param string $left
     * @param string $right
     *
     * @return CurrencySymbols
     */
    public static function create(string $left, string $right): CurrencySymbols
    {
        return new self($left, $right);
    }
    
    
    /**
     * @return string
     */
    public function left(): string
    {
        return $this->left;
    }
    
    
    /**
     * @return string
     */
    public function right(): string
    {
        return $this->right;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'left'  => $this->left(),
            'right' => $this->right(),
        ];
    }
}