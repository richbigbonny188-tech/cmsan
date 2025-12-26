<?php
/*--------------------------------------------------------------
   AdminCacheAjaxController.php 2023-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

use Gambio\Core\Cache\Services\CacheFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AdminCacheAjaxController
 */
class AdminCacheAjaxController extends AdminHttpViewController
{
    private CacheControl $control;
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionDefault(): JsonHttpControllerResponse
    {
        $data = [
            'page'     => ['page'],
            'module'   => ['module', 'page'],
            'category' => ['module', 'category', 'page'],
            'variant'  => ['module', 'variant', 'page'],
            'filter'   => ['module', 'filter', 'page'],
            'text'     => ['module', 'text', 'page'],
            'email'    => ['module', 'email', 'page'],
        ];
        
        $action        = $this->_getQueryParameter('action');
        $data[$action] ??= null;
        
        try {
            foreach ($data[$action] as $action) {
                $this->clearCacheByAction($action);
            }
            
            return $this->successResponse();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $exception) {
            return $this->errorResponse($exception->getMessage(), 7);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionPage(): JsonHttpControllerResponse
    {
        try {
            $this->clearPageCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 1);
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionModule(): JsonHttpControllerResponse
    {
        try {
            $this->clearModuleCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 2);
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionCategory(): JsonHttpControllerResponse
    {
        try {
            $this->clearCategoryCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 3);
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionVariant(): JsonHttpControllerResponse
    {
        try {
            $this->clearVariantsCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 4);
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionFilter(): JsonHttpControllerResponse
    {
        try {
            $this->clearFilterCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 5);
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionText(): JsonHttpControllerResponse
    {
        try {
            $this->clearTextCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 6);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $exception) {
            return $this->errorResponse($exception->getMessage(), 7);
        }
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionEmail(): JsonHttpControllerResponse
    {
        try {
            $this->clearEmailCache();
            
            return $this->successResponse();
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 8);
        }
    }
    
    
    /**
     * @return CacheControl
     */
    private function cache(): CacheControl
    {
        if (isset($this->control) === false) {
            $this->control = MainFactory::create_object('CacheControl');
        }
        
        return $this->control;
    }
    
    
    /**
     * @param array $data
     *
     * @return JsonHttpControllerResponse
     */
    private function successResponse(array $data = []): JsonHttpControllerResponse
    {
        $response = ['success' => true];
        if (empty($data) === false) {
            $response['data'] = $data;
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * @param string $message
     * @param int    $code
     *
     * @return JsonHttpControllerResponse
     */
    private function errorResponse(string $message, int $code): JsonHttpControllerResponse
    {
        $data = [
            'success' => false,
            'message' => $message,
            'code'    => $code,
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $data);
    }
    
    
    /**
     * @param string $action
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function clearCacheByAction(string $action): void
    {
        match ($action) {
            'page' => $this->clearPageCache(),
            'module' => $this->clearModuleCache(),
            'category' => $this->clearCategoryCache(),
            'variant' => $this->clearVariantsCache(),
            'text' => $this->clearTextCache(),
            'email' => $this->clearEmailCache(),
            default => static fn() => false,
        };
    }
    
    
    /**
     * Clears the page caches
     *
     * @return void
     */
    private function clearPageCache(): void
    {
        $this->cache()->clear_theme_cache();
        $this->cache()->clear_content_view_cache();
        $this->cache()->clear_templates_c();
        $this->cache()->clear_template_cache();
        $this->cache()->clear_google_font_cache();
        $this->cache()->clear_css_cache();
        $this->cache()->clear_expired_shared_shopping_carts();
        $this->cache()->remove_reset_token();
    }
    
    
    /**
     * Clears the modules caches
     *
     * @return void
     */
    private function clearModuleCache(): void
    {
        $this->cache()->clear_data_cache();
        $this->cache()->clear_menu_cache();
    }
    
    
    /**
     * Clears category caches
     *
     * @return void
     */
    private function clearCategoryCache(): void
    {
        $this->cache()->rebuild_products_categories_index();
    }
    
    
    /**
     * Clears variant caches
     *
     * @return void
     */
    private function clearVariantsCache(): void
    {
        $this->cache()->rebuild_products_properties_index();
    }
    
    
    /**
     * Clears filter caches
     *
     * @return void
     */
    private function clearFilterCache(): void
    {
        $this->cache()->rebuild_feature_index();
    }
    
    
    /**
     * Clears text caches
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function clearTextCache(): void
    {
        MainFactory::create_object('PhraseCacheBuilder', [])->build();
        LegacyDependencyContainer::getInstance()->get(CacheFactory::class)->createCacheFor('text_cache')->clear();
    }
    
    
    /**
     * Clears email template caches
     *
     * @return void
     */
    private function clearEmailCache(): void
    {
        MainFactory::create_object('MailTemplatesCacheBuilder')->build();
    }
}