<?php
/*------------------------------------------------------------------------------
 BasicSetupWizardService.php 2020-09-14
 BasicSetupWizardService.php 2020-09-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard;

use Gambio\Admin\Modules\SetupWizard\Collections\SetupWizardStepCollection;
use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\Storage\SetupWizardStorage;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;

/**
 * Class SetupWizardService
 * @package Gambio\Admin\Modules\SetupWizard
 */
class SetupWizardService implements SetupWizardServiceInterface
{
    /**
     * @var SetupWizardStepService[]
     */
    private $steps;
    /**
     * @var SetupWizardStorage
     */
    private $storage;
    
    
    /**
     * SetupWizardService constructor.
     *
     * @param SetupWizardStorage     $storage
     * @param SetupWizardStepService ...$steps
     */
    public function __construct(SetupWizardStorage $storage, SetupWizardStepService ...$steps)
    {
        $this->steps   = $steps;
        $this->storage = $storage;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSteps(): SetupWizardStepCollection
    {
        $resultArray = [];
        foreach ($this->steps as $stepService) {
            $resultArray[] = $stepService->getStep();
        }
        
        return new SetupWizardStepCollection($resultArray, !$this->storage->getValue()->value());
    }
    
    
    /**
     * @inheritDoc
     */
    public function setStepComplete(string $keyName): void
    {
        $key = new Key($keyName);
        foreach ($this->steps as $stepService) {
            if ($stepService->canHandle($key)) {
                $stepService->markComplete();
                break;
            }
            
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function hide(): void
    {
        $this->storage->setHidden();
    }
    
    
    /**
     * @inheritDoc
     */
    public function show(): void
    {
        $this->storage->setVisible();
    }
    
    
    /**
     * @inheritDoc
     */
    public function setStepIncomplete(string $keyName): void
    {
        $key = new Key($keyName);
        foreach ($this->steps as $stepService) {
            if ($stepService->canHandle($key)) {
                $stepService->markIncomplete();
                break;
            }
        }
    }
}