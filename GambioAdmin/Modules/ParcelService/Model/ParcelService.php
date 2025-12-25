<?php
/* --------------------------------------------------------------
   ParcelService.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceDescriptions;
use Gambio\Admin\Modules\ParcelService\Model\Events\ParcelServiceDescriptionsUpdated;
use Gambio\Admin\Modules\ParcelService\Model\Events\ParcelServiceMarkedAsDefault;
use Gambio\Admin\Modules\ParcelService\Model\Events\ParcelServiceNameUpdated;
use Gambio\Admin\Modules\ParcelService\Model\Events\ParcelServiceShipmentTypeUpdated;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class ParcelService
 *
 * @package Gambio\Admin\Modules\ParcelService\Model
 */
class ParcelService extends AbstractEventRaisingEntity
{
    /**
     * @var ParcelServiceId
     */
    private $id;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var bool
     */
    private $isDefault;
    
    /**
     * @var ParcelServiceDescriptions
     */
    private $descriptions;
    
    /**
     * @var string
     */
    private $shipmentType;
    
    
    /**
     * ParcelService constructor.
     *
     * @param ParcelServiceId           $id
     * @param string                    $name
     * @param bool                      $isDefault
     * @param ParcelServiceDescriptions $descriptions
     * @param string                    $shipmentType
     */
    private function __construct(
        ParcelServiceId           $id,
        string                    $name,
        bool                      $isDefault,
        ParcelServiceDescriptions $descriptions,
        string                    $shipmentType
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->isDefault    = $isDefault;
        $this->descriptions = $descriptions;
        $this->shipmentType = $shipmentType;
    }
    
    
    /**
     * @param ParcelServiceId           $id
     * @param string                    $name
     * @param bool                      $isDefault
     * @param ParcelServiceDescriptions $descriptions
     * @param string                    $shipmentType
     *
     * @return ParcelService
     */
    public static function create(
        ParcelServiceId           $id,
        string                    $name,
        bool                      $isDefault,
        ParcelServiceDescriptions $descriptions,
        string                    $shipmentType
    ): ParcelService {
        return new self($id, $name, $isDefault, $descriptions, $shipmentType);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function url(string $languageCode): string
    {
        return $this->descriptions->url($languageCode);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function comment(string $languageCode): string
    {
        return $this->descriptions->comment($languageCode);
    }
    
    
    /**
     * @return string
     */
    public function shipmentType(): string
    {
        return $this->shipmentType;
    }
    
    
    /**
     * @param string $newName
     */
    public function changeName(string $newName): void
    {
        $this->name = $newName;
        $this->raiseEvent(ParcelServiceNameUpdated::create($this->id, $newName));
    }
    
    
    /**
     *
     */
    public function setAsDefault(): void
    {
        $this->isDefault = true;
        $this->raiseEvent(ParcelServiceMarkedAsDefault::create($this->id));
    }
    
    
    /**
     * @param ParcelServiceDescriptions $newDescriptions
     */
    public function changeDescriptions(ParcelServiceDescriptions $newDescriptions): void
    {
        $this->descriptions = $newDescriptions;
        $this->raiseEvent(ParcelServiceDescriptionsUpdated::create($this->id, $newDescriptions));
    }
    
    
    /**
     * @param string $newShipmentType
     */
    public function changeShipmentType(string $newShipmentType): void
    {
        $this->shipmentType = $newShipmentType;
        $this->raiseEvent(ParcelServiceShipmentTypeUpdated::create($this->id, $newShipmentType));
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->id->value(),
            'name'         => $this->name(),
            'isDefault'    => $this->isDefault(),
            'descriptions' => $this->descriptions->toArray(),
            'shipmentType' => $this->shipmentType(),
        ];
    }
    
}