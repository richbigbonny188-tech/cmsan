<?php
/*------------------------------------------------------------------------------
 ExternalDataStorageImpl.php 2020-09-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Dashboard\App\Data;

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

class DashboardConfigurationStorageImpl implements DashboardConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    protected const CONFIG_STORAGE_NAMESPACE = 'configuration/dashboard';
    protected const CONFIG_STORAGE_KEY       = 'EMBED_SOCIAL_MEDIA';
    
    /**
     * @var ConfigurationStorageRepository
     */
    protected $repository;
    
    
    public function __construct(ConfigurationStorageRepositoryBuilder $builder)
    {
        $this->repository = $builder->build(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    public function enableExternalSocialMediaEmbeds(): void
    {
        $this->repository->set(static::CONFIG_STORAGE_KEY, 'true');
    }
    
    
    public function disableExternalSocialMediaEmbeds(): void
    {
        $this->repository->set(static::CONFIG_STORAGE_KEY, 'false');
    }
    
    
    public function isExternalSocialMediaEmbedsAllowed(): bool
    {
        return $this->repository->get(static::CONFIG_STORAGE_KEY) === 'true';
    }
}