<?php
/* --------------------------------------------------------------
  GoogleFontManager.inc.php 2022-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\StyleEdit\Core\Repositories\Entities\Configuration;
use Gambio\StyleEdit\Core\Services\SettingsService;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Class GoogleFontManager
 */
class GoogleFontManager
{
    protected const GOOGLE_FONT_V1_API_URL = 'https://fonts.googleapis.com/css?family=';
    protected const GOOGLE_FONT_V2_API_URL = 'https://fonts.googleapis.com/css2';
    
    /**
     * @var array
     */
    protected $requiredFonts;
    
    /**
     * @var GoogleFontDownloader
     */
    protected $downloader;
    
    /**
     * @var string
     */
    protected $fontUrl;
    
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    
    /**
     * GoogleFontManager constructor.
     *
     * @param GoogleFontDownloader   $downloader
     * @param string|null            $fontUrl
     * @param FilesystemAdapter|null $filesystem
     *
     * @throws Exception
     */
    public function __construct(
        GoogleFontDownloader $downloader,
        ?string              $fontUrl,
        ?FilesystemAdapter   $filesystem = null
    ) {
        $this->downloader = $downloader;
        $this->fontUrl    = $fontUrl ?? self::GOOGLE_FONT_V1_API_URL;
        $this->filesystem = $filesystem ?? $this->createFilesystemAdapter();
        
        $this->determineRequiredFonts();
    }
    
    
    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function getFontCss(): string
    {
        $this->fontUrl = $this->addRequiredFontFamiliesToUrl($this->fontUrl);
        $fontPath      = $this->fontCssCachePath();
        
        if ($this->filesystem->has($fontPath)) {
            
            return $this->filesystem->read($fontPath);
        }
        
        $this->downloader->downloadFont($this->fontUrl);
        
        if (!$this->filesystem->has($fontPath)) {
            
            return '';
        }
        
        return $this->filesystem->read($fontPath);
    }
    
    
    /**
     * @return string
     */
    protected function fontCssCachePath(): string
    {
        $fontHash = md5($this->fontUrl);
        
        return 'public/fonts/' . $fontHash . '.css';
    }
    
    
    /**
     * @param string $fontFamily
     */
    public function addRequiredFontFamily(string $fontFamily): void
    {
        if (!in_array($fontFamily, $this->requiredFonts, true)) {
            
            $this->requiredFonts[] = $fontFamily;
        }
    }
    
    
    /**
     * @param string $fontFamily
     */
    public function removeRequiredFontFamily(string $fontFamily): void
    {
        if (in_array($fontFamily, $this->requiredFonts, true)) {
            
            $index = array_search($fontFamily, $this->requiredFonts, true);
            unset($this->requiredFonts[$index]);
            $this->requiredFonts = array_values($this->requiredFonts);
        }
    }
    
    
    /**
     * @return array
     */
    public function requiredFonts(): array
    {
        return $this->requiredFonts;
    }
    
    
    /**
     * @param $fontUrl
     *
     * @return mixed
     */
    protected function addRequiredFontFamiliesToUrl($fontUrl)
    {
        $v1ApiRequest = (bool)preg_match('#^' . preg_quote(static::GOOGLE_FONT_V1_API_URL, '#') . '#', $fontUrl);
        $v2ApiRequest = (bool)preg_match('#^' . preg_quote(static::GOOGLE_FONT_V2_API_URL, '#') . '#', $fontUrl);
        
        foreach ($this->requiredFonts as $requiredFont) {
            
            $urlContainsFont = (bool)preg_match('#' . preg_quote($requiredFont, '#') . '#i', $requiredFont);
            
            if ($urlContainsFont === false) {
                
                continue;
            }
    
            $fontUrlName = str_replace(' ', '+', $requiredFont);
            
            if ($v1ApiRequest) {
    
                $additionalUrl = $fontUrl !== static::GOOGLE_FONT_V1_API_URL ? '|' : '';
                $additionalUrl .= $fontUrlName . ':400,700,300,900';
                
                $fontUrl .= $additionalUrl;
            } elseif ($v2ApiRequest) {
    
                $fontUrl .= '&family=' . $fontUrlName;
            } elseif (($logger = LogControl::get_instance()) !== null) {
                
                $message = 'The provided google web font url "%s" is not supported and fonts will not be preloaded';
                $logger->warning(sprintf($message, $fontUrl));
            }
        }
        
        return $fontUrl;
    }
    
    
    /**
     * @return FilesystemAdapter
     */
    protected function createFilesystemAdapter(): FilesystemAdapter
    {
        $shopRoot = dirname(__FILE__, 3);
        
        $permissionMap = [
            'file' => [
                'public'  => 0777,
                'private' => 0700,
            ],
            'dir'  => [
                'public'  => 0777,
                'private' => 0700,
            ],
        ];
        $visibility    = PortableVisibilityConverter::fromArray($permissionMap);
        
        $filesystemAdapter = new LocalFilesystemAdapter($shopRoot,
                                                        $visibility,
                                                        LOCK_EX,
                                                        LocalFilesystemAdapter::DISALLOW_LINKS);
        $leagueFilesystem  = new Filesystem($filesystemAdapter);
        
        return MainFactory::create(FilesystemAdapter::class, $leagueFilesystem);
    }
    
    
    /**
     * @return string
     */
    public function fontUrl(): string
    {
        return $this->fontUrl;
    }
    
    
    /**
     * @throws Exception
     */
    protected function determineRequiredFonts(): void
    {
        // style edit 3 is active and the SettingsService can't read settings
        if (StyleEditServiceFactory::service() instanceof StyleEdit3Service) {
            // previously required fonts
            $this->requiredFonts = [
                'Roboto',
                'Open Sans',
                'Lato',
                'Slabo',
                'Oswald',
                'Source Sans Pro',
                'Montserrat',
                'Raleway',
            ];
            
            return;
        }
        
        $currentThemeId = StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
        $reader         = new SettingsService($currentThemeId);
        
        $this->requiredFonts = $reader->configurationsList()->getArray(); // all stored values from settings.json
        $this->requiredFonts = array_filter($this->requiredFonts,
            // filtered for only font-family options that are none empty
            static function (Configuration $configuration): bool {
                return $configuration->type() === 'fontfamily' && $configuration->value() !== '';
            });
        
        // extracting all set font families
        $this->requiredFonts = array_map(static function (Configuration $configuration): array {
            
            $replaceSlashes = static function (string $value): string {
                return str_replace(['"', '\''], '', $value);
            };
            
            $array = explode(',', $configuration->value());
            $array = array_map($replaceSlashes, $array);
            
            return array_map('trim', $array);
        },
            $this->requiredFonts);
        
        $this->requiredFonts = array_unique(array_merge(...array_values($this->requiredFonts)));
    }
}