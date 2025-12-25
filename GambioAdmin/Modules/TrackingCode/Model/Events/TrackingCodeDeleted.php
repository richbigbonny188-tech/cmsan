<?php
/* --------------------------------------------------------------
   TrackingCodeDeleted.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Model\Events;

use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;

/**
 * Class TrackingCodeDeleted
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model\Events
 * @codeCoverageIgnore
 */
class TrackingCodeDeleted
{
    /**
     * @var TrackingCodeId
     */
    private $id;
    
    
    /**
     * TrackingCodeDeleted constructor.
     *
     * @param TrackingCodeId $id
     */
    private function __construct(TrackingCodeId $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param TrackingCodeId $id
     *
     * @return TrackingCodeDeleted
     */
    public static function create(TrackingCodeId $id): TrackingCodeDeleted
    {
        return new self($id);
    }
    
    
    /**
     * @return TrackingCodeId
     */
    public function trackingCodeId(): TrackingCodeId
    {
        return $this->id;
    }
}