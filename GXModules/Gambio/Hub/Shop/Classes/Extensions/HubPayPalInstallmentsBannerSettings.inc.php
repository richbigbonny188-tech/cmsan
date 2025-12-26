<?php
/* --------------------------------------------------------------
   HubPayPalInstallmentsBannerSettings.inc.php 2020-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class HubPayPalInstallmentsBannerSettings extends HubPayPalSettings
{
    /**
     * Returns an array representing a PayPal Installments Banner configuration.
     *
     * @return array
     */
    public function getInstallmentsBannerSettings()
    {
        $settings = [
            'jssrc'     => $this->getJavascriptSource(),
            'positions' => [
                'cartBottom' => [
                    'placement' => 'cart',
                    'style' => [
                        'layout' => $this->config->getConfigInstallmentsBannerCartBottomLayout(),
                        // text|flex
                        'logo'   => [
                            'type' => $this->config->getConfigInstallmentsBannerCartBottomLogotype(),
                            // primary|alternative|inline|none (text layout only)
                        ],
                        'text'   => [
                            'color' => $this->config->getConfigInstallmentsBannerCartBottomTextcolor(),
                            // black|white (text layout only)
                        ],
                        'color'  => $this->config->getConfigInstallmentsBannerCartBottomColor(),
                        // blue|black|white|gray (flex layout only)
                        'ratio'  => $this->config->getConfigInstallmentsBannerCartBottomRatio(),
                        // 1x1|1x4|8x1|20x1 (flex layout only)
                    ],
                ],
                'product' => [
                    'placement' => 'product',
                    'style' => [
                        'layout' => $this->config->getConfigInstallmentsBannerProductLayout(),
                        'logo'   => [
                            'type' => $this->config->getConfigInstallmentsBannerProductLogotype(),
                        ],
                        'text'   => [
                            'color' => $this->config->getConfigInstallmentsBannerProductTextcolor(),
                        ],
                        'color'  => $this->config->getConfigInstallmentsBannerProductColor(),
                        'ratio'  => $this->config->getConfigInstallmentsBannerProductRatio(),
                    ],
                ],
            ],
        ];
        
        return $settings;
    }
}
