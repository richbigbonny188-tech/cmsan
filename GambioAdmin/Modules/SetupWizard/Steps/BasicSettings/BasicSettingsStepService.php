<?php
/*------------------------------------------------------------------------------
 SetupWizardCatalogStepService.php 2020-08-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings;

use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\Services\AbstractSetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;

/**
 * Class BasicSettingsStepService
 * @package Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings
 */
class BasicSettingsStepService extends AbstractSetupWizardStepService
{
    
    /**
     * @return SetupWizardStep
     */
    public function getStep() : SetupWizardStep
    {
        $status = $this->storage->getValue();
        $index = new Index(3);
        $title  = new Title(
            $this->textManager->getPhraseText(
                str_replace('SETUP_WIZARD_', '', $this->key->value()). '_HEADLINE',
                static::TEXT_SECTION
            )
        );
        $description  = new Text(
            $this->textManager->getPhraseText(
                str_replace('SETUP_WIZARD_', '', $this->key->value()). '_DESCRIPTION',
                static::TEXT_SECTION
            )
        );
        
        return new BasicSettingsStep($status, $index, $title, $description, $this->key);
    }
    
}