<?php
/* --------------------------------------------------------------
  ConfigurationServiceFactoryInterface.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Factories\Interfaces;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\Configuration\Converter\Interfaces\StyleEdit3ToStyleEdit4ConverterInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Interfaces\ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationJsonReaderInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationRepositoryInterface;

/**
 * Interface ConfigurationServiceFactoryInterface
 */
interface ConfigurationServiceFactoryInterface
{
    /**
     * @return FilesystemAdapter
     */
    public function themeFilesystem(): FilesystemAdapter;
    
    
    /**
     * @return ConfigurationJsonReaderInterface
     */
    public function jsonReader(): ConfigurationJsonReaderInterface;
    
    
    /**
     * @return ConfigurationRepositoryInterface
     */
    public function repository(): ConfigurationRepositoryInterface;
    
    
    /**
     * @return StyleEdit3ToStyleEdit4ConverterInterface
     */
    public function converter(): StyleEdit3ToStyleEdit4ConverterInterface;
    
    
    /**
     * @return ConfigurationFactoryInterface
     */
    public function factory(): ConfigurationFactoryInterface;
    
    
    /**
     * @return ConfigurationServiceInterface
     */
    public function service(): ConfigurationServiceInterface;
}