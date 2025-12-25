<?php
/* --------------------------------------------------------------
   WithdrawalId.php 2020-08-24
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
 * Class WithdrawalId
 *
 * @package Gambio\Admin\Modules\Withdrawal\Model\ValueObjects
 */
class WithdrawalId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * WithdrawalId constructor.
     *
     * @param int $value
     */
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int $value
     *
     * @return WithdrawalId
     */
    public static function create(int $value): WithdrawalId
    {
        Assert::greaterThan($value, 0, 'The withdrawal ID must be a positive integer or null. Got: %s');
        
        return new self($value);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
}