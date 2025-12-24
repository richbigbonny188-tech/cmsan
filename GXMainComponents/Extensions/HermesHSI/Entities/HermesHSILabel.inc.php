<?php
/* --------------------------------------------------------------
   HermesHSILabel.inc.php 2020-04-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSILabel
{
    /** @var string */
    protected $labelData;
    /** @var string */
    protected $shipmentId;
    
    protected $fromLocalStorage;
    
    
    public function __construct(string $labelData, string $shipmentId = '')
    {
        $this->labelData        = $labelData;
        $this->shipmentId       = $shipmentId;
        $this->fromLocalStorage = false;
    }
    
    
    /**
     * @return string
     */
    public function getLabelData(): string
    {
        return $this->labelData;
    }
    
    
    /**
     * @param string $labelData
     */
    public function setLabelData(string $labelData): void
    {
        $this->labelData = $labelData;
    }
    
    
    /**
     * @return string
     */
    public function getShipmentId(): string
    {
        return $this->shipmentId;
    }
    
    
    /**
     * @param string $shipmentId
     */
    public function setShipmentId(string $shipmentId): void
    {
        $this->shipmentId = $shipmentId;
    }
    
    
    /**
     * @return bool
     */
    public function isFromLocalStorage(): bool
    {
        return $this->fromLocalStorage;
    }
    
    
    /**
     * @param bool $fromLocalStorage
     */
    public function setFromLocalStorage(bool $fromLocalStorage): void
    {
        $this->fromLocalStorage = $fromLocalStorage;
    }
}
