<?php
/* --------------------------------------------------------------
   CacheApiV2Controller.inc.php 2020-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

use Gambio\Core\Cache\Services\CacheFactory;

/**
 * Class CategoriesApiV2Controller
 *
 * Provides a gateway to the CategoryWriteService and CategoryReadService classes, which handle the shop category
 * resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CacheApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var CacheControl
     */
    protected $cacheControl;
    
    /**
     * @var CacheFactory
     */
    protected $cacheFactory;
    
    /**
     * @var PhraseCacheBuilder
     */
    protected $phraseCacheBuilder;
    
    /**
     * @var MailTemplatesCacheBuilder
     */
    protected $mailTemplatesCacheBuilder;
    
    
    public function init(): void
    {
        $this->cacheControl              = MainFactory::create_object('CacheControl');
        $this->cacheFactory              = LegacyDependencyContainer::getInstance()->get(CacheFactory::class);
        $this->phraseCacheBuilder        = MainFactory::create_object('PhraseCacheBuilder', []);
        $this->mailTemplatesCacheBuilder = MainFactory::create_object('MailTemplatesCacheBuilder');
    }
    
    
    /**
     * @api        {delete} /cache/:module? Delete all cached data or selected module
     * @apiVersion 2.7.0
     * @apiName    DeleteCache
     * @apiGroup   Cache
     *
     * @apiDescription
     * All cached data is removed/renewed. This method will always return success.
     *
     * @apiExample {curl} Delete all cached data
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/cache
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "delete"
     * }
     */
    public function delete()
    {
        $this->init();
        
        if (count($this->uri) === 1) {
            
            $this->clearAll();
        } else {
            
            switch (strtolower(end($this->uri))) {
                
                case 'page':
                    $this->clearPageCache();
                    break;
                case 'module':
                    $this->clearModuleCache();
                    break;
                case 'products':
                    $this->clearProductsCache();
                    break;
                case 'properties':
                    $this->clearPropertiesCache();
                    break;
                case 'filter':
                    $this->clearFilterCache();
                    break;
                case 'text':
                    $this->clearTextCache();
                    break;
                case 'email':
                    $this->clearEmailCache();
            }
        }
        
        $response = [
            'code'   => 200,
            'status' => 'success',
            'action' => 'delete'
        ];
        
        $this->_writeResponse($response);
    }
    
    
    protected function clearAll(): void
    {
        $this->cacheControl->clear_cache();
        $this->clearPageCache();
        $this->clearModuleCache();
        $this->clearProductsCache();
        $this->clearPropertiesCache();
        $this->clearFilterCache();
        $this->clearTextCache();
        $this->clearEmailCache();
    }
    
    protected function clearPageCache(): void
    {
        $this->clearThemeCache();
        $this->cacheControl->clear_content_view_cache();
        $this->cacheControl->clear_templates_c();
        $this->cacheControl->clear_template_cache();
        $this->cacheControl->clear_css_cache();
        $this->cacheControl->clear_expired_shared_shopping_carts();
        $this->cacheControl->remove_reset_token();
    }
    
    protected function clearThemeCache(): void
    {
        $themeId              = StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
        $themeSourcePath      = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemesPath();
        $themeDestinationPath = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemePath();
    
        $destination   = ThemeDirectoryRoot::create(new RequiredDirectory($themeDestinationPath));
        $themeSettings = ThemeSettings::create(ThemeDirectoryRoot::create(new ExistingDirectory($themeSourcePath)),
                                               $destination);
    
        /** @var ThemeService $themeService */
        $themeService = StaticGXCoreLoader::getService('Theme');
        $themeService->buildTemporaryTheme(ThemeId::create($themeId), $themeSettings);
    }
    
    
    protected function clearModuleCache(): void
    {
        $this->cacheControl->clear_data_cache();
        $this->cacheControl->clear_menu_cache();
    }
    
    protected function clearProductsCache(): void
    {
        $this->cacheControl->rebuild_products_categories_index();
    }
    
    protected function clearPropertiesCache(): void
    {
        $this->cacheControl->rebuild_products_properties_index();
    }
    
    protected function clearFilterCache(): void
    {
        $this->cacheControl->rebuild_feature_index();
    }
    
    protected function clearTextCache(): void
    {
        $this->phraseCacheBuilder->build();
        $this->cacheFactory->createCacheFor('text_cache')->clear();
        $this->cacheControl->clear_data_cache();
    }
    
    protected function clearEmailCache(): void
    {
        $this->mailTemplatesCacheBuilder->build();
    }
}