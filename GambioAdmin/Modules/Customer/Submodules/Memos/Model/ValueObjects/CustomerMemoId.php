<?php
/*--------------------------------------------------------------
   CustomerMemoId.php 2021-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerMemoId
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects
 */
class CustomerMemoId
{
    /**
     * @var int
     */
    private int $value;
    
    
    /**
     * OptionId constructor.
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
     * @return CustomerMemoId
     */
    public static function create(int $value): CustomerMemoId
    {
        Assert::greaterThan($value, 0, 'Given customer ID must be greater than 0. Got: %s');
        
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