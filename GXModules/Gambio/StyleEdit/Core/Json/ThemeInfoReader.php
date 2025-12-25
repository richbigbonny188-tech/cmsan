<?php
/**
 * ThemeInfoReader.php 2019-12-11
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2019 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\StyleEdit\Core\Json;

use \Exception;

class ThemeInfoReader
{
    /**
     * @var array|mixed
     */
    private $themeInfo;
    
    
    public function __construct(string $json)
    {
        $this->themeInfo = $this->getThemeInfo($json);
    }
    
    
    public function getThemeId() : ?string
    {
        return $this->themeInfo['id'] ?? null;
        
    }
    
    
    public function getThemeVersion() : ?string
    {
        return $this->themeInfo['version'] ?? null;
    }
    
    
    private function getThemeInfo(string $json)
    {
        if(is_null($this->themeInfo)) {
            if(!$this->isValidJson($json)) {
                throw new Exception("Invalid theme.json file format.");
            }
            $this->themeInfo = json_decode($json, true);
        }
        return $this->themeInfo;
    }
    
    private function isValidJson(string $json)
    {
        return (is_string($json)
            && is_array(json_decode($json, true))
            && (json_last_error() == JSON_ERROR_NONE));
        
    }
    
}