<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationRepository.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Interfaces\StyleEdit3ConfigurationFactoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\StyleEdit3ConfigurationFactory;
use \Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ConfigurationRepositoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3TemplateConfigurationReaderInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ThemeConfigurationReaderInterface;

/**
 * Class StyleEdit3ConfigurationRepository
 */
class StyleEdit3ConfigurationRepository implements StyleEdit3ConfigurationRepositoryInterface
{
    /**
     * @var StyleEdit3ThemeConfigurationReaderInterface
     */
    protected $themeConfigurationReader;
    
    /**
     * @var StyleEdit3TemplateConfigurationReaderInterface
     */
    protected $templateConfigurationReader;
    
    /**
     * @var StyleEdit3ConfigurationFactoryInterface
     */
    protected $factory;
    
    
    /**
     * StyleEdit3ConfigurationRepository constructor.
     *
     * @param StyleEdit3ThemeConfigurationReaderInterface    $themeConfigurationReader
     * @param StyleEdit3TemplateConfigurationReaderInterface $templateConfigurationReader
     * @param StyleEdit3ConfigurationFactoryInterface        $factory
     */
    public function __construct(
        StyleEdit3ThemeConfigurationReaderInterface $themeConfigurationReader,
        StyleEdit3TemplateConfigurationReaderInterface $templateConfigurationReader,
        StyleEdit3ConfigurationFactoryInterface $factory
    ) {
        $this->themeConfigurationReader    = $themeConfigurationReader;
        $this->templateConfigurationReader = $templateConfigurationReader;
        $this->factory                     = $factory;
    }
    
    
    /**
     * @return StyleEdit3ConfigurationInterface[] paths to a StyleEdit3 json file
     */
    public function configurations(): array
    {
        $result = [];
        
        foreach ($this->themeConfigurationReader->configurations() as $configurationPath) {
            
            $result[] = $this->factory->createForTheme($configurationPath);
        }
        
        foreach ($this->templateConfigurationReader->configurations() as $configurationPath) {
            
            $result[] = $this->factory->createForTemplate($configurationPath);
        }
        
        return $result;
    }
}