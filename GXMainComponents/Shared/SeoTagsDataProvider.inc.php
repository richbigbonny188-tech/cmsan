<?php
/* --------------------------------------------------------------
   SeoTagsDataProvider.inc.php 2023-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SeoTagsDataProvider
 *
 * This class provides configuration data for SeoTagsHeaderExtender. By keeping this data inside this separate class
 * third-party developers can extend the lists provided by overloading SeoTagsDataProvider.
 *
 */
class SeoTagsDataProvider
{
    /**
     * Provides list of keys which will be filtered out of the query string before canonical and alternate links are
     * generated.
     *
     * @return array
     */
    public static function getExcludeGetParams()
    {
        return [
            'cat',
            'coID',
            'cPath',
            'XTCsid',
            'no_boost',
            'gm_boosted_category',
            'gm_boosted_content',
            'gm_boosted_product',
            'info',
            'language',
            'currency',
            'products_id',
            'products_qty',
            'ycr',
            'yc'
        ];
    }


    /**
     * Provides a list of keys which if present in the query string ($_GET) will cause the noindex flag to be set.
     *
     * @return array
     */
    public static function getNoIndexKeys()
    {
        return [
            'feature_categories_id',
            'filter_categories_id',
            'filter_fv_id',
            'filter_id',
            'filter_price_max',
            'filter_price_min',
            'keywords',
            'listing_count',
            'listing_sort',
            'manufacturers_id',
            'page',
            'value_conjunction',
            'view_mode',
        ];
    }


    /**
     * Provides a list of keys which if present in the query string ($_GET) will cause the noindex flag to be set
     * depending on page type.
     *
     * @return array
     */
    public static function getNoIndexKeysPerPageType()
    {
        return [
            'product'  => [],
            'category' => ['page'],
            'content'  => [],
            'index'    => [],
            'boosted'  => [],
            'other'    => [],
        ];
    }


    /**
     * Provides a list of keys which if present in the query string ($_GET) will cause the nofollow flag to be set
     * depending on page type.
     *
     * @return array
     */
    public static function getNoFollowKeysPerPageType()
    {
        return [
            'product'  => [],
            'category' => [],
            'content'  => [],
            'index'    => [],
            'boosted'  => [],
            'other'    => [],
        ];
    }


    /**
     * Provides a list of keys which if present in the query string ($_GET) will disable output of prev/next links.
     *
     * @return array
     */
    public static function getNoRelPrevNext()
    {
        return [
            'feature_categories_id',
            'filter_categories_id',
            'filter_fv_id',
            'filter_id',
            'filter_price_max',
            'filter_price_min',
            'listing_count',
            'listing_sort',
            'value_conjunction'
        ];
    }


    /**
     * Provides a list of files (as present in REQUEST_URI) which must not output a canonical URL.
     *
     * @return array
     */
    public static function getNoCanonicalFiles()
    {
        return [
            'specials.php',
            'products_new.php',
        ];
    }


    /**
     * Provides a list of files (as present in REQUEST_URI) which will be marked as noindex.
     *
     * @return array
     */
    public static function getNoIndexFiles()
    {
        $noIndex = [];

        // direct db access used for performance reasons; use StaticSeoUrlReadService otherwise!
        $db                    = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $staticSeoNoIndexPages =
            $db->select('name')->get_where('static_seo_urls', ['robots_disallow_entry' => 1])->result_array();
        foreach ($staticSeoNoIndexPages as $staticSeoNoIndexPageRow) {
            $noIndex[] = $staticSeoNoIndexPageRow['name'];
        }

        return $noIndex;
    }


    /**
     * Provides a list of query string keys which will be filtered out when building canonical URLs.
     *
     * @return array
     */
    public static function getExcludeKeysFromCanonical()
    {
        return [

        ];
    }


    /**
     * Provides a page-specific list of query string keys which will be filtered out when building canonical URLs.
     *
     * @return array
     */
    public static function getExcludeKeysFromCanonicalPerPageType()
    {
        return [
            'default'  => [],
            'product'  => [
                'coID',
                'view_mode',
                'listing_sort',
                'listing_count',
                'manufacturers_id',
                'filter_fv_id',
                'filter_id',
                'filter_price_max',
                'filter_price_min',
                'combi_id',
                'currency',
                'no_boost=1'
            ],
            'category' => [
                'coID',
                'combi_id'
            ],
            'content'  => [
                'view_mode',
                'listing_sort',
                'listing_count',
                'manufacturers_id',
                'filter_fv_id',
                'filter_id',
                'filter_price_max',
                'filter_price_min',
                'combi_id'
            ],
            'index'    => [
                'coID',
                'view_mode',
                'listing_sort',
                'listing_count',
                'manufacturers_id',
                'filter_fv_id',
                'filter_id',
                'filter_price_max',
                'filter_price_min',
                'combi_id',
                'currency'
            ],
            'boosted'  => [],
            'other'    => [
                'coID',
                'view_mode',
                'listing_sort',
                'listing_count',
                'manufacturers_id',
                'filter_fv_id',
                'filter_id',
                'filter_price_max',
                'filter_price_min',
                'combi_id',
                'currency'
            ],
        ];
    }

}


