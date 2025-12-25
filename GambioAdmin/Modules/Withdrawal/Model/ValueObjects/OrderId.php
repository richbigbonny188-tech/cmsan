<?php
/* --------------------------------------------------------------
   OrderId.php 2020-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class OrderId
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\ValueObjects
 */
class OrderId
{
    /**
     * @var int|null
     */
    private $value;
    
    
    /**
     * OrderId constructor.
     *
     * @param int|null $value
     */
    private function __construct(?int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int|null $value
     *
     * @return OrderId
     */
    public static function create(?int $value = null): OrderId
    {
        if ($value !== null) {
            Assert::greaterThan($value, 0, 'The order ID must be a positive integer. Got: %s');
        }
        
        return new self($value);
    }
    
    
    /**
     * @return int|null
     */
    public function value(): ?int
    {
        return $this->value;
    }
}