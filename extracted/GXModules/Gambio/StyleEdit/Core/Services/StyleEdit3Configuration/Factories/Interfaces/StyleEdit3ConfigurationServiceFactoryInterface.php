<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationServiceFactoryInterface.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Interfaces;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Interfaces\StyleEdit3ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ConfigurationRepositoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3TemplateConfigurationReaderInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ThemeConfigurationReaderInterface;

/**
 * Interface StyleEdit3ConfigurationServiceFactoryInterface
 */
interface StyleEdit3ConfigurationServiceFactoryInterface
{
    /**
     * @return FilesystemAdapter
     */
    public function shopRootFilesystem(): FilesystemAdapter;
    
    
    /**
     * @return FilesystemAdapter
     */
    public function themesFilesystem(): FilesystemAdapter;
    
    
    /**
     * @return StyleEdit3ThemeConfigurationReaderInterface
     */
    public function themeConfigurationReader(): StyleEdit3ThemeConfigurationReaderInterface;
    
    
    /**
     * @return StyleEdit3TemplateConfigurationReaderInterface
     */
    public function templateConfigurationReader(): StyleEdit3TemplateConfigurationReaderInterface;
    
    
    /**
     * @return StyleEdit3ConfigurationFactoryInterface
     */
    public function configurationFactory(): StyleEdit3ConfigurationFactoryInterface;
    
    
    /**
     * @return StyleEdit3ConfigurationRepositoryInterface
     */
    public function repository(): StyleEdit3ConfigurationRepositoryInterface;
    
    
    /**
     * @return StyleEdit3ConfigurationServiceInterface
     */
    public function service(): StyleEdit3ConfigurationServiceInterface;
}