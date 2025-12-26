<?php
/* --------------------------------------------------------------
   UserId.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Model;

use Webmozart\Assert\Assert;

class UserId implements \Gambio\Core\Auth\UserId
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
        Assert::greaterThan($value, 0, 'Provided ID must be greater than 0. Got: %s');
        
        return new self($value);
    }
    
    
    /**
     * @return int
     */
    public function userId(): int
    {
        return $this->value;
    }
}