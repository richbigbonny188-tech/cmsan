<?php
/* --------------------------------------------------------------
  ThemeSettingsRepository.php 2019-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Repositories;

use Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces\ThemeSettingsFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsReaderInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsRepositoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsWriterInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsInterface;

/**
 * Class ThemeSettingsRepository
 */
class ThemeSettingsRepository implements ThemeSettingsRepositoryInterface
{
    /**
     * @var ThemeSettingsWriterInterface
     */
    protected $writer;
    
    /**
     * @var ThemeSettingsReaderInterface
     */
    protected $reader;
    
    /**
     * @var ThemeSettingsFactoryInterface
     */
    protected $factory;
    
    
    /**
     * ThemeSettingsRepository constructor.
     *
     * @param ThemeSettingsWriterInterface  $writer
     * @param ThemeSettingsReaderInterface  $reader
     * @param ThemeSettingsFactoryInterface $factory
     */
    public function __construct(
        ThemeSettingsWriterInterface $writer,
        ThemeSettingsReaderInterface $reader,
        ThemeSettingsFactoryInterface $factory
    ) {
        $this->writer  = $writer;
        $this->reader  = $reader;
        $this->factory = $factory;
    }
    
    
    /**
     * @param string $id
     *
     * @return ThemeSettingsInterface
     */
    public function getById(string $id): ThemeSettingsInterface
    {
        return $this->factory->createThemeSettings(...$this->reader->getById($id));
    }
    
    
    /**
     * @return ThemeSettingsInterface[]
     */
    public function getAll(): array
    {
        $result = [];
        
        if (count($this->reader->getAll())) {
            
            foreach ($this->reader->getAll() as $themeSettings) {
                
                $result[] = $this->factory->createThemeSettings(...$themeSettings);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param ThemeSettingsInterface $settings
     */
    public function updateThemeSetting(ThemeSettingsInterface $settings): void
    {
        $this->writer->updateThemeSetting($settings);
    }
    
    
    /**
     * @param ThemeSettingsInterface $settings
     */
    public function updateDatabaseEntry(ThemeSettingsInterface $settings): void
    {
        $this->writer->updateDatabaseEntry($settings);
    }
}