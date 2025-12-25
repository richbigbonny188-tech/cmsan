<?php
/* --------------------------------------------------------------
   GroupId.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class AccessGroupId
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\ValueObjects
 */
class AccessGroupId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * AccessGroupId constructor.
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
     * @return AccessGroupId
     */
    public static function create(int $value): AccessGroupId
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