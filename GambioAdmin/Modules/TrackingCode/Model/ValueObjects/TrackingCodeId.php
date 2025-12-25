<?php
/* --------------------------------------------------------------
   TrackingCodeId.php 2020-09-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class TrackingCodeId
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model\ValueObjects
 */
class TrackingCodeId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * TrackingCodeId constructor.
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
     * @return TrackingCodeId
     */
    public static function create(int $value): TrackingCodeId
    {
        Assert::greaterThan($value, 0, 'The tracking code ID must be a positive integer or null. Got: %s');
        
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