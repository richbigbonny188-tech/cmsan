<?php
/* --------------------------------------------------------------
   DSGVOGVSendThemeContentView.inc.php 2018-12-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class representing the GV send content view overload for DSGVO
 */
class DSGVOGVSendThemeContentView extends DSGVOGVSendThemeContentView_parent
{
    protected function send_action()
    {
        /**
         * @var $agreementWriteService AgreementWriteService
         * @var $customerReadService CustomerReadService
         */

        parent::send_action();

        $languageId = new IdType($_SESSION['languages_id']);
        $configKey = new NonEmptyStringType('LOG_IP_GV_SEND');

        $agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
        $customerReadService = StaticGXCoreLoader::getService('CustomerRead');

        $customer = $customerReadService->getCustomerById(new IdType($_SESSION['customer_id']));
        $agreementCustomer = $agreementWriteService->createCustomer(
            new StringType($customer->getFirstname() . ' ' . $customer->getLastname()),
            $customer->getEmail()
        );

        AgreementStoreHelper::store(
            $languageId,
            LegalTextType::PRIVACY,
            $agreementCustomer,
            $configKey
        );
    }

    protected function default_action()
    {
        parent::default_action();

        if (gm_get_conf('GM_SHOW_PRIVACY_GV_SEND') === '1') {
            $this->content_array['show_privacy'] = 1;
            $this->content_array['privacy_link'] = gm_get_privacy_link('GM_SHOW_PRIVACY_GV_SEND');
            $this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_GV_SEND');
        }
    }
}