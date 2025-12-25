<?php
/* --------------------------------------------------------------
 ClearCacheOverview.php 2023-09-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Cache\App\Actions;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Modules\Bootstrap\IncludeBootstrap;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class ConfigurationOverview
 *
 * @package Gambio\Admin\Modules\Configuration\App\Actions
 * @codeCoverageIgnore
 */
class ClearCacheOverview extends VuePageAction
{
    use IncludeBootstrap;
    
    /**
     * Clear cache translation section name
     */
    private const CLEAR_CACHE_SECTION = 'clear_cache';
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $data = [];
        $cacheActions = $this->getClearCacheActions();
        $this->includeBootstrapAssets($data);
        $data = array_merge($data, ['actions' => $cacheActions]);
        
        $this->addVuePageTranslation('HEADING_TITLE', self::CLEAR_CACHE_SECTION);
        $this->addVuePageTranslation('TEXT_EXECUTE', self::CLEAR_CACHE_SECTION);
        $this->addVuePageTranslation('TEXT_EXECUTING', self::CLEAR_CACHE_SECTION);
        $this->addVuePageTranslation('TEXT_SUCCESS', self::CLEAR_CACHE_SECTION);
        $this->addVuePageTranslation('TEXT_ERROR', self::CLEAR_CACHE_SECTION);
        
        $template = $this->render($this->translate('HEADING_TITLE', self::CLEAR_CACHE_SECTION),
                                  __DIR__ . '/../../ui/clear_cache_page.html',
                                  $data
                                  );
        
        return $response->write($template);
    }
    
    /**
     * Returns an array with the available "clear cache" options
     * [
     *     [
     *         'id'          => 'clear_cache_id',
     *         'title'       => 'TRANSLATED TITLE',
     *         'description' => 'TRANSLATED DESCRIPTION',
     *         'icon'        => 'font awesome icon class',
     *         'name'        => 'TRANSLATED CACHE NAME',
     *         'caches'      => ['TRANSLATED CACHE NAME 1', 'TRANSLATED CACHE NAME 2']
     *     ],
     *     [
     *         'id'          => 'clear_cache_id',
     *         'title'       => 'TRANSLATED TITLE',
     *         'description' => 'TRANSLATED DESCRIPTION',
     *         'icon'        => 'font awesome icon class',
     *         'name'        => 'TRANSLATED CACHE NAME',
     *         'caches'      => ['TRANSLATED CACHE NAME 1', 'TRANSLATED CACHE NAME 2'],
     *     ]
     * ]
     *
     * @return array
     */
    private function getClearCacheActions(): array
    {
        return [
            [
                'id'          => 'page',
                'title'       => $this->translate('BUTTON_OUTPUT_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_OUTPUT_CACHE', self::CLEAR_CACHE_SECTION),
                'icon'        => 'fa-magic',
            ],
            [
                'id'          => 'module',
                'title'       => $this->translate('BUTTON_DATA_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_DATA_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_DATA_CACHE', self::CLEAR_CACHE_SECTION),
                'caches' => [
                    $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION)
                ],
                'icon'        => 'fa-puzzle-piece',
            ],
            [
                'id'          => 'category',
                'title'       => $this->translate('BUTTON_CATEGORIES_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_CATEGORIES_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_CATEGORIES_CACHE', self::CLEAR_CACHE_SECTION),
                'caches' => [
                    $this->translate('BUTTON_DATA_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                    $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION)
                ],
                'icon'        => 'fa-sitemap',
            ],
            [
                'id'          => 'variant',
                'title'       => $this->translate('BUTTON_PROPERTIES_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_PROPERTIES_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_PROPERTIES_CACHE', self::CLEAR_CACHE_SECTION),
                'caches' => [
                    $this->translate('BUTTON_DATA_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                    $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION)
                ],
                'icon'        => 'fa-th-list',
            ],
            [
                'id'          => 'filter',
                'title'       => $this->translate('BUTTON_FEATURES_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_FEATURES_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_FEATURES_CACHE', self::CLEAR_CACHE_SECTION),
                'caches' => [
                    $this->translate('BUTTON_DATA_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                    $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION)
                ],
                'icon'        => 'fa-filter',
            ],
            [
                'id'          => 'text',
                'title'       => $this->translate('BUTTON_TEXT_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_TEXT_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_TEXT_CACHE', self::CLEAR_CACHE_SECTION),
                'caches' => [
                    $this->translate('BUTTON_DATA_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                    $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION)
                ],
                'icon'        => 'fa-font',
            ],
            [
                'id'          => 'email',
                'title'       => $this->translate('BUTTON_MAIL_TEMPLATES_CACHE', self::CLEAR_CACHE_SECTION),
                'name'        => $this->translate('BUTTON_MAIL_TEMPLATES_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                'description' => $this->translate('TEXT_MAIL_TEMPLATES_CACHE', self::CLEAR_CACHE_SECTION),
                'caches' => [
                    $this->translate('BUTTON_DATA_CACHE_NAME', self::CLEAR_CACHE_SECTION),
                    $this->translate('BUTTON_OUTPUT_CACHE_NAME', self::CLEAR_CACHE_SECTION)
                ],
                'icon'        => 'fa-envelope-o',
            ],
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'cache';
    }
}