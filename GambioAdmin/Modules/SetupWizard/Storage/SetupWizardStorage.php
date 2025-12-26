<?php
/* --------------------------------------------------------------
  HideSetupWizardStorage.inc.php 2019-05-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Storage;

use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class HideSetupWizardStorage
 * @codeCoverageIgnore
 */
class SetupWizardStorage
{
    
    /**
     * Configuration storage namespace
     */
    protected const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/setupwizard';
    protected const HIDE_SETUP_WIZARD        = 'HIDE_SETUP_WIZARD';

    
    /**
     * @var ConfigurationStorageRepository
     */
    protected $repository;
    
    
    /**
     * AbstractStepIsDoneStorage constructor.
     *
     * @param ConfigurationStorageRepositoryBuilder $builder
     */
    public function __construct(ConfigurationStorageRepositoryBuilder $builder)
    {
        $this->repository = $builder->build(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    /**
     * @return Status is the step done
     */
    public function getValue(): Status
    {
        $value = $this->repository->get(static::HIDE_SETUP_WIZARD);
        $value = $value === '' ? 'false' : $value;
        
        return new Status($value === 'true');
    }
    
    
    /**
     * marks a step as done in the database
     */
    public function setHidden(): void
    {
        $this->repository->set(static::HIDE_SETUP_WIZARD, 'true');
    }
    
    
    public function setVisible(): void
    {
        $this->repository->set(static::HIDE_SETUP_WIZARD, 'false');
    }
}