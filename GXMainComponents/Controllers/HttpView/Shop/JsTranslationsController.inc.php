<?php
/* --------------------------------------------------------------
   CreateGuestController.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class JsTranslationsController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class JsTranslationsController extends HttpViewController
{
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    
    /**
     * Returns a json encoded language section array.
     * This method is used by js modules to receive the language values of specific sections.
     *
     * Example (Javascript):
     *  var lang = jse.core.config.get('appUrl') + '/shop.php?do=JsTranslations&section=shared_shopping_cart'
     *
     * The GET-Param 'section' is required.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        $section = $this->_getQueryParameter('section');
        if (null === $section) {
            return new JsonHttpControllerResponse(['status' => 'error']);
        }
        
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'shared_shopping_cart_configuration',
                                                         (int)($_SESSION['languages_id'] ?? null));
        $sectionArray              = $this->languageTextManager->get_section_array($section);
        
        return new JsonHttpControllerResponse($sectionArray);
    }
}