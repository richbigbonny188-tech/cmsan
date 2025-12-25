<?php
/*--------------------------------------------------------------------------------------------------
    ShopCacheCleaner.php 2022-04-29
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Adapters;

use CacheControl;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Adapters\Interfaces\CacheCleanerInterface;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use Gambio\StyleEdit\StyleEditConfiguration;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use MainFactory;
use RuntimeException;

/**
 * Class ShopCacheCleaner
 * @package Gambio\StyleEdit\Adapters
 */
class ShopCacheCleaner implements CacheCleanerInterface, SingletonStrategyInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $adapter;
    
    /**
     * @var CacheControl
     */
    protected $cacheControl;
    
    
    /**
     * ShopCacheCleaner constructor.
     *
     * @param FilesystemAdapter $adapter
     * @param CacheControl      $cacheControl
     */
    public function __construct(FilesystemAdapter $adapter, CacheControl $cacheControl)
    {
        $this->adapter      = $adapter;
        $this->cacheControl = $cacheControl;
    }
    
    
    /**
     * @return ShopCacheCleaner
     */
    public static function create(): ShopCacheCleaner
    {
        $settings      = new StyleEditConfiguration;
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
    
        $filesystemAdapter = new LocalFilesystemAdapter($settings->baseFolderPath(),
                                                        $visibility,
                                                        LOCK_EX,
                                                        LocalFilesystemAdapter::DISALLOW_LINKS);
        $filesystem        = new Filesystem($filesystemAdapter);
        
        $adapter      = MainFactory::create(FilesystemAdapter::class, $filesystem);
        $cacheControl = MainFactory::create_object(CacheControl::class);
        
        return new self($adapter, $cacheControl);
    }
    
    
    /**
     * @param $themeId
     *
     * @throws FileNotFoundException
     */
    public function clearThemeCache($themeId): void
    {
        $previewFilename = "themes/$themeId/preview.json";
        if ($this->adapter->has($previewFilename)) {
            $previewContent    = $this->adapter->read($previewFilename);
            $previewFileObject = json_decode($previewContent);
            if (json_last_error()) {
                throw new RuntimeException ("Invalid Preview file $previewFilename");
            }
            if ($this->adapter->has($previewFileObject->publishPath)) {
                $this->adapter->deleteDir($previewFileObject->publishPath);
                $this->adapter->createDir($previewFileObject->publishPath, ['visibility' => 'public']);
            }
            if ($this->adapter->has($previewFileObject->compilePath)) {
                $this->adapter->deleteDir($previewFileObject->compilePath);
                $this->adapter->createDir($previewFileObject->compilePath, ['visibility' => 'public']);
            }
        }
    }
    
    
    /**
     */
    public function clearShopCache(): void
    {
        $this->cacheControl->clear_content_view_cache();
        $this->cacheControl->clear_templates_c();
        $this->cacheControl->clear_shop_offline_page_cache();
        $dir = "public/theme";
        
        if ($this->adapter->has($dir)) {
            $this->adapter->deleteDir($dir);
            @$this->adapter->createDir($dir, ['visibility' => 'public']);
        }
    }
}