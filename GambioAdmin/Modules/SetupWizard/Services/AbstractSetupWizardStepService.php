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

namespace Gambio\Admin\Modules\SetupWizard\Services;

use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\Storage\StepIsDoneStorageInterface;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Core\TextManager\Services\TextManager;

abstract class AbstractSetupWizardStepService implements SetupWizardStepService
{
    protected const TEXT_SECTION = 'setup_wizard';
    /**
     * @var string
     */
    protected $key;
    /**
     * @var StepIsDoneStorageInterface
     */
    protected $storage;
    /**
     * @var TextManager
     */
    protected $textManager;
    
    
    /**
     * AbstractSetupWizardStepService constructor.
     *
     * @param Key                        $key
     * @param StepIsDoneStorageInterface $storage
     * @param TextManager                $textManager
     */
    public function __construct(Key $key, StepIsDoneStorageInterface $storage, TextManager $textManager)
    {
        
        $this->key         = $key;
        $this->storage     = $storage;
        $this->textManager = $textManager;
    }
    
    
    public function canHandle(Key $key)
    {
        return $key->value() === $this->key->value();
    }
    
    
    public function markComplete()
    {
        $this->storage->setStepComplete();
    }
    
    
    public function markIncomplete()
    {
        $this->storage->setStepIncomplete();
    }
    
}