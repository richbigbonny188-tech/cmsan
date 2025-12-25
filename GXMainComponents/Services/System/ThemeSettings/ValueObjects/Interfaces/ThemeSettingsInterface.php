<?php
/* --------------------------------------------------------------
  ThemeSettingsInterface.php 2019-08-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces;

use stdClass;

/**
 * Interface ThemeSettingsInterface
 *
 * Represents a theme.json
 */
interface ThemeSettingsInterface
{
    /**
     * id matches directory name
     * @return string
     */
    public function id(): string;
    
    
    /**
     * @return string
     */
    public function path(): string;
    
    
    /**
     * @return bool
     */
    public function isActive(): bool;
    
    
    /**
     * @param bool $isActive
     */
    public function setActive(bool $isActive): void;
    
    
    /**
     * @param stdClass $json
     * @param string   $path
     *
     * @return ThemeSettingsInterface
     */
    public static function createFromJsonObject(stdClass $json, string $path): self;
}