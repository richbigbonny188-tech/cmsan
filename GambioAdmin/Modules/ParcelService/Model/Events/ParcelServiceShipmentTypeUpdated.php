<?php
/* --------------------------------------------------------------
   ParcelServiceShipmentTypeUpdated.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\Events;

use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;

/**
 * Class ParcelServiceShipmentTypeUpdated
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\Events
 * @codeCoverageIgnore
 */
class ParcelServiceShipmentTypeUpdated
{
    /**
     * @var ParcelServiceId
     */
    private $id;
    
    /**
     * @var string
     */
    private $newShipmentType;
    
    
    /**
     * ParcelServiceShipmentTypeUpdated constructor.
     *
     * @param ParcelServiceId $id
     * @param string          $newShipmentType
     */
    private function __construct(ParcelServiceId $id, string $newShipmentType)
    {
        $this->id              = $id;
        $this->newShipmentType = $newShipmentType;
    }
    
    
    /**
     * @param ParcelServiceId $id
     * @param string          $newShipmentType
     *
     * @return ParcelServiceShipmentTypeUpdated
     */
    public static function create(ParcelServiceId $id, string $newShipmentType): ParcelServiceShipmentTypeUpdated
    {
        return new self($id, $newShipmentType);
    }
    
    
    /**
     * @return ParcelServiceId
     */
    public function parcelServiceId(): ParcelServiceId
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function newShipmentType(): string
    {
        return $this->newShipmentType;
    }
}