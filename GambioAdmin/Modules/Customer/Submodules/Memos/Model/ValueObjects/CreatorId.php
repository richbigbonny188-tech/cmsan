<?php
/*--------------------------------------------------------------
   CreatorId.php 2022-09-14
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
 * Class CreatorId
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Model\ValueObjects
 */
class CreatorId
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
     * @return CreatorId
     */
    public static function create(int $value): CreatorId
    {
        Assert::greaterThan($value, 0, 'Given creator ID must be greater than 0. Got: %s');
        
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