<?php
/* --------------------------------------------------------------
   TrackingCodeCreated.php 2021-05-14
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
 * Class TrackingCodeCreated
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model\Events
 * @codeCoverageIgnore
 */
class TrackingCodeCreated
{
    /**
     * @var TrackingCodeId
     */
    private $id;
    
    
    /**
     * TrackingCodeCreated constructor.
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
     * @return TrackingCodeCreated
     */
    public static function create(TrackingCodeId $id): TrackingCodeCreated
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