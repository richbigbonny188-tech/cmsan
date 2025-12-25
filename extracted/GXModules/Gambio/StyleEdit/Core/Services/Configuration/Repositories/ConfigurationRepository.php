<?php
/* --------------------------------------------------------------
  ConfigurationRepository.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Repositories;

use Gambio\StyleEdit\Core\Services\Configuration\Converter\Interfaces\StyleEdit3ToStyleEdit4ConverterInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Exceptions\ConfigurationJsonNotFoundException;
use Gambio\StyleEdit\Core\Services\Configuration\Factories\Interfaces\ConfigurationFactoryInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationJsonReaderInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationRepositoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;

/**
 * Class ConfigurationRepository
 */
class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    /**
     * @var ConfigurationJsonReaderInterface
     */
    protected $reader;
    
    /**
     * @var ConfigurationFactoryInterface
     */
    protected $factory;
    
    /**
     * @var StyleEdit3ToStyleEdit4ConverterInterface
     */
    protected $converter;
    
    
    /**
     * ConfigurationRepository constructor.
     *
     * @param ConfigurationJsonReaderInterface         $reader
     * @param ConfigurationFactoryInterface            $factory
     * @param StyleEdit3ToStyleEdit4ConverterInterface $converter
     */
    public function __construct(
        ConfigurationJsonReaderInterface $reader,
        ConfigurationFactoryInterface $factory,
        StyleEdit3ToStyleEdit4ConverterInterface $converter
    ) {
        $this->reader    = $reader;
        $this->factory   = $factory;
        $this->converter = $converter;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return ConfigurationInterface
     * @throws ConfigurationJsonNotFoundException
     */
    public function getSettingsById(string $themeId): ConfigurationInterface
    {
        $json = $this->reader->getSettingsById($themeId);
        
        return $this->factory->createSettings($json);
    }
    
    
    /**
     * @param StyleEdit3ConfigurationInterface $configuration
     *
     * @return ConfigurationInterface
     */
    public function convertFromStyleEdit3(StyleEdit3ConfigurationInterface $configuration): ConfigurationInterface
    {
        return $this->converter->convert($configuration);
    }
}