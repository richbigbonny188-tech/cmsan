<?php

/* --------------------------------------------------------------
	DSGVONewsletterThemeContentView.inc.php 2018-12-12
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class representing the newsletter view overload for DSGVO
 */
class DSGVONewsletterThemeContentView extends DSGVONewsletterThemeContentView_parent
{
    /**
     * Prepare data
     */
    public function prepare_data()
    {
        parent::prepare_data();

        if (!$this->form_send) {
            return;
        }

        $agreementCustomer = StaticGXCoreLoader::getService('AgreementWrite')->createCustomer(
            new StringType(''),
            MainFactory::create('AgreementCustomerEmail', $this->email_address)
        );

        AgreementStoreHelper::store(
            new IdType($_SESSION['languages_id']),
            LegalTextType::PRIVACY,
            $agreementCustomer,
            new NonEmptyStringType('LOG_IP_ACCOUNT_NEWSLETTER')
        );
    }
}