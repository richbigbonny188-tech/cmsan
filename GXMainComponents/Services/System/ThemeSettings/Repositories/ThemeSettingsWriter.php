<?php
/* --------------------------------------------------------------
  ThemeSettingsWriter.php 2021-05-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Repositories;

use CI_DB_query_builder;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsWriterInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsInterface;

/**
 * Class ThemeSettingsWriter
 */
class ThemeSettingsWriter implements ThemeSettingsWriterInterface
{
    protected const CONFIGURATION_TABLE = 'gx_configurations';
    
    /**
     * @var FilesystemAdapter
     */
    protected $themeDirectory;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ThemeSettingsWriter constructor.
     *
     * @param FilesystemAdapter   $themeDirectory
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(FilesystemAdapter $themeDirectory, CI_DB_query_builder $queryBuilder)
    {
        $this->themeDirectory = $themeDirectory;
        $this->queryBuilder   = $queryBuilder;
    }
    
    
    /**
     * @param ThemeSettingsInterface $settings
     *
     * @throws FileNotFoundException
     */
    public function updateThemeSetting(ThemeSettingsInterface $settings): void
    {
        $themeJsonPath = $settings->path();
        
        if ($this->themeDirectory->has($themeJsonPath)) {
            
            $this->themeDirectory->update($themeJsonPath, json_encode($settings, JSON_PRETTY_PRINT));
        } else {
            
            throw new FileNotFoundException('Could now overwrite "' . $themeJsonPath . '" as it does not exist');
        }
    }
    
    
    /**
     * @param ThemeSettingsInterface $settings
     */
    public function updateDatabaseEntry(ThemeSettingsInterface $settings): void
    {
        $themeId   = $settings->id();
        $timestamp = date('Y-m-d H:i:s');
        
        $this->queryBuilder->update(self::CONFIGURATION_TABLE,
                                    ['value' => $themeId, 'last_modified' => $timestamp],
                                    ['key' => 'configuration/CURRENT_THEME']);
    }
}