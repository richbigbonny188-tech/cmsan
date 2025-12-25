<?php
/* --------------------------------------------------------------
  DisplayUrlsRepository.php 2023-05-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Content;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ListingDisplaySettings;

/**
 * Class DisplayUrlsRepository
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplayUrlsRepository
{
    /**
     * @param UrlBuilder  $url
     * @param UrlSeoBoost $seoBooster
     */
    public function __construct(
        private UrlBuilder  $url,
        private UrlSeoBoost $seoBooster
    )
    {
        //
    }


    /**
     * @param int                    $productId
     * @param string                 $productName
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getProductLink(
        int                    $productId,
        string                 $productName,
        ListingDisplaySettings $settings
    ): string
    {
        $params      = [];
        $seoSettings = $settings->seo();

        if (true === $seoSettings->boostProduct()) {
            $routeName = $this->seoBooster->getProductUrl($productId, $productName);
        } else {
            $productInfo = sprintf('p%s_%s.html', $productId, $this->url->getSlug($productName));
            $routeName   = 'product_info.php';
            $params      = ['info' => $productInfo];
            if (true === $seoSettings->useBoosterLanguage()) {
                $routeName = "{$settings->locale()->language()->code()}/" . $routeName;
            }
        }

        return $this->url->getLink($routeName, $params);
    }


    /**
     * @param Content                $content
     * @param ListingDisplaySettings $settings
     *
     * @return string
     */
    public function getContentLink(
        Content                $content,
        ListingDisplaySettings $settings
    ): string
    {
        $params            = [];
        $seoSettings       = $settings->seo();
        $seoBoostIsEnabled = $seoSettings->boostContent();
        $lightboxIsEnabled = $settings->visibility()->lightboxIsActive();
        $language          = $settings->locale()->language();
        $routeName         = 'popup_content.php';

        if ($seoBoostIsEnabled) {
            $routeName = $this->seoBooster->getContentUrl($content->id(), $language->id());
            if ($lightboxIsEnabled) {
                $routeName = str_replace(
                    'info/',
                    'popup/',
                    $routeName
                );
            }
        } else {
            if (true === $seoSettings->useFriendlyUrls() && false === $seoSettings->boostShortUrls()) {
                $params['content'] = $content->title();
            }
            $params['coID']          = $content->groupId();
            $params['lightbox_mode'] = (int)$lightboxIsEnabled;

            if (true === $seoSettings->useBoosterLanguage()) {
                $routeName = "{$language->code()}/" . $routeName;
            }
        }

        return $seoBoostIsEnabled ? $this->url->getLink($routeName, $params) : $this->url->getPath($routeName, $params);
    }


    /**
     * @param string $actionName
     * @param string $routeName
     * @param array  $params
     * @param bool   $asLink
     *
     * @return string
     */
    public function getAction(
        string $actionName,
        string $routeName,
        array  $params = [],
        bool   $asLink = false
    ): string
    {
        $params    = ['action' => $actionName] + $params;
        $routeName = trim($routeName, '/');

        return $asLink ? $this->url->getLink($routeName, $params) : $this->url->getPath($routeName, $params);
    }
}