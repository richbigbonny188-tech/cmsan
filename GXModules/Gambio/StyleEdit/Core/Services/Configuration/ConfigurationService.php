<?php
/* --------------------------------------------------------------
  ConfigurationService.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration;

use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Exceptions\ConfigurationJsonNotFoundException;
use Gambio\StyleEdit\Core\Services\Configuration\Interfaces\ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationRepositoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;

/**
 * Class ConfigurationService
 */
class ConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var ConfigurationRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ConfigurationService constructor.
     *
     * @param ConfigurationRepositoryInterface $repository
     */
    public function __construct(ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return ConfigurationInterface
     * @throws ConfigurationJsonNotFoundException
     */
    public function getSettingsById(string $themeId): ConfigurationInterface
    {
        return $this->repository->getSettingsById($themeId);
    }
    
    
    /**
     * @param StyleEdit3ConfigurationInterface $configuration
     *
     * @return ConfigurationInterface
     */
    public function convertFromStyleEdit3(StyleEdit3ConfigurationInterface $configuration): ConfigurationInterface
    {
        return $this->repository->convertFromStyleEdit3($configuration);
    }
}