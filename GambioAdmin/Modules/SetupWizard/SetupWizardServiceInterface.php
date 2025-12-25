<?php
/*------------------------------------------------------------------------------
 SetupWizardServiceInterface.php 2020-09-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard;

use Gambio\Admin\Modules\SetupWizard\Collections\SetupWizardStepCollection;

interface SetupWizardServiceInterface
{
    /**
     * @return SetupWizardStepCollection
     */
    public function getSteps(): SetupWizardStepCollection;
    
    /**
     * @param string $keyName
     */
    public function setStepComplete(string $keyName): void;
    
    /**
     * @param string $keyName
     */
    public function setStepIncomplete(string $keyName): void;
    
    
    /**
     * Hide the Setup Wizard Panel
     */
    public function hide(): void;
    
    
    /**
     * Show the Setup Wizard Panel
     */
    public function show(): void;
    
}