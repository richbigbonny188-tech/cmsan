<?php
/* --------------------------------------------------------------
  AbstractStepIsDoneStorage.inc.php 2019-05-27
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
 * Class AbstractStepIsDoneStorage
 * @codeCoverageIgnore
 */
abstract class AbstractStepIsDoneStorage implements StepIsDoneStorageInterface
{
    /**
     * Configuration storage namespace
     */
    protected const CONFIG_STORAGE_NAMESPACE = 'modules/gambio/setupwizard';
    
    /**
     * @var string
     */
    protected $key;
    
    /**
     * @var ConfigurationStorageRepository
     */
    protected $repository;
    
    
    /**
     * AbstractStepIsDoneStorage constructor.
     *
     * @param Key                                $key
     * @param ConfigurationStorageRepositoryBuilder $builder
     */
    public function __construct(Key $key, ConfigurationStorageRepositoryBuilder $builder)
    {
        $this->key        = $key;
        $this->repository = $builder->build(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    /**
     * @return Status is the step done
     */
    public function getValue() : Status
    {
        $value = $this->repository->get($this->key->value());
        $value = $value === '' ? 'false' : $value;
        
        return new Status($value === 'true');
    }
    
    
    /**
     * marks a step as done in the database
     */
    public function setStepComplete() : void
    {
        $this->repository->set($this->key->value(), 'true');
    }
    
    public function setStepIncomplete() : void
    {
        $this->repository->set($this->key->value(), 'false');
    }
}