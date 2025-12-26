<?php
/* --------------------------------------------------------------
   ReturnOrderConfirmation.php 2021-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);


namespace Gambio\Admin\Modules\DHLReturns\Model\ValueObjects;

class ReturnOrderConfirmation
{
    /**
     * @var string
     */
    private $shipmentNumber;
    /**
     * @var string
     */
    private $labelData;
    /**
     * @var string
     */
    private $qrLabelData;
    /**
     * @var string
     */
    private $routingCode;
    
    
    /**
     * ReturnOrderConfirmation constructor.
     *
     * @param string $shipmentNumber
     */
    public function __construct(string $shipmentNumber)
    {
    
        $this->shipmentNumber = $shipmentNumber;
        $this->labelData = '';
        $this->qrLabelData = '';
        $this->routingCode = '';
    }
    
    
    /**
     * @return string
     */
    public function getShipmentNumber(): string
    {
        return $this->shipmentNumber;
    }
    
    
    /**
     * @param string $shipmentNumber
     */
    public function setShipmentNumber(string $shipmentNumber): void
    {
        $this->shipmentNumber = $shipmentNumber;
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
    public function getQrLabelData(): string
    {
        return $this->qrLabelData;
    }
    
    
    /**
     * @param string $qrLabelData
     */
    public function setQrLabelData(string $qrLabelData): void
    {
        $this->qrLabelData = $qrLabelData;
    }
    
    
    /**
     * @return string
     */
    public function getRoutingCode(): string
    {
        return $this->routingCode;
    }
    
    
    /**
     * @param string $routingCode
     */
    public function setRoutingCode(string $routingCode): void
    {
        $this->routingCode = $routingCode;
    }


}