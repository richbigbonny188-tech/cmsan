<?php
/*--------------------------------------------------------------
   ReviewId.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ReviewId
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects
 */
class ReviewId
{
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
     * @return ReviewId
     */
    public static function create(int $value): ReviewId
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