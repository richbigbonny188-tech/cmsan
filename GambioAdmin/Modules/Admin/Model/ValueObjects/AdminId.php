<?php
/* --------------------------------------------------------------
   AdminId.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class AdminId
 *
 * @package Gambio\Admin\Modules\Admin\Model\ValueObjects
 */
class AdminId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * AdminId constructor.
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
     * @return AdminId
     */
    public static function create(int $value): AdminId
    {
        Assert::greaterThan($value, 0, 'Admin ID need to be greater than 0. Got: %s');
        
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