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

namespace Gambio\Admin\Modules\SetupWizard\Steps\Catalog;

use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\Services\AbstractSetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\Storage\StepIsDoneStorageInterface;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Core\TextManager\Services\TextManager;

class CatalogStepService extends AbstractSetupWizardStepService
{
    /**
     * CatalogStepService constructor.
     *
     * @param CatalogStepKey             $key
     * @param StepIsDoneStorageInterface $storage
     * @param TextManager                $textManager
     */
    public function __construct(CatalogStepKey $key, StepIsDoneStorageInterface $storage, TextManager $textManager)
    {
        parent::__construct($key, $storage, $textManager);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getStep() : SetupWizardStep
    {
        $status = $this->storage->getValue();
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
        
        return new CatalogStep($status, new Index(2), $title, $description, $this->key);
    }
    
}