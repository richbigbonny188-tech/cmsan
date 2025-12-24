<?php
/* --------------------------------------------------------------
   ApplicationMain.inc.php 2020-03-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_top.php 1323 2005-10-27 17:58:08Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

namespace Gambio\GX;

use InputFilter;
use product;

require_once __DIR__ . '/Application.inc.php';

/**
 * Class ApplicationMain
 * @package Gambio
 */
class ApplicationMain extends Application
{
    public function run()
    {
        $this->registerComposerAutoloader();
        $this->runGProtector();
        
        self::loadConfig();
        
        $this->checkRequestUriForCorrectProtocolAndDomain();
        $this->setUpEnvironment();
        $this->runPrimalExtenders();
        $this->setUpFrontend();
        $this->handleChangeOfLanguageOrCurrencyOrCountry();
        $this->initLanguage();
        $this->updateSessionData();
        $this->initializeGlobalObjects();
        $this->initializeGlobalCategoryVariables();
        $this->runExtenders();
        $this->sendHeader();
    }
    
    
    protected function sanitizeRequestData()
    {
        # check GET/POST/COOKIE VARS
        require_once DIR_WS_CLASSES . 'class.inputfilter.php';
        
        $inputFilter = new InputFilter();
        $_GET        = $inputFilter->process($_GET, true);
        $_POST       = $inputFilter->process($_POST);
    }
    
    
    protected function initializeGlobalObjects()
    {
        parent::initializeGlobalObjects();
        
        $GLOBALS['cPath']       = '';
        $GLOBALS['cPath_array'] = [];
        $GLOBALS['product']     = new product(0, $this->getLanguageId());
    }
}
