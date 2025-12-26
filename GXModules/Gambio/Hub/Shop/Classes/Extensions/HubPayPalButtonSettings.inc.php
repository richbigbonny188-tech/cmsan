<?php
/* --------------------------------------------------------------
   HubPayPalButtonSettings.inc.php 2022-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\Http\CurlRequest;

MainFactory::load_class('HubPayPalSettings');

class HubPayPalButtonSettings extends HubPayPalSettings
{
    /**
     * Returns an array representing a PayPal Smart Buttons configuration.
     *
     * @return array
     */
    public function getButtonSettings()
    {
        $paypalLocaleFactory = MainFactory::create('HubPayPalLocaleFactory');

        $buttonConfig = [
            'jssrc'                => $this->getJavascriptSource(),
            'env'                  => $this->config->getEnv() === 'live' ? 'live' : 'sandbox',
            'commit'               => false,
            'style'                => [
                'label'        => $this->config->getStyleLabel(),
                'shape'        => $this->config->getStyleShape(),
                'color'        => $this->config->getStyleColor(),
                'layout'       => $this->config->getStyleLayout(),
                'tagline'      => $this->config->isStyleTagline(),
            ],
            'locale' => $paypalLocaleFactory->getLocaleByLanguageAndCountry($_SESSION['language_code'],
                                                                            $_SESSION['delivery_zone'] ?? ''),
            'fundingCardAllowed'   => $this->config->isFundingCardAllowed(),
            'fundingELVAllowed'    => $this->config->isFundingELVAllowed(),
            'fundingCreditAllowed' => $this->config->isFundingCreditAllowed(),
            'createPaymentUrl'     => '',
            'authorizedPaymentUrl' => '',
            'checkoutUrl'          => '',
        ];

        return $buttonConfig;
    }

    public function getECMButtonSettings()
    {
        $ecmButtonConfig = $this->getButtonSettings();
        $ecmButtonConfig['jssrc'] = $this->getJavascriptSource('ecm');
        $ecmButtonConfig['style']['layout'] = 'horizontal';
        $ecmButtonConfig['style']['label'] = 'checkout';
        return $ecmButtonConfig;
    }


}
