<?php
/*--------------------------------------------------------------
   BusinessInformation.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\ValueObjects;

/**
 * Class BusinessInformation
 *
 * @package Gambio\Admin\Modules\Customer\Model\ValueObjects
 */
class BusinessInformation
{
    private string $companyName;
    private string $vatId;
    private bool   $isValidVatId;
    private bool   $isTradesperson;
    
    
    /**
     * @param string $companyName
     * @param string $vatId
     * @param bool   $isTradesperson
     * @param bool   $isValidVatId
     */
    public function __construct(string $companyName, string $vatId, bool $isTradesperson, bool $isValidVatId)
    {
        $this->companyName    = $companyName;
        $this->vatId          = $vatId;
        $this->isValidVatId   = $isValidVatId;
        $this->isTradesperson = $isTradesperson;
    }
    
    
    /**
     * @param string $companyName
     * @param string $vatId
     * @param bool   $isTradesperson
     * @param bool   $isValidVatId
     *
     * @return BusinessInformation
     */
    public static function create(
        string $companyName = '',
        string $vatId = '',
        bool   $isTradesperson = false,
        bool   $isValidVatId = false
    ): BusinessInformation {
        return new self($companyName, $vatId, $isTradesperson, $isValidVatId);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'companyName'    => $this->companyName(),
            'vatId'          => $this->vatId(),
            'isTradesperson' => $this->isTradesperson(),
            'isValidVatId'   => $this->isValidVatId(),
        ];
    }
    
    
    /**
     * @return string
     */
    public function companyName(): string
    {
        return $this->companyName;
    }
    
    
    /**
     * @return string
     */
    public function vatId(): string
    {
        return $this->vatId;
    }
    
    
    /**
     * @return bool
     */
    public function isValidVatId(): bool
    {
        return $this->isValidVatId;
    }
    
    
    /**
     * @return bool
     */
    public function isTradesperson(): bool
    {
        return $this->isTradesperson;
    }
}