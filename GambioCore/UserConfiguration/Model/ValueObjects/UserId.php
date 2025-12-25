<?php
/* --------------------------------------------------------------
   UserId.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class UserId
 *
 * @package Gambio\Core\UserConfiguration\Model\ValueObjects
 */
class UserId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * UserId constructor.
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
     * @return UserId
     */
    public static function create(int $value): UserId
    {
        Assert::greaterThan($value, 0, 'Invalid user ID provided. Got: %s');
        
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