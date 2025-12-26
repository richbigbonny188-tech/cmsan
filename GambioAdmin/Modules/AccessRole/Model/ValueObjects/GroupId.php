<?php
/* --------------------------------------------------------------
   GroupId.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class GroupId
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\ValueObjects
 */
class GroupId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * GroupId constructor.
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
     * @return GroupId
     */
    public static function create(int $value): GroupId
    {
        Assert::greaterThan($value, 0, 'Group ID need to be greater than 0. Got: %s');
        
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