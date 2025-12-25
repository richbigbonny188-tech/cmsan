<?php
/* --------------------------------------------------------------
   GambioOmnibusPolicyModuleCenterModule.inc.php 2022-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class GambioOmnibusPolicyModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * @var GambioOmnibusPolicyConfigurationStorage
     */
    private $configuration;
    /**
     * @var GambioOmnibusPolicyTextPhrasesStorage
     */
    private $textPhrases;


    /**
     * @inheritDoc
     */
    protected function _init()
    {
        $this->title         = $this->languageTextManager->get_text('omnibus_title', 'module_center_module');
        $this->description   = $this->languageTextManager->get_text('omnibus_description', 'module_center_module');
        $this->configuration = MainFactory::create('GambioOmnibusPolicyConfigurationStorage');
        $this->textPhrases   = MainFactory::create('GambioOmnibusPolicyTextPhrasesStorage');
        $this->sortOrder     = 99999;
    }
    
    
    public function install()
    {
        parent::install();
    
        $this->clearPageCache();
    }


    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        parent::uninstall();
        // remove module stored data
        $this->configuration->deleteAll();
        $this->textPhrases->deleteAll();
        
        $this->clearPageCache();
    }

    
    /**
     * Clears the page cache
     *
     * @return void
     */
    private function clearPageCache()
    {
        $coo_cache_control = MainFactory::create_object('CacheControl');
    
        $coo_cache_control->clear_content_view_cache();
        $coo_cache_control->clear_templates_c();
        $coo_cache_control->clear_template_cache();
        $coo_cache_control->clear_css_cache();
        
        $this->clearThemeCache();
    }
    
    
    private function clearThemeCache()
    {
        $adapter = $this->getFileSystemAdapter();
        $dir = "public/theme";
    
        if ($adapter->has($dir)) {
            $adapter->deleteDir($dir);
            @$adapter->createDir($dir, ['visibility' => 'public']);
        }
    }
    
    
    private function getFileSystemAdapter()
    {
        $permissionMap = [
            'file' => [
                'public'  => 0777,
                'private' => 0700,
            ],
            'dir'  => [
                'public'  => 0777,
                'private' => 0700,
            ]
        ];
        $visibility = PortableVisibilityConverter::fromArray($permissionMap);
        
        $filesystemAdapter = new LocalFilesystemAdapter(SHOP_ROOT, $visibility, LOCK_EX, LocalFilesystemAdapter::DISALLOW_LINKS);
        $filesystem        = new Filesystem($filesystemAdapter);
    
        return MainFactory::create(FilesystemAdapter::class, $filesystem);
    }
}
