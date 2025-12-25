<?php
/*------------------------------------------------------------------------------
 SetupWizardStepService.php 2020-08-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Interfaces;

use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;

interface SetupWizardStepService
{
    /**
     * @return SetupWizardStep
     */
    public function getStep() : SetupWizardStep;
    
    public function canHandle(Key $key);
    
    public function markComplete();
    
    public function markIncomplete();
    
}