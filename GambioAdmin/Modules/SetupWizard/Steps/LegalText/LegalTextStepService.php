<?php
/*--------------------------------------------------------------
   LegalTextStepService.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText;

use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\Services\AbstractSetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections\TermsAndConditionTextHashesCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections\WithdrawalTextHashesCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Reader\LegalTextReader;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Core\TextManager\Services\TextManager;

class LegalTextStepService extends AbstractSetupWizardStepService
{
    /**
     * @var WithdrawalTextHashesCollection
     */
    protected $withdrawalTextHashes;
    
    /**
     * @var TermsAndConditionTextHashesCollection
     */
    protected $termsAndConditionTextHashes;
    
    /**
     * @var LegalTextReader
     */
    protected $reader;
    
    
    /**
     * LegalTextStepService constructor.
     *
     * @param Key                                   $key
     * @param LegalTextStepIsDoneStorage            $storage
     * @param TextManager                           $textManager
     * @param WithdrawalTextHashesCollection        $withdrawalTextHashes
     * @param TermsAndConditionTextHashesCollection $termsAndConditionTextHashes
     * @param LegalTextReader                       $reader
     */
    public function __construct(
        Key $key,
        LegalTextStepIsDoneStorage $storage,
        TextManager $textManager,
        WithdrawalTextHashesCollection $withdrawalTextHashes,
        TermsAndConditionTextHashesCollection $termsAndConditionTextHashes,
        LegalTextReader $reader
    ) {
        $this->withdrawalTextHashes        = $withdrawalTextHashes;
        $this->termsAndConditionTextHashes = $termsAndConditionTextHashes;
        $this->reader                      = $reader;
        
        parent::__construct($key, $storage, $textManager);
    }
    
    
    /**
     * @return SetupWizardStep
     */
    public function getStep() : SetupWizardStep
    {
        $status = $this->storage->getValue()->value();
        
        if (($status === false) && $this->currentLegalTextWasChanged()) {
            
            $this->storage->setStepComplete();
            $status = true;
        }
        
        return $this->createStep(new Status($status));
    }
    
    
    /**
     * @param Status $status
     *
     * @return LegalTextStep
     */
    protected function createStep(Status $status): LegalTextStep
    {
        $title       = $this->createTitle();
        $description = $this->createDescription();
        
        return new LegalTextStep($status, $title, $description, $this->key);
    }
    
    /**
     * @return Title
     */
    protected function createTitle(): Title
    {
        $titleValue = $this->textManager->getPhraseText('STEP_LEGAL_TEXT_HEADLINE', 'setup_wizard');
        return new Title($titleValue);
    }
    
    
    /**
     * @return Text
     */
    protected function createDescription(): Text
    {
        $descriptionValue = $this->textManager->getPhraseText('STEP_LEGAL_TEXT_DESCRIPTION', 'setup_wizard');
        return new Text($descriptionValue);
    }
    
    /**
     * @return bool
     */
    protected function currentLegalTextWasChanged(): bool
    {
        $legalTextStatus = ['withdrawals' => false, 'termsAndConditions' => false];
    
        foreach ($this->reader->legalTexts() as $legalText) {
        
            if ($legalText->withdrawalType() === 'file') {
                
                $legalTextStatus['withdrawals'] = true;
            }
        
            if ($legalText->termsAndConditionsType() === 'file') {
                
                $legalTextStatus['termsAndConditions'] = true;
            }
    
            $languageId = $legalText->languageId();
            if ($this->hashValuesExistForLanguageId($languageId)) {
    
                $withdrawalHashes        = $this->withdrawalTextHashes[$languageId];
                $termsAndConditionHashes = $this->termsAndConditionTextHashes[$languageId];
    
                if ($legalText->withdrawalHeading() !== $withdrawalHashes->heading()
                    || $legalText->withdrawalText() !== $withdrawalHashes->text()) {
        
                    $legalTextStatus['withdrawals'] = true;
                }
    
                if ($legalText->termsAndConditionsHeading() !== $termsAndConditionHashes->heading()
                    || $legalText->termsAndConditionsText() !== $termsAndConditionHashes->text()) {
        
                    $legalTextStatus['termsAndConditions'] = true;
                }
            }
        }
        
        return in_array(false, $legalTextStatus, true) === false;
    }
    
    
    /**
     * @param int $languageId
     *
     * @return bool
     */
    protected function hashValuesExistForLanguageId(int $languageId): bool
    {
        return isset($this->withdrawalTextHashes[$languageId]) === true && isset($this->termsAndConditionTextHashes[$languageId]) === true;
    }
}