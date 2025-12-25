<?php
/*--------------------------------------------------------------------------------------------------
    ConfigurationJsonReader.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Services\Configuration\Repositories;

use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\Configuration\Exceptions\ConfigurationJsonNotFoundException;
use Gambio\StyleEdit\Core\Services\Configuration\Exceptions\ConfigurationJsonNotValidException;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationJsonReaderInterface;
use stdClass;

/**
 * Class ConfigurationJsonReader
 */
class ConfigurationJsonReader implements ConfigurationJsonReaderInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $themeFilesystem;
    
    
    /**
     * ConfigurationJsonReader constructor.
     *
     * @param FilesystemAdapter $themeFilesystem
     */
    public function __construct(FilesystemAdapter $themeFilesystem)
    {
        $this->themeFilesystem = $themeFilesystem;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return array
     * @throws ConfigurationJsonNotFoundException
     * @throws FileNotFoundException
     * @throws ConfigurationJsonNotValidException
     */
    public function getSettingsById(string $themeId): array
    {
        $settingsJsonPath = $themeId . '/settings.json';
        if (!$this->themeFilesystem->has($settingsJsonPath)) {
            $settingsJsonPath = $themeId . '/settings.default.json';
            if (!$this->themeFilesystem->has($settingsJsonPath)) {
        
                throw new ConfigurationJsonNotFoundException($settingsJsonPath);
            }
        }
        
        
        
        $jsonString = $this->themeFilesystem->read($settingsJsonPath);
        $result     = json_decode($jsonString, false);
        
        if ($result === false || !is_array($result)) {
            
            throw new ConfigurationJsonNotValidException($settingsJsonPath);
        }
        
        return $result;
    }
}