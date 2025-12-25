<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationService.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration;

use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Exceptions\StyleEdit3ConfigurationNotFoundException;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Interfaces\StyleEdit3ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ConfigurationRepositoryInterface;

/**
 * Class StyleEdit3ConfigurationService
 */
class StyleEdit3ConfigurationService implements StyleEdit3ConfigurationServiceInterface
{
    /**
     * @var StyleEdit3ConfigurationRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * StyleEdit3ConfigurationService constructor.
     *
     * @param StyleEdit3ConfigurationRepositoryInterface $repository
     */
    public function __construct(StyleEdit3ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @return StyleEdit3ConfigurationInterface[]
     */
    public function configurations(): array
    {
        return $this->repository->configurations();
    }
    
    
    /**
     * @param string $path
     *
     * @return StyleEdit3ConfigurationInterface
     * @throws StyleEdit3ConfigurationNotFoundException
     */
    public function getConfigurationByPath(string $path): StyleEdit3ConfigurationInterface
    {
        foreach ($this->repository->configurations() as $configuration) {
            
            if ($configuration->path() === $path) {
                
                return $configuration;
            }
        }
        
        throw new StyleEdit3ConfigurationNotFoundException($path);
    }
}