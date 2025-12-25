<?php
/* --------------------------------------------------------------
  PublishedThemeValidationService.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class PublishedThemeValidationService
 */
class PublishedThemeValidationService implements PublishedThemeValidationServiceInterface
{
    /**
     * @var PublishedThemePathRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var ShopPathsInterface
     */
    protected $shopPaths;
    /**
     * @var CacheControl
     */
    protected $cacheControl;
    
    
    /**
     * PublishedThemeValidationService constructor.
     *
     * @param PublishedThemePathRepositoryInterface $repository
     * @param ShopPathsInterface                    $shopPaths
     * @param CacheControl                          $cacheControl
     */
    public function __construct(PublishedThemePathRepositoryInterface $repository, ShopPathsInterface $shopPaths, CacheControl $cacheControl)
    {
        $this->repository   = $repository;
        $this->shopPaths    = $shopPaths;
        $this->cacheControl = $cacheControl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function publishedThemeIsValid(): bool
    {
        return $this->shopPaths->webPath() === $this->repository->cacheFile()->path();
    }
    
    
    public function removePublishedTheme(): void
    {
        $this->repository->removePublishedTheme();
        $this->cacheControl->clear_content_view_cache();
        $this->cacheControl->clear_templates_c();
        $this->cacheControl->clear_template_cache();
        $this->cacheControl->clear_google_font_cache();
        $this->cacheControl->clear_css_cache();
        $this->cacheControl->clear_expired_shared_shopping_carts();
        $this->cacheControl->remove_reset_token();
        $this->repository->store();
    }
}