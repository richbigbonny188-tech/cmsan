<?php

/* --------------------------------------------------------------
 QuantityUnitController.inc.php 2018-08-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

MainFactory::load_class('AdminHttpViewController');

/**
 * Class QuantityUnitController
 *
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class QuantityUnitController extends AdminHttpViewController
{
    /**
     * Default action of the quantity unit controller.
     * Provides necessary data to display the quantity unit listing.
     *
     * @return \AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        /** @var \QuantityUnitReadService $quantityUnitReadService */
        
        $languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
        $this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
        $quantityUnitReadService = StaticGXCoreLoader::getService('QuantityUnitRead');
        $languageTextManager     = MainFactory::create('LanguageTextManager');
        $languageTextManager->init_from_lang_file('products_vpe');
        $languageIds = $languageProvider->getAdminIds()->getIntArray();
        $langIds     = [];
        
        foreach ($languageIds as $languageId) {
            $data      = [
                'id'   => $languageId,
                'code' => $languageProvider->getCodeById(new IdType($languageId))
            ];
            $langIds[] = $data;
        }
        
        $title = $languageTextManager->get_text($languageTextManager->get_text('HEADING_TITLE', 'quantity_units'));
        $data  = MainFactory::create('KeyValueCollection',
                                     [
                                         'languages'            => $langIds,
                                         'languageCode'         => $languageProvider->getCodeById(new IdType($_SESSION['languages_id'])),
                                         'quantityUnitEntities' => $quantityUnitReadService->getAll()->getArray(),
                                     ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   new NonEmptyStringType($title),
                                   MainFactory::create('ExistingFile',
                                                       new NonEmptyStringType(DIR_FS_ADMIN
                                                                              . 'html/content/quantity_units/quantity_units.html')),
                                   $data,
                                   MainFactory::create('AssetCollection',
                                                       [
                                                           MainFactory::create('Asset', 'quantity_units.lang.inc.php')
                                                       ]));
    }
}
