<?php
/* --------------------------------------------------------------
   CustomsDocument.php 2021-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Model\ValueObjects;

use Gambio\Admin\Modules\DHLReturns\Model\Collections\CustomsDocumentsPositionCollection;

class CustomsDocument
{
    /**
     * @var string
     */
    private $currency;
    /**
     * @var CustomsDocumentsPositionCollection
     */
    private $positions;
    /**
     * @var string
     */
    private $originalShipmentNumber;
    /**
     * @var string
     */
    private $originalOperator;
    /**
     * @var string
     */
    private $acommpanyingDocument;
    /**
     * @var string
     */
    private $originalInvoiceNumber;
    /**
     * @var string
     */
    private $originalInvoiceDate;
    /**
     * @var string
     */
    private $comment;
    
    
    /**
     * CustomsDocument constructor.
     */
    public function __construct(string $currency, CustomsDocumentsPositionCollection $positions)
    {
        $this->currency               = $currency;
        $this->positions              = $positions;
        $this->originalShipmentNumber = '';
        $this->originalOperator       = '';
        $this->acommpanyingDocument   = '';
        $this->originalInvoiceNumber  = '';
        $this->originalInvoiceDate    = '';
        $this->comment                = '';
    }
    
    
    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    
    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }
    
    
    /**
     * @return CustomsDocumentsPositionCollection
     */
    public function getPositions(): CustomsDocumentsPositionCollection
    {
        return $this->positions;
    }
    
    
    /**
     * @param CustomsDocumentsPositionCollection $positions
     */
    public function setPositions(CustomsDocumentsPositionCollection $positions): void
    {
        $this->positions = $positions;
    }
    
    
    /**
     * @return string
     */
    public function getOriginalShipmentNumber(): string
    {
        return $this->originalShipmentNumber;
    }
    
    
    /**
     * @param string $originalShipmentNumber
     */
    public function setOriginalShipmentNumber(string $originalShipmentNumber): void
    {
        $this->originalShipmentNumber = $originalShipmentNumber;
    }
    
    
    /**
     * @return string
     */
    public function getOriginalOperator(): string
    {
        return $this->originalOperator;
    }
    
    
    /**
     * @param string $originalOperator
     */
    public function setOriginalOperator(string $originalOperator): void
    {
        $this->originalOperator = $originalOperator;
    }
    
    
    /**
     * @return string
     */
    public function getAcommpanyingDocument(): string
    {
        return $this->acommpanyingDocument;
    }
    
    
    /**
     * @param string $acommpanyingDocument
     */
    public function setAcommpanyingDocument(string $acommpanyingDocument): void
    {
        $this->acommpanyingDocument = $acommpanyingDocument;
    }
    
    
    /**
     * @return string
     */
    public function getOriginalInvoiceNumber(): string
    {
        return $this->originalInvoiceNumber;
    }
    
    
    /**
     * @param string $originalInvoiceNumber
     */
    public function setOriginalInvoiceNumber(string $originalInvoiceNumber): void
    {
        $this->originalInvoiceNumber = $originalInvoiceNumber;
    }
    
    
    /**
     * @return string
     */
    public function getOriginalInvoiceDate(): string
    {
        return $this->originalInvoiceDate;
    }
    
    
    /**
     * @param string $originalInvoiceDate
     */
    public function setOriginalInvoiceDate(string $originalInvoiceDate): void
    {
        $this->originalInvoiceDate = $originalInvoiceDate;
    }
    
    
    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
    
    
    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
    
    
}
