<?php
/*--------------------------------------------------------------------------------------------------
    ProductWidgetConfigurationFactory.php 2021-12-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ConfigurationFactory
 */
class ProductWidgetConfigurationFactory implements ProductWidgetConfigurationFactoryInterface
{
    /**
     * @param array $params
     * @return ProductWidgetCommandConfiguration
     * @throws Exception
     */
    public function createCommandConfigurationFromArray(array $params): ProductWidgetCommandConfiguration
    {
        $showManufacturerImages = gm_get_conf('SHOW_MANUFACTURER_IMAGE_LISTING');
        $showProductRibbons     = gm_get_conf('SHOW_PRODUCT_RIBBONS');
        $truncate               = gm_get_conf('TRUNCATE_PRODUCTS_NAME');
        $hoverable              = true;

        $fullscreenPage = (bool)$GLOBALS['coo_template_control']->findSettingValueByName('gx-index-full-width');
        $languageId = new IdType((int)$params['languageId']);
        $languageCode = MainFactory::create(LanguageCode::class, new StringType((string)$params['languageCode']));

        $showRating = false;
        if (gm_get_conf('ENABLE_RATING') === 'true' &&
            gm_get_conf('SHOW_RATING_IN_GRID_AND_LISTING') === 'true'
        ) {
            $showRating = true;
        }

        return new ProductWidgetCommandConfiguration(
            $params['id'],
            $params['class'],
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