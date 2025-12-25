<?php
/* --------------------------------------------------------------
   ParcelServiceDeleted.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Events;

use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;

/**
 * Class ParcelServiceDeleted
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Events
 * @codeCoverageIgnore
 */
class ParcelServiceDeleted
{
    /**
     * @var ParcelServiceId
     */
    private $id;
    
    
    /**
     * ParcelServiceDeleted constructor.
     *
     * @param ParcelServiceId $id
     */
    private function __construct(ParcelServiceId $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param ParcelServiceId $id
     *
     * @return ParcelServiceDeleted
     */
    public static function create(ParcelServiceId $id): ParcelServiceDeleted
    {
        return new self($id);
    }
    
    
    /**
     * @return ParcelServiceId
     */
    public function parcelServiceId(): ParcelServiceId
    {
        return $this->id;
    }
}