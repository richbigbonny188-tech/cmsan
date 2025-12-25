<?php
/*--------------------------------------------------------------
   LegalTextDTO.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\DTO;

/**
 * Class LegalTextDTO
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText
 */
class LegalTextDTO
{
    /**
     * @var string
     */
    protected $termsAndConditionsHeading;
    
    /**
     * @var string
     */
    protected $termsAndConditionsText;
    
    /**
     * @var string
     */
    protected $withdrawalHeading;
    
    /**
     * @var string
     */
    protected $withdrawalText;
    
    /**
     * @var int
     */
    protected $languageId;
    
    /**
     * @var string
     */
    protected $withdrawalType;
    
    /**
     * @var string
     */
    protected $termsAndConditionsType;
    
    
    /**
     * LegalTextDTO constructor.
     *
     * @param string $termsAndConditionsHeading
     * @param string $termsAndConditionsText
     * @param string $termsAndConditionsType
     * @param string $withdrawalHeading
     * @param string $withdrawalText
     * @param string $withdrawalType
     * @param int    $languageId
     */
    public function __construct(
        string $termsAndConditionsHeading,
        string $termsAndConditionsText,
        string $termsAndConditionsType,
        string $withdrawalHeading,
        string $withdrawalText,
        string $withdrawalType,
        int $languageId
    ) {
        $this->termsAndConditionsHeading = $termsAndConditionsHeading;
        $this->termsAndConditionsText    = $termsAndConditionsText;
        $this->withdrawalHeading         = $withdrawalHeading;
        $this->withdrawalText            = $withdrawalText;
        $this->languageId                = $languageId;
        $this->withdrawalType = $withdrawalType;
        $this->termsAndConditionsType = $termsAndConditionsType;
    }
    
    
    /**
     * @return string
     */
    public function termsAndConditionsHeading(): string
    {
        return $this->termsAndConditionsHeading;
    }
    
    
    /**
     * @return string
     */
    public function termsAndConditionsText(): string
    {
        return $this->termsAndConditionsText;
    }
    
    
    /**
     * @return string
     */
    public function withdrawalHeading(): string
    {
        return $this->withdrawalHeading;
    }
    
    
    /**
     * @return string
     */
    public function withdrawalText(): string
    {
        return $this->withdrawalText;
    }
    
    
    /**
     * @return int
     */
    public function languageId(): int
    {
        return $this->languageId;
    }
    
    
    /**
     * @return string
     */
    public function withdrawalType(): string
    {
        return $this->withdrawalType;
    }
    
    
    /**
     * @return string
     */
    public function termsAndConditionsType(): string
    {
        return $this->termsAndConditionsType;
    }
    
}