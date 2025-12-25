<?php
/*--------------------------------------------------------------
   UploadLogoStepService.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo;

use Gambio\Admin\Modules\SetupWizard\Commands\Done\UploadLogoStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Entities\UploadLogoStep;
use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader\UploadLogoReader;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects\LogoDirectoryPath;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects\LogoProperties;
use Gambio\Admin\Modules\SetupWizard\Services\AbstractSetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class UploadLogoRepository
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo
 */
class UploadLogoStepService extends AbstractSetupWizardStepService
{
    /**
     * @var UploadLogoReader
     */
    protected $reader;
    
    /**
     * @var UploadLogoStepDoneCommand
     */
    protected $command;
    
    /**
     * @var LogoDirectoryPath
     */
    protected $directoryPath;
    
    /**
     * @var LogoProperties
     */
    protected $logoProperties;
    
    
    /**
     * UploadLogoRepository constructor.
     *
     * @param UploadLogoStepKey           $key
     * @param UploadLogoReader            $reader
     * @param UploadLogoStepIsDoneStorage $storage
     * @param UploadLogoStepDoneCommand   $command
     * @param LogoDirectoryPath           $directoryPath
     * @param LogoProperties              $logoProperties
     * @param TextManager                 $textManager
     */
    public function __construct(
        UploadLogoStepKey $key,
        UploadLogoReader $reader,
        UploadLogoStepIsDoneStorage $storage,
        UploadLogoStepDoneCommand $command,
        LogoDirectoryPath $directoryPath,
        LogoProperties $logoProperties,
        TextManager $textManager
    ) {
        parent::__construct($key, $storage, $textManager);
        
        $this->reader         = $reader;
        $this->command        = $command;
        $this->directoryPath  = $directoryPath;
        $this->logoProperties = $logoProperties;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getStep(): SetupWizardStep
    {
        $status = $this->storage->getValue()->value();
        
        if ($status === false) {
            
            $currentLogo = $this->reader->currentLogo();
            
            if ($currentLogo->filename() === $this->logoProperties->filename()) {
                
                $currentLogoPath = $this->directoryPath->value() . $currentLogo->filename();
                $currentLogoHash = file_exists($currentLogoPath) ? hash_file('sha1', $currentLogoPath) : '';
                
                if ($currentLogoHash !== $this->logoProperties->hashValue()) {
                    
                    $status = true;
                }
                
            } else {
                $status = true;
            }
            
            if ($status === true) {
                
                $this->command->execute();
            }
        }
        
        return $this->createStep(new Status($status));
    }
    
    
    protected function createStep(Status $status): UploadLogoStep
    {
        $title       = $this->createTitle();
        $description = $this->createDescription();
        
        return new UploadLogoStep($status, $title, $description, $this->key);
    }
    
    
    /**
     * @return Title
     */
    protected function createTitle(): Title
    {
        $titleValue = $this->textManager->getPhraseText('STEP_LOGO_HEADLINE', 'setup_wizard');
        return new Title($titleValue);
    }
    
    
    /**
     * @return Text
     */
    protected function createDescription(): Text
    {
        $descriptionValue = $this->textManager->getPhraseText('STEP_LOGO_DESCRIPTION', 'setup_wizard');
        return new Text($descriptionValue);
    }
}