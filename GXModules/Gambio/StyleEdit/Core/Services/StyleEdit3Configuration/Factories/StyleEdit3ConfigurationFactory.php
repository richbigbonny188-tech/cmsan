<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationFactory.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories;

use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\StyleEdit3Configuration;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Exceptions\StyleEdit3ConfigurationNotFoundException;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Interfaces\StyleEdit3ConfigurationFactoryInterface;
use stdClass;

/**
 * Class StyleEdit3ConfigurationFactory
 */
class StyleEdit3ConfigurationFactory implements StyleEdit3ConfigurationFactoryInterface
{
    protected const NAME_DETECTION_PATTERN = '/(themes\/|StyleEdit3\/templates\/)([^\/]+)/';
    protected const SE3_VERSION_DETECTION_PATTERN = '/(styles\/styleedit|StyleEdit3)/';

    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    
    /**
     * StyleEdit3ConfigurationFactory constructor.
     *
     * @param FilesystemAdapter $filesystem
     */
    public function __construct(FilesystemAdapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    
    /**
     * @param string $path
     *
     * @return StyleEdit3ConfigurationInterface
     * @throws StyleEdit3ConfigurationNotFoundException
     * @throws FileNotFoundException
     */
    public function createForTheme(string $path): StyleEdit3ConfigurationInterface
    {
        return StyleEdit3Configuration::createForTheme(...$this->getConfigurationConstructorParameters($path));
    }
    
    
    /**
     * @param string $path
     *
     * @return StyleEdit3ConfigurationInterface
     * @throws StyleEdit3ConfigurationNotFoundException
     * @throws FileNotFoundException
     */
    public function createForTemplate(string $path): StyleEdit3ConfigurationInterface
    {
        return StyleEdit3Configuration::createForTemplate(...$this->getConfigurationConstructorParameters($path));
    }
    
    
    /**
     * @param string $path
     *
     * @return array
     * @throws FileNotFoundException
     * @throws StyleEdit3ConfigurationNotFoundException
     */
    protected function getConfigurationConstructorParameters(string $path): array
    {
        $json = $this->getJsonObjectFromPath($path);
        
        if (preg_match(self::NAME_DETECTION_PATTERN, $path, $name)) {
            
            $customCss = $json->customStyles ?? '';
            $name      = end($name);
            $version   = preg_match(self::SE3_VERSION_DETECTION_PATTERN, $path)  ? 'StyleEdit3' : 'StyleEdit4';
            
            // name in the json is the name of the style!
            return [$name, $path, $json->name, $json->settings, $customCss, $json->isActive, $version];
        }
        
        throw new \RuntimeException('Could not determine Theme/Template name from path (' . $path . ')');
    }
    
    
    /**
     * @param string $path
     *
     * @return stdClass
     * @throws FileNotFoundException
     * @throws StyleEdit3ConfigurationNotFoundException
     */
    protected function getJsonObjectFromPath(string $path): stdClass
    {
        if ($this->filesystem->has($path) === false) {
            
            throw new StyleEdit3ConfigurationNotFoundException($path);
        }
        
        $jsonString = $this->filesystem->read($path);
        
        return json_decode($jsonString, false);
    }
}