<?php
/* --------------------------------------------------------------
   OrderId.php 2020-09-01
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
 * Class OrderId
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model\ValueObjects
 */
class OrderId
{
    /**
     * @var int
     */
    private $id;
    
    
    /**
     * OrderId constructor.
     *
     * @param int $id
     */
    private function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param int $id
     *
     * @return OrderId
     */
    public static function create(int $id): OrderId
    {
        Assert::greaterThan($id, 0, 'Invalid ID value provided. Got: %s');
        
        return new self($id);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
    }
}