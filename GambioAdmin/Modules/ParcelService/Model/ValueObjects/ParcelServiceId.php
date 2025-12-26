<?php
/* --------------------------------------------------------------
   ParcelServiceId.php 2020-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ParcelServiceId
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\ValueObjects
 */
class ParcelServiceId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * ParcelServiceId constructor.
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
     * @return ParcelServiceId
     */
    public static function create(int $value): ParcelServiceId
    {
        Assert::greaterThan($value, 0, 'The parcel service ID must be a positive integer or null. Got: %s');
        
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