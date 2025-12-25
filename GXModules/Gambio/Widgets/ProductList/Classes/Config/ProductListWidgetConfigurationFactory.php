<?php
/*--------------------------------------------------------------------------------------------------
    ConfigurationFactory.php 2020-03-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ConfigurationFactory
 */
class ProductListWidgetConfigurationFactory implements ProductListWidgetConfigurationFactoryInterface
{
    /**
     * @param array $params
     * @return ProductListWidgetCommandConfiguration
     * @throws Exception
     */
    public function createCommandConfigurationFromArray(array $params): ProductListWidgetCommandConfiguration
    {
        $showManufacturerImages = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
        $showProductRibbons     = gm_get_conf('SHOW_PRODUCT_RIBBONS');
        $truncate               = gm_get_conf('TRUNCATE_PRODUCTS_NAME');
        $hoverable              = true;
    
        $fullscreenPage = $GLOBALS['coo_template_control']->findSettingValueByName('gx-index-full-width') ?? true;
        $languageId     = $params['languageId'] instanceof IdType ? $params['languageId'] : new IdType((int)$params['languageId']);
        $languageCode   = MainFactory::create(LanguageCode::class, new StringType((string)$params['languageCode']));

        $showRating = false;
        if (gm_get_conf('ENABLE_RATING') === 'true' &&
            gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true'
        ) {
            $showRating = true;
        }

        return new ProductListWidgetCommandConfiguration(
            (int)$params['categoryId'],
            $params['listType'],
            $params['random'],
            $params['itemsPerRowXs'] ?? '2',
            $params['itemsPerRowSm'] ?? '3',
            $params['itemsPerRowMd'] ?? '2',
            $params['itemsPerRowLg'] ?? '5',
            (int)$params['maxProducts'],
            $params['id'],
            $params['class'],
            $params['presentation'],
            $languageId,
            $languageCode,
            $truncate,
            $hoverable,
            $showManufacturerImages,
            $showProductRibbons,
            $fullscreenPage,
            $showRating
        );
    }
}