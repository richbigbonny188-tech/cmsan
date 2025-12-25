<?php
/* --------------------------------------------------------------
   HubPayPalConfiguration.inc.php 2021-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/* Configuration:
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLESHAPE := pill | rect
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLECOLOR := gold | blue | silver
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLEMAXBUTTONS := <int> 1..n
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_ECS := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_ECSPRODUCT := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_ECSDROPDOWN := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_IUPSPECIFICPRODUCT := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_IUPSPECIFICCART := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_IUPSPECIFICPAYMENT := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_IUPSPECIFICCOMPUTED := true|false
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_IUPLENDER := <string>
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_BRANDNAME := <string>

Fixed/deprecated configuration:
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_ENV := sandbox | production
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLELABEL := paypal (checkout | credit| pay | buynow | paypal)
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLESIZE := responsive (small | medium | large | responsive)
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLELAYOUT := vertical (vertical | horizontal)
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLEFUNDINGICONS := false (true | false  (string!))
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_FUNDINGCARD := false (true | false  (string!))
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_FUNDINGELV := false (true | false  (string!))
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_FUNDINGCREDIT := false (true | false  (string!))
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLEBRANDING := true (true | false  (string!))
GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_STYLETAGLINE := false (true | false  (string!))
*/

class HubPayPalConfiguration
{
    protected $configPrefixes = ['GAMBIO_HUB_REMOTE_CONFIG_PAYPAL2HUB_', 'GAMBIO_HUB_REMOTE_CONFIG_PAYPALHUB_'];
    protected $configEnv;
    protected $configStyleLabel;
    protected $configStyleSize;
    protected $configStyleShape;
    protected $configStyleColor;
    protected $configStyleLayout;
    protected $configStyleMaxbuttons;
    protected $configStyleTagline;
    protected $configStyleBranding;
    protected $configStyleFundingicons;
    protected $configFundingCardAllowed;
    protected $configFundingELVAllowed;
    protected $configFundingCreditAllowed;
    protected $configECS;
    protected $configECSProduct;
    protected $configECSDropdown;
    protected $configBrandName;
    protected $configInstallmentsBannerCartBottomLayout;
    protected $configInstallmentsBannerCartBottomLogotype;
    protected $configInstallmentsBannerCartBottomTextcolor;
    protected $configInstallmentsBannerCartBottomColor;
    protected $configInstallmentsBannerCartBottomRatio;
    protected $configInstallmentsBannerProductLayout;
    protected $configInstallmentsBannerProductLogotype;
    protected $configInstallmentsBannerProductTextcolor;
    protected $configInstallmentsBannerProductColor;
    protected $configInstallmentsBannerProductRatio;


    public function __construct()
    {
        $this->configEnv                  = $this->getConfig('MODE');
        $this->configEnv                  = $this->configEnv === 'sandbox' ? 'sandbox' : 'production';
        $this->configStyleShape           = $this->getConfig('STYLESHAPE');
        $this->configStyleShape           = in_array($this->configStyleShape, ['pill', 'rect'], true) ? $this->configStyleShape : 'rect';
        $this->configStyleColor           = $this->getConfig('STYLECOLOR');
        $this->configStyleColor           = in_array(
            $this->configStyleColor,
            ['gold', 'blue', 'silver', 'white', 'black'],
            true
        ) ? $this->configStyleColor : 'gold';
        $this->configStyleMaxbuttons      = max(1, (int)$this->getConfig('STYLEMAXBUTTONS'));
        $this->configECS                  = $this->getConfig('ECS') === 'true';
        $this->configECSProduct           = $this->getConfig('ECSPRODUCT') === 'true';
        $this->configECSDropdown          = $this->getConfig('ECSDROPDOWN') === 'true';

        $this->configStyleLabel           = 'checkout';
        $this->configStyleSize            = 'responsive';
        $this->configStyleLayout          = 'vertical';
        $this->configStyleBranding        = true;
        $this->configStyleFundingicons    = false;
        $this->configFundingCardAllowed   = true;
        $this->configFundingELVAllowed    = true;
        $this->configFundingCreditAllowed = false;
        $this->configStyleTagline         = false;

        $this->configInstallmentsBannerCartBottomLayout    = $this->getConfig('INSTALLMENTSBANNER_CARTBOTTOM_LAYOUT');
        $this->configInstallmentsBannerCartBottomLayout    = $this->configInstallmentsBannerCartBottomLayout === null ? 'none' : $this->configInstallmentsBannerCartBottomLayout;
        $this->configInstallmentsBannerCartBottomLogotype  = $this->getConfig('INSTALLMENTSBANNER_CARTBOTTOM_LOGOTYPE');
        $this->configInstallmentsBannerCartBottomLogotype  = $this->configInstallmentsBannerCartBottomLogotype === null ? 'primary' : $this->configInstallmentsBannerCartBottomLogotype;
        $this->configInstallmentsBannerCartBottomTextcolor = $this->getConfig('INSTALLMENTSBANNER_CARTBOTTOM_TEXTCOLOR');
        $this->configInstallmentsBannerCartBottomTextcolor = $this->configInstallmentsBannerCartBottomTextcolor === null ? 'black' : $this->configInstallmentsBannerCartBottomTextcolor;
        $this->configInstallmentsBannerCartBottomColor     = $this->getConfig('INSTALLMENTSBANNER_CARTBOTTOM_COLOR');
        $this->configInstallmentsBannerCartBottomColor     = $this->configInstallmentsBannerCartBottomColor === null ? 'blue' : $this->configInstallmentsBannerCartBottomColor;
        $this->configInstallmentsBannerCartBottomRatio     = $this->getConfig('INSTALLMENTSBANNER_CARTBOTTOM_RATIO');
        $this->configInstallmentsBannerCartBottomRatio     = $this->configInstallmentsBannerCartBottomRatio === null ? '1x1' : $this->configInstallmentsBannerCartBottomRatio;

        $this->configInstallmentsBannerProductLayout    = $this->getConfig('INSTALLMENTSBANNER_PRODUCT_LAYOUT');
        $this->configInstallmentsBannerProductLayout    = $this->configInstallmentsBannerProductLayout === null ? 'none' : $this->configInstallmentsBannerProductLayout;
        $this->configInstallmentsBannerProductLogotype  = $this->getConfig('INSTALLMENTSBANNER_PRODUCT_LOGOTYPE');
        $this->configInstallmentsBannerProductLogotype  = $this->configInstallmentsBannerProductLogotype === null ? 'primary' : $this->configInstallmentsBannerProductLogotype;
        $this->configInstallmentsBannerProductTextcolor = $this->getConfig('INSTALLMENTSBANNER_PRODUCT_TEXTCOLOR');
        $this->configInstallmentsBannerProductTextcolor = $this->configInstallmentsBannerProductTextcolor === null ? 'black' : $this->configInstallmentsBannerProductTextcolor;
        $this->configInstallmentsBannerProductColor     = $this->getConfig('INSTALLMENTSBANNER_PRODUCT_COLOR');
        $this->configInstallmentsBannerProductColor     = $this->configInstallmentsBannerProductColor === null ? 'blue' : $this->configInstallmentsBannerProductColor;
        $this->configInstallmentsBannerProductRatio     = $this->getConfig('INSTALLMENTSBANNER_PRODUCT_RATIO');
        $this->configInstallmentsBannerProductRatio     = $this->configInstallmentsBannerProductRatio === null ? '1x1' : $this->configInstallmentsBannerProductRatio;
    }


    /**
     * @param $key
     *
     * @return array|bool|mixed|null
     */
    public function getConfig($key)
    {
        $value = null;

        foreach($this->configPrefixes as $configPrefix) {
            $value = gm_get_conf($configPrefix . $key);

            if ($value !== null) {
                break;
            }
        }

        return $value;
    }

    public function getECSButtonConfiguration() {
        $payPalPPGold    = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAyNCAzMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWluWU1pbiBtZWV0Ij4KICAgIDxwYXRoIGZpbGw9IiMwMDljZGUiIGQ9Ik0gMjAuOTA1IDkuNSBDIDIxLjE4NSA3LjQgMjAuOTA1IDYgMTkuNzgyIDQuNyBDIDE4LjU2NCAzLjMgMTYuNDExIDIuNiAxMy42OTcgMi42IEwgNS43MzkgMi42IEMgNS4yNzEgMi42IDQuNzEgMy4xIDQuNjE1IDMuNiBMIDEuMzM5IDI1LjggQyAxLjMzOSAyNi4yIDEuNjIgMjYuNyAyLjA4OCAyNi43IEwgNi45NTYgMjYuNyBMIDYuNjc1IDI4LjkgQyA2LjU4MSAyOS4zIDYuODYyIDI5LjYgNy4yMzYgMjkuNiBMIDExLjM1NiAyOS42IEMgMTEuODI1IDI5LjYgMTIuMjkyIDI5LjMgMTIuMzg2IDI4LjggTCAxMi4zODYgMjguNSBMIDEzLjIyOCAyMy4zIEwgMTMuMjI4IDIzLjEgQyAxMy4zMjIgMjIuNiAxMy43OSAyMi4yIDE0LjI1OCAyMi4yIEwgMTQuODIxIDIyLjIgQyAxOC44NDUgMjIuMiAyMS45MzUgMjAuNSAyMi44NzEgMTUuNSBDIDIzLjMzOSAxMy40IDIzLjE1MyAxMS43IDIyLjAyOSAxMC41IEMgMjEuNzQ4IDEwLjEgMjEuMjc5IDkuOCAyMC45MDUgOS41IEwgMjAuOTA1IDkuNSI+PC9wYXRoPgogICAgPHBhdGggZmlsbD0iIzAxMjE2OSIgZD0iTSAyMC45MDUgOS41IEMgMjEuMTg1IDcuNCAyMC45MDUgNiAxOS43ODIgNC43IEMgMTguNTY0IDMuMyAxNi40MTEgMi42IDEzLjY5NyAyLjYgTCA1LjczOSAyLjYgQyA1LjI3MSAyLjYgNC43MSAzLjEgNC42MTUgMy42IEwgMS4zMzkgMjUuOCBDIDEuMzM5IDI2LjIgMS42MiAyNi43IDIuMDg4IDI2LjcgTCA2Ljk1NiAyNi43IEwgOC4yNjcgMTguNCBMIDguMTczIDE4LjcgQyA4LjI2NyAxOC4xIDguNzM1IDE3LjcgOS4yOTYgMTcuNyBMIDExLjYzNiAxNy43IEMgMTYuMjI0IDE3LjcgMTkuNzgyIDE1LjcgMjAuOTA1IDEwLjEgQyAyMC44MTIgOS44IDIwLjkwNSA5LjcgMjAuOTA1IDkuNSI+PC9wYXRoPgogICAgPHBhdGggZmlsbD0iIzAwMzA4NyIgZD0iTSA5LjQ4NSA5LjUgQyA5LjU3NyA5LjIgOS43NjUgOC45IDEwLjA0NiA4LjcgQyAxMC4yMzIgOC43IDEwLjMyNiA4LjYgMTAuNTEzIDguNiBMIDE2LjY5MiA4LjYgQyAxNy40NDIgOC42IDE4LjE4OSA4LjcgMTguNzUzIDguOCBDIDE4LjkzOSA4LjggMTkuMTI3IDguOCAxOS4zMTQgOC45IEMgMTkuNTAxIDkgMTkuNjg4IDkgMTkuNzgyIDkuMSBDIDE5Ljg3NSA5LjEgMTkuOTY4IDkuMSAyMC4wNjMgOS4xIEMgMjAuMzQzIDkuMiAyMC42MjQgOS40IDIwLjkwNSA5LjUgQyAyMS4xODUgNy40IDIwLjkwNSA2IDE5Ljc4MiA0LjYgQyAxOC42NTggMy4yIDE2LjUwNiAyLjYgMTMuNzkgMi42IEwgNS43MzkgMi42IEMgNS4yNzEgMi42IDQuNzEgMyA0LjYxNSAzLjYgTCAxLjMzOSAyNS44IEMgMS4zMzkgMjYuMiAxLjYyIDI2LjcgMi4wODggMjYuNyBMIDYuOTU2IDI2LjcgTCA4LjI2NyAxOC40IEwgOS40ODUgOS41IFoiPjwvcGF0aD4KPC9zdmc+Cg==';
        $payPalPPBlue   = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAyNCAzMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWluWU1pbiBtZWV0Ij4KICAgIDxwYXRoIGZpbGw9IiNmZmZmZmYiIG9wYWNpdHk9IjAuNyIgZD0iTSAyMC43MDIgOS40NDYgQyAyMC45ODIgNy4zNDcgMjAuNzAyIDUuOTQ3IDE5LjU3OCA0LjU0OCBDIDE4LjM2MSAzLjE0OCAxNi4yMDggMi41NDggMTMuNDkzIDIuNTQ4IEwgNS41MzYgMi41NDggQyA0Ljk3NCAyLjU0OCA0LjUwNiAyLjk0OCA0LjQxMiAzLjU0OCBMIDEuMTM2IDI1Ljc0IEMgMS4wNDIgMjYuMjM5IDEuMzIzIDI2LjYzOSAxLjc5MSAyNi42MzkgTCA2Ljc1MyAyNi42MzkgTCA2LjM3OCAyOC45MzggQyA2LjI4NSAyOS4yMzggNi42NTkgMjkuNjM4IDYuOTQgMjkuNjM4IEwgMTEuMTUzIDI5LjYzOCBDIDExLjYyMSAyOS42MzggMTEuOTk1IDI5LjIzOCAxMi4wODkgMjguNzM5IEwgMTIuMTgyIDI4LjUzOSBMIDEyLjkzMSAyMy4zNDEgTCAxMy4wMjUgMjMuMDQxIEMgMTMuMTE5IDIyLjQ0MSAxMy40OTMgMjIuMTQxIDEzLjk2MSAyMi4xNDEgTCAxNC42MTYgMjIuMTQxIEMgMTguNjQyIDIyLjE0MSAyMS43MzEgMjAuMzQyIDIyLjY2OCAxNS40NDMgQyAyMy4wNDIgMTMuMzQ0IDIyLjg1NSAxMS41NDUgMjEuODI1IDEwLjM0NSBDIDIxLjQ1MSAxMC4wNDYgMjEuMDc2IDkuNjQ2IDIwLjcwMiA5LjQ0NiBMIDIwLjcwMiA5LjQ0NiI+PC9wYXRoPgogICAgPHBhdGggZmlsbD0iI2ZmZmZmZiIgb3BhY2l0eT0iMC43IiBkPSJNIDIwLjcwMiA5LjQ0NiBDIDIwLjk4MiA3LjM0NyAyMC43MDIgNS45NDcgMTkuNTc4IDQuNTQ4IEMgMTguMzYxIDMuMTQ4IDE2LjIwOCAyLjU0OCAxMy40OTMgMi41NDggTCA1LjUzNiAyLjU0OCBDIDQuOTc0IDIuNTQ4IDQuNTA2IDIuOTQ4IDQuNDEyIDMuNTQ4IEwgMS4xMzYgMjUuNzQgQyAxLjA0MiAyNi4yMzkgMS4zMjMgMjYuNjM5IDEuNzkxIDI2LjYzOSBMIDYuNzUzIDI2LjYzOSBMIDcuOTcgMTguMzQyIEwgNy44NzYgMTguNjQyIEMgOC4wNjMgMTguMDQzIDguNDM4IDE3LjY0MyA5LjA5MyAxNy42NDMgTCAxMS40MzMgMTcuNjQzIEMgMTYuMDIxIDE3LjY0MyAxOS41NzggMTUuNjQzIDIwLjYwOCA5Ljk0NiBDIDIwLjYwOCA5Ljc0NiAyMC42MDggOS41NDYgMjAuNzAyIDkuNDQ2Ij48L3BhdGg+CiAgICA8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNIDkuMjggOS40NDYgQyA5LjI4IDkuMTQ2IDkuNDY4IDguODQ2IDkuODQyIDguNjQ2IEMgOS45MzYgOC42NDYgMTAuMTIzIDguNTQ2IDEwLjIxNiA4LjU0NiBMIDE2LjQ4OSA4LjU0NiBDIDE3LjIzOCA4LjU0NiAxNy44OTMgOC42NDYgMTguNTQ4IDguNzQ2IEMgMTguNzM2IDguNzQ2IDE4LjgyOSA4Ljc0NiAxOS4xMSA4Ljg0NiBDIDE5LjIwNCA4Ljk0NiAxOS4zOTEgOC45NDYgMTkuNTc4IDkuMDQ2IEMgMTkuNjcyIDkuMDQ2IDE5LjY3MiA5LjA0NiAxOS44NTkgOS4xNDYgQyAyMC4xNCA5LjI0NiAyMC40MjEgOS4zNDYgMjAuNzAyIDkuNDQ2IEMgMjAuOTgyIDcuMzQ3IDIwLjcwMiA1Ljk0NyAxOS41NzggNC42NDggQyAxOC4zNjEgMy4yNDggMTYuMjA4IDIuNTQ4IDEzLjQ5MyAyLjU0OCBMIDUuNTM2IDIuNTQ4IEMgNC45NzQgMi41NDggNC41MDYgMy4wNDggNC40MTIgMy41NDggTCAxLjEzNiAyNS43NCBDIDEuMDQyIDI2LjIzOSAxLjMyMyAyNi42MzkgMS43OTEgMjYuNjM5IEwgNi43NTMgMjYuNjM5IEwgNy45NyAxOC4zNDIgTCA5LjI4IDkuNDQ2IFoiPjwvcGF0aD4KICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDAuNDk3NzM3LCAwLCAwLCAwLjUyNjEyLCAxLjEwMTQ0LCAwLjYzODY1NCkiIG9wYWNpdHk9IjAuMiI+CiAgICAgICAgPHBhdGggZmlsbD0iIzIzMWYyMCIgZD0iTTM5LjMgMTYuN2MwLjkgMC41IDEuNyAxLjEgMi4zIDEuOCAxIDEuMSAxLjYgMi41IDEuOSA0LjEgMC4zLTMuMi0wLjItNS44LTEuOS03LjgtMC42LTAuNy0xLjMtMS4yLTIuMS0xLjdDMzkuNSAxNC4yIDM5LjUgMTUuNCAzOS4zIDE2Ljd6Ij48L3BhdGg+CiAgICAgICAgPHBhdGggZmlsbD0iIzIzMWYyMCIgZD0iTTAuNCA0NS4yTDYuNyA1LjZDNi44IDQuNSA3LjggMy43IDguOSAzLjdoMTZjNS41IDAgOS44IDEuMiAxMi4yIDMuOSAxLjIgMS40IDEuOSAzIDIuMiA0LjggMC40LTMuNi0wLjItNi4xLTIuMi04LjRDMzQuNyAxLjIgMzAuNCAwIDI0LjkgMEg4LjljLTEuMSAwLTIuMSAwLjgtMi4zIDEuOUwwIDQ0LjFDMCA0NC41IDAuMSA0NC45IDAuNCA0NS4yeiI+PC9wYXRoPgogICAgICAgIDxwYXRoIGZpbGw9IiMyMzFmMjAiIGQ9Ik0xMC43IDQ5LjRsLTAuMSAwLjZjLTAuMSAwLjQgMC4xIDAuOCAwLjQgMS4xbDAuMy0xLjdIMTAuN3oiPjwvcGF0aD4KICAgIDwvZz4KPC9zdmc+Cg==';
        $payPalPPBlack = $payPalPPBlue;
        $payPalPPSilver = $payPalPPGold;

        $payPalLogoGold  = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMTAwIDMyIiB4bWxucz0iaHR0cDomI3gyRjsmI3gyRjt3d3cudzMub3JnJiN4MkY7MjAwMCYjeDJGO3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pbllNaW4gbWVldCI+PHBhdGggZmlsbD0iIzAwMzA4NyIgZD0iTSAxMiA0LjkxNyBMIDQuMiA0LjkxNyBDIDMuNyA0LjkxNyAzLjIgNS4zMTcgMy4xIDUuODE3IEwgMCAyNS44MTcgQyAtMC4xIDI2LjIxNyAwLjIgMjYuNTE3IDAuNiAyNi41MTcgTCA0LjMgMjYuNTE3IEMgNC44IDI2LjUxNyA1LjMgMjYuMTE3IDUuNCAyNS42MTcgTCA2LjIgMjAuMjE3IEMgNi4zIDE5LjcxNyA2LjcgMTkuMzE3IDcuMyAxOS4zMTcgTCA5LjggMTkuMzE3IEMgMTQuOSAxOS4zMTcgMTcuOSAxNi44MTcgMTguNyAxMS45MTcgQyAxOSA5LjgxNyAxOC43IDguMTE3IDE3LjcgNi45MTcgQyAxNi42IDUuNjE3IDE0LjYgNC45MTcgMTIgNC45MTcgWiBNIDEyLjkgMTIuMjE3IEMgMTIuNSAxNS4wMTcgMTAuMyAxNS4wMTcgOC4zIDE1LjAxNyBMIDcuMSAxNS4wMTcgTCA3LjkgOS44MTcgQyA3LjkgOS41MTcgOC4yIDkuMzE3IDguNSA5LjMxNyBMIDkgOS4zMTcgQyAxMC40IDkuMzE3IDExLjcgOS4zMTcgMTIuNCAxMC4xMTcgQyAxMi45IDEwLjUxNyAxMy4xIDExLjIxNyAxMi45IDEyLjIxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwMzA4NyIgZD0iTSAzNS4yIDEyLjExNyBMIDMxLjUgMTIuMTE3IEMgMzEuMiAxMi4xMTcgMzAuOSAxMi4zMTcgMzAuOSAxMi42MTcgTCAzMC43IDEzLjYxNyBMIDMwLjQgMTMuMjE3IEMgMjkuNiAxMi4wMTcgMjcuOCAxMS42MTcgMjYgMTEuNjE3IEMgMjEuOSAxMS42MTcgMTguNCAxNC43MTcgMTcuNyAxOS4xMTcgQyAxNy4zIDIxLjMxNyAxNy44IDIzLjQxNyAxOS4xIDI0LjgxNyBDIDIwLjIgMjYuMTE3IDIxLjkgMjYuNzE3IDIzLjggMjYuNzE3IEMgMjcuMSAyNi43MTcgMjkgMjQuNjE3IDI5IDI0LjYxNyBMIDI4LjggMjUuNjE3IEMgMjguNyAyNi4wMTcgMjkgMjYuNDE3IDI5LjQgMjYuNDE3IEwgMzIuOCAyNi40MTcgQyAzMy4zIDI2LjQxNyAzMy44IDI2LjAxNyAzMy45IDI1LjUxNyBMIDM1LjkgMTIuNzE3IEMgMzYgMTIuNTE3IDM1LjYgMTIuMTE3IDM1LjIgMTIuMTE3IFogTSAzMC4xIDE5LjMxNyBDIDI5LjcgMjEuNDE3IDI4LjEgMjIuOTE3IDI1LjkgMjIuOTE3IEMgMjQuOCAyMi45MTcgMjQgMjIuNjE3IDIzLjQgMjEuOTE3IEMgMjIuOCAyMS4yMTcgMjIuNiAyMC4zMTcgMjIuOCAxOS4zMTcgQyAyMy4xIDE3LjIxNyAyNC45IDE1LjcxNyAyNyAxNS43MTcgQyAyOC4xIDE1LjcxNyAyOC45IDE2LjExNyAyOS41IDE2LjcxNyBDIDMwIDE3LjQxNyAzMC4yIDE4LjMxNyAzMC4xIDE5LjMxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwMzA4NyIgZD0iTSA1NS4xIDEyLjExNyBMIDUxLjQgMTIuMTE3IEMgNTEgMTIuMTE3IDUwLjcgMTIuMzE3IDUwLjUgMTIuNjE3IEwgNDUuMyAyMC4yMTcgTCA0My4xIDEyLjkxNyBDIDQzIDEyLjQxNyA0Mi41IDEyLjExNyA0Mi4xIDEyLjExNyBMIDM4LjQgMTIuMTE3IEMgMzggMTIuMTE3IDM3LjYgMTIuNTE3IDM3LjggMTMuMDE3IEwgNDEuOSAyNS4xMTcgTCAzOCAzMC41MTcgQyAzNy43IDMwLjkxNyAzOCAzMS41MTcgMzguNSAzMS41MTcgTCA0Mi4yIDMxLjUxNyBDIDQyLjYgMzEuNTE3IDQyLjkgMzEuMzE3IDQzLjEgMzEuMDE3IEwgNTUuNiAxMy4wMTcgQyA1NS45IDEyLjcxNyA1NS42IDEyLjExNyA1NS4xIDEyLjExNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA2Ny41IDQuOTE3IEwgNTkuNyA0LjkxNyBDIDU5LjIgNC45MTcgNTguNyA1LjMxNyA1OC42IDUuODE3IEwgNTUuNSAyNS43MTcgQyA1NS40IDI2LjExNyA1NS43IDI2LjQxNyA1Ni4xIDI2LjQxNyBMIDYwLjEgMjYuNDE3IEMgNjAuNSAyNi40MTcgNjAuOCAyNi4xMTcgNjAuOCAyNS44MTcgTCA2MS43IDIwLjExNyBDIDYxLjggMTkuNjE3IDYyLjIgMTkuMjE3IDYyLjggMTkuMjE3IEwgNjUuMyAxOS4yMTcgQyA3MC40IDE5LjIxNyA3My40IDE2LjcxNyA3NC4yIDExLjgxNyBDIDc0LjUgOS43MTcgNzQuMiA4LjAxNyA3My4yIDYuODE3IEMgNzIgNS42MTcgNzAuMSA0LjkxNyA2Ny41IDQuOTE3IFogTSA2OC40IDEyLjIxNyBDIDY4IDE1LjAxNyA2NS44IDE1LjAxNyA2My44IDE1LjAxNyBMIDYyLjYgMTUuMDE3IEwgNjMuNCA5LjgxNyBDIDYzLjQgOS41MTcgNjMuNyA5LjMxNyA2NCA5LjMxNyBMIDY0LjUgOS4zMTcgQyA2NS45IDkuMzE3IDY3LjIgOS4zMTcgNjcuOSAxMC4xMTcgQyA2OC40IDEwLjUxNyA2OC41IDExLjIxNyA2OC40IDEyLjIxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA5MC43IDEyLjExNyBMIDg3IDEyLjExNyBDIDg2LjcgMTIuMTE3IDg2LjQgMTIuMzE3IDg2LjQgMTIuNjE3IEwgODYuMiAxMy42MTcgTCA4NS45IDEzLjIxNyBDIDg1LjEgMTIuMDE3IDgzLjMgMTEuNjE3IDgxLjUgMTEuNjE3IEMgNzcuNCAxMS42MTcgNzMuOSAxNC43MTcgNzMuMiAxOS4xMTcgQyA3Mi44IDIxLjMxNyA3My4zIDIzLjQxNyA3NC42IDI0LjgxNyBDIDc1LjcgMjYuMTE3IDc3LjQgMjYuNzE3IDc5LjMgMjYuNzE3IEMgODIuNiAyNi43MTcgODQuNSAyNC42MTcgODQuNSAyNC42MTcgTCA4NC4zIDI1LjYxNyBDIDg0LjIgMjYuMDE3IDg0LjUgMjYuNDE3IDg0LjkgMjYuNDE3IEwgODguMyAyNi40MTcgQyA4OC44IDI2LjQxNyA4OS4zIDI2LjAxNyA4OS40IDI1LjUxNyBMIDkxLjQgMTIuNzE3IEMgOTEuNCAxMi41MTcgOTEuMSAxMi4xMTcgOTAuNyAxMi4xMTcgWiBNIDg1LjUgMTkuMzE3IEMgODUuMSAyMS40MTcgODMuNSAyMi45MTcgODEuMyAyMi45MTcgQyA4MC4yIDIyLjkxNyA3OS40IDIyLjYxNyA3OC44IDIxLjkxNyBDIDc4LjIgMjEuMjE3IDc4IDIwLjMxNyA3OC4yIDE5LjMxNyBDIDc4LjUgMTcuMjE3IDgwLjMgMTUuNzE3IDgyLjQgMTUuNzE3IEMgODMuNSAxNS43MTcgODQuMyAxNi4xMTcgODQuOSAxNi43MTcgQyA4NS41IDE3LjQxNyA4NS43IDE4LjMxNyA4NS41IDE5LjMxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA5NS4xIDUuNDE3IEwgOTEuOSAyNS43MTcgQyA5MS44IDI2LjExNyA5Mi4xIDI2LjQxNyA5Mi41IDI2LjQxNyBMIDk1LjcgMjYuNDE3IEMgOTYuMiAyNi40MTcgOTYuNyAyNi4wMTcgOTYuOCAyNS41MTcgTCAxMDAgNS42MTcgQyAxMDAuMSA1LjIxNyA5OS44IDQuOTE3IDk5LjQgNC45MTcgTCA5NS44IDQuOTE3IEMgOTUuNCA0LjkxNyA5NS4yIDUuMTE3IDk1LjEgNS40MTcgWiI+PC9wYXRoPjwvc3ZnPg==';
        $payPalLogoBlue = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMTAwIDMyIiB4bWxucz0iaHR0cDomI3gyRjsmI3gyRjt3d3cudzMub3JnJiN4MkY7MjAwMCYjeDJGO3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pbllNaW4gbWVldCI+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSAxMiA0LjkxNyBMIDQuMiA0LjkxNyBDIDMuNyA0LjkxNyAzLjIgNS4zMTcgMy4xIDUuODE3IEwgMCAyNS44MTcgQyAtMC4xIDI2LjIxNyAwLjIgMjYuNTE3IDAuNiAyNi41MTcgTCA0LjMgMjYuNTE3IEMgNC44IDI2LjUxNyA1LjMgMjYuMTE3IDUuNCAyNS42MTcgTCA2LjIgMjAuMjE3IEMgNi4zIDE5LjcxNyA2LjcgMTkuMzE3IDcuMyAxOS4zMTcgTCA5LjggMTkuMzE3IEMgMTQuOSAxOS4zMTcgMTcuOSAxNi44MTcgMTguNyAxMS45MTcgQyAxOSA5LjgxNyAxOC43IDguMTE3IDE3LjcgNi45MTcgQyAxNi42IDUuNjE3IDE0LjYgNC45MTcgMTIgNC45MTcgWiBNIDEyLjkgMTIuMjE3IEMgMTIuNSAxNS4wMTcgMTAuMyAxNS4wMTcgOC4zIDE1LjAxNyBMIDcuMSAxNS4wMTcgTCA3LjkgOS44MTcgQyA3LjkgOS41MTcgOC4yIDkuMzE3IDguNSA5LjMxNyBMIDkgOS4zMTcgQyAxMC40IDkuMzE3IDExLjcgOS4zMTcgMTIuNCAxMC4xMTcgQyAxMi45IDEwLjUxNyAxMy4xIDExLjIxNyAxMi45IDEyLjIxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSAzNS4yIDEyLjExNyBMIDMxLjUgMTIuMTE3IEMgMzEuMiAxMi4xMTcgMzAuOSAxMi4zMTcgMzAuOSAxMi42MTcgTCAzMC43IDEzLjYxNyBMIDMwLjQgMTMuMjE3IEMgMjkuNiAxMi4wMTcgMjcuOCAxMS42MTcgMjYgMTEuNjE3IEMgMjEuOSAxMS42MTcgMTguNCAxNC43MTcgMTcuNyAxOS4xMTcgQyAxNy4zIDIxLjMxNyAxNy44IDIzLjQxNyAxOS4xIDI0LjgxNyBDIDIwLjIgMjYuMTE3IDIxLjkgMjYuNzE3IDIzLjggMjYuNzE3IEMgMjcuMSAyNi43MTcgMjkgMjQuNjE3IDI5IDI0LjYxNyBMIDI4LjggMjUuNjE3IEMgMjguNyAyNi4wMTcgMjkgMjYuNDE3IDI5LjQgMjYuNDE3IEwgMzIuOCAyNi40MTcgQyAzMy4zIDI2LjQxNyAzMy44IDI2LjAxNyAzMy45IDI1LjUxNyBMIDM1LjkgMTIuNzE3IEMgMzYgMTIuNTE3IDM1LjYgMTIuMTE3IDM1LjIgMTIuMTE3IFogTSAzMC4xIDE5LjMxNyBDIDI5LjcgMjEuNDE3IDI4LjEgMjIuOTE3IDI1LjkgMjIuOTE3IEMgMjQuOCAyMi45MTcgMjQgMjIuNjE3IDIzLjQgMjEuOTE3IEMgMjIuOCAyMS4yMTcgMjIuNiAyMC4zMTcgMjIuOCAxOS4zMTcgQyAyMy4xIDE3LjIxNyAyNC45IDE1LjcxNyAyNyAxNS43MTcgQyAyOC4xIDE1LjcxNyAyOC45IDE2LjExNyAyOS41IDE2LjcxNyBDIDMwIDE3LjQxNyAzMC4yIDE4LjMxNyAzMC4xIDE5LjMxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA1NS4xIDEyLjExNyBMIDUxLjQgMTIuMTE3IEMgNTEgMTIuMTE3IDUwLjcgMTIuMzE3IDUwLjUgMTIuNjE3IEwgNDUuMyAyMC4yMTcgTCA0My4xIDEyLjkxNyBDIDQzIDEyLjQxNyA0Mi41IDEyLjExNyA0Mi4xIDEyLjExNyBMIDM4LjQgMTIuMTE3IEMgMzggMTIuMTE3IDM3LjYgMTIuNTE3IDM3LjggMTMuMDE3IEwgNDEuOSAyNS4xMTcgTCAzOCAzMC41MTcgQyAzNy43IDMwLjkxNyAzOCAzMS41MTcgMzguNSAzMS41MTcgTCA0Mi4yIDMxLjUxNyBDIDQyLjYgMzEuNTE3IDQyLjkgMzEuMzE3IDQzLjEgMzEuMDE3IEwgNTUuNiAxMy4wMTcgQyA1NS45IDEyLjcxNyA1NS42IDEyLjExNyA1NS4xIDEyLjExNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA2Ny41IDQuOTE3IEwgNTkuNyA0LjkxNyBDIDU5LjIgNC45MTcgNTguNyA1LjMxNyA1OC42IDUuODE3IEwgNTUuNSAyNS43MTcgQyA1NS40IDI2LjExNyA1NS43IDI2LjQxNyA1Ni4xIDI2LjQxNyBMIDYwLjEgMjYuNDE3IEMgNjAuNSAyNi40MTcgNjAuOCAyNi4xMTcgNjAuOCAyNS44MTcgTCA2MS43IDIwLjExNyBDIDYxLjggMTkuNjE3IDYyLjIgMTkuMjE3IDYyLjggMTkuMjE3IEwgNjUuMyAxOS4yMTcgQyA3MC40IDE5LjIxNyA3My40IDE2LjcxNyA3NC4yIDExLjgxNyBDIDc0LjUgOS43MTcgNzQuMiA4LjAxNyA3My4yIDYuODE3IEMgNzIgNS42MTcgNzAuMSA0LjkxNyA2Ny41IDQuOTE3IFogTSA2OC40IDEyLjIxNyBDIDY4IDE1LjAxNyA2NS44IDE1LjAxNyA2My44IDE1LjAxNyBMIDYyLjYgMTUuMDE3IEwgNjMuNCA5LjgxNyBDIDYzLjQgOS41MTcgNjMuNyA5LjMxNyA2NCA5LjMxNyBMIDY0LjUgOS4zMTcgQyA2NS45IDkuMzE3IDY3LjIgOS4zMTcgNjcuOSAxMC4xMTcgQyA2OC40IDEwLjUxNyA2OC41IDExLjIxNyA2OC40IDEyLjIxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA5MC43IDEyLjExNyBMIDg3IDEyLjExNyBDIDg2LjcgMTIuMTE3IDg2LjQgMTIuMzE3IDg2LjQgMTIuNjE3IEwgODYuMiAxMy42MTcgTCA4NS45IDEzLjIxNyBDIDg1LjEgMTIuMDE3IDgzLjMgMTEuNjE3IDgxLjUgMTEuNjE3IEMgNzcuNCAxMS42MTcgNzMuOSAxNC43MTcgNzMuMiAxOS4xMTcgQyA3Mi44IDIxLjMxNyA3My4zIDIzLjQxNyA3NC42IDI0LjgxNyBDIDc1LjcgMjYuMTE3IDc3LjQgMjYuNzE3IDc5LjMgMjYuNzE3IEMgODIuNiAyNi43MTcgODQuNSAyNC42MTcgODQuNSAyNC42MTcgTCA4NC4zIDI1LjYxNyBDIDg0LjIgMjYuMDE3IDg0LjUgMjYuNDE3IDg0LjkgMjYuNDE3IEwgODguMyAyNi40MTcgQyA4OC44IDI2LjQxNyA4OS4zIDI2LjAxNyA4OS40IDI1LjUxNyBMIDkxLjQgMTIuNzE3IEMgOTEuNCAxMi41MTcgOTEuMSAxMi4xMTcgOTAuNyAxMi4xMTcgWiBNIDg1LjUgMTkuMzE3IEMgODUuMSAyMS40MTcgODMuNSAyMi45MTcgODEuMyAyMi45MTcgQyA4MC4yIDIyLjkxNyA3OS40IDIyLjYxNyA3OC44IDIxLjkxNyBDIDc4LjIgMjEuMjE3IDc4IDIwLjMxNyA3OC4yIDE5LjMxNyBDIDc4LjUgMTcuMjE3IDgwLjMgMTUuNzE3IDgyLjQgMTUuNzE3IEMgODMuNSAxNS43MTcgODQuMyAxNi4xMTcgODQuOSAxNi43MTcgQyA4NS41IDE3LjQxNyA4NS43IDE4LjMxNyA4NS41IDE5LjMxNyBaIj48L3BhdGg+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA5NS4xIDUuNDE3IEwgOTEuOSAyNS43MTcgQyA5MS44IDI2LjExNyA5Mi4xIDI2LjQxNyA5Mi41IDI2LjQxNyBMIDk1LjcgMjYuNDE3IEMgOTYuMiAyNi40MTcgOTYuNyAyNi4wMTcgOTYuOCAyNS41MTcgTCAxMDAgNS42MTcgQyAxMDAuMSA1LjIxNyA5OS44IDQuOTE3IDk5LjQgNC45MTcgTCA5NS44IDQuOTE3IEMgOTUuNCA0LjkxNyA5NS4yIDUuMTE3IDk1LjEgNS40MTcgWiI+PC9wYXRoPjwvc3ZnPg==';
        $payPalLogoBlack = $payPalLogoBlue;
        $payPalLogoSilver = $payPalLogoGold;

        $bgGold           = '#ffc439';
        $borderGold       = '#cba13f';
        $bgBlue           = '#009cde';
        $borderBlue       = '#0086bf';
        $bgSilver         = '#eee';
        $borderSilver     = '#ccc';
        $bgBlack          = '#2C2E2F';
        $borderBlack      = '#ddd';
        $borderRadiusRect = '4px';
        $borderRadiusPill = '15px';

        $buttonConfiguration = [];
        if($this->getStyleColor() === 'gold')
        {
            $buttonConfiguration['ppUrl'] = $payPalPPGold;
            $buttonConfiguration['logoUrl'] = $payPalLogoGold;
            $buttonConfiguration['backgroundColor'] = $bgGold;
            $buttonConfiguration['borderColor'] = $borderGold;
        }
        elseif($this->getStyleColor() === 'silver')
        {
            $buttonConfiguration['ppUrl'] = $payPalPPSilver;
            $buttonConfiguration['logoUrl'] = $payPalLogoSilver;
            $buttonConfiguration['backgroundColor'] = $bgSilver;
            $buttonConfiguration['borderColor'] = $borderSilver;
        }
        elseif($this->getStyleColor() === 'blue')
        {
            $buttonConfiguration['ppUrl'] = $payPalPPBlue;
            $buttonConfiguration['logoUrl'] = $payPalLogoBlue;
            $buttonConfiguration['backgroundColor'] = $bgBlue;
            $buttonConfiguration['borderColor'] = $borderBlue;
        }
        elseif($this->getStyleColor() === 'black')
        {
            $buttonConfiguration['ppUrl'] = $payPalPPBlack;
            $buttonConfiguration['logoUrl'] = $payPalLogoBlack;
            $buttonConfiguration['backgroundColor'] = $bgBlack;
            $buttonConfiguration['borderColor'] = $borderBlack;
        }

        if($this->getStyleShape() === 'rect')
        {
            $buttonConfiguration['borderRadius'] = $borderRadiusRect;
        }
        else
        {
            $buttonConfiguration['borderRadius'] = $borderRadiusPill;
        }

        return $buttonConfiguration;
    }


    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->configEnv;
    }


    /**
     * @param string $configEnv
     */
    public function setEnv($configEnv)
    {
        $this->configEnv = $configEnv;
    }


    /**
     * @return array|bool|string|null
     */
    public function getStyleLabel()
    {
        return $this->configStyleLabel;
    }


    /**
     * @param array|bool|string|null $configStyleLabel
     */
    public function setStyleLabel($configStyleLabel)
    {
        $this->configStyleLabel = $configStyleLabel;
    }


    /**
     * @return array|bool|string|null
     */
    public function getStyleSize()
    {
        return $this->configStyleSize;
    }


    /**
     * @param array|bool|string|null $configStyleSize
     */
    public function setStyleSize($configStyleSize)
    {
        $this->configStyleSize = $configStyleSize;
    }


    /**
     * @return array|bool|string|null
     */
    public function getStyleShape()
    {
        return $this->configStyleShape;
    }


    /**
     * @param array|bool|string|null $configStyleShape
     */
    public function setStyleShape($configStyleShape)
    {
        $this->configStyleShape = $configStyleShape;
    }


    /**
     * @return array|bool|string|null
     */
    public function getStyleColor()
    {
        return $this->configStyleColor;
    }


    /**
     * @param array|bool|string|null $configStyleColor
     */
    public function setStyleColor($configStyleColor)
    {
        $this->configStyleColor = $configStyleColor;
    }


    /**
     * @return array|bool|string|null
     */
    public function getStyleLayout()
    {
        return $this->configStyleLayout;
    }


    /**
     * @param array|bool|string|null $configStyleLayout
     */
    public function setStyleLayout($configStyleLayout)
    {
        $this->configStyleLayout = $configStyleLayout;
    }


    /**
     * @return mixed
     */
    public function getStyleMaxbuttons()
    {
        return $this->configStyleMaxbuttons;
    }


    /**
     * @param mixed $configStyleMaxbuttons
     */
    public function setStyleMaxbuttons($configStyleMaxbuttons)
    {
        $this->configStyleMaxbuttons = $configStyleMaxbuttons;
    }


    /**
     * @return bool
     */
    public function isStyleTagline()
    {
        return $this->configStyleTagline;
    }


    /**
     * @param bool $configStyleTagline
     */
    public function setStyleTagline($configStyleTagline)
    {
        $this->configStyleTagline = $configStyleTagline;
    }


    /**
     * @return bool
     */
    public function isStyleFundingicons()
    {
        return $this->configStyleFundingicons;
    }


    /**
     * @param bool $configStyleFundingicons
     */
    public function setStyleFundingicons($configStyleFundingicons)
    {
        $this->configStyleFundingicons = $configStyleFundingicons;
    }


    /**
     * @return bool
     */
    public function isFundingCardAllowed()
    {
        return $this->configFundingCardAllowed;
    }


    /**
     * @param bool $configFundingCardAllowed
     */
    public function setFundingCardAllowed($configFundingCardAllowed)
    {
        $this->configFundingCardAllowed = $configFundingCardAllowed;
    }


    /**
     * @return bool
     */
    public function isFundingELVAllowed()
    {
        return $this->configFundingELVAllowed;
    }


    /**
     * @param bool $configFundingELVAllowed
     */
    public function setFundingELVAllowed($configFundingELVAllowed)
    {
        $this->configFundingELVAllowed = $configFundingELVAllowed;
    }


    /**
     * @return bool
     */
    public function isFundingCreditAllowed()
    {
        return $this->configFundingCreditAllowed;
    }


    /**
     * @param bool $configFundingCreditAllowed
     */
    public function setFundingCreditAllowed($configFundingCreditAllowed)
    {
        $this->configFundingCreditAllowed = $configFundingCreditAllowed;
    }


    /**
     * @return bool
     */
    public function isStyleBranding()
    {
        return $this->configStyleBranding;
    }


    /**
     * @param bool $configStyleBranding
     */
    public function setStyleBranding($configStyleBranding)
    {
        $this->configStyleBranding = $configStyleBranding;
    }


    /**
     * @return bool
     */
    public function isECS()
    {
        return $this->configECS;
    }


    /**
     * @param bool $configECS
     */
    public function setECS($configECS)
    {
        $this->configECS = (bool)$configECS;
    }


    /**
     * @return bool
     */
    public function isECSProduct()
    {
        return $this->isECS() && $this->configECSProduct;
    }


    /**
     * @param bool $configECSProduct
     */
    public function setECSProduct($configECSProduct)
    {
        $this->configECSProduct = $configECSProduct;
    }


    /**
     * @return bool
     */
    public function isECSDropdown()
    {
        return $this->isECS() && $this->configECSDropdown;
    }


    /**
     * @param bool $configECSDropdown
     */
    public function setECSDropdown($configECSDropdown)
    {
        $this->configECSDropdown = $configECSDropdown;
    }


    /**
     * @return mixed
     */
    public function getConfigBrandName()
    {
        return $this->configBrandName;
    }


    /**
     * @param mixed $configBrandName
     */
    public function setConfigBrandName($configBrandName)
    {
        $this->configBrandName = (string)$configBrandName;
    }


    /**
     * @return string
     */
    public function getConfigInstallmentsBannerCartBottomLayout()
    {
        return $this->configInstallmentsBannerCartBottomLayout;
    }


    /**
     * @param string $configInstallmentsBannerCartBottomLayout
     */
    public function setConfigInstallmentsBannerCartBottomLayout($configInstallmentsBannerCartBottomLayout)
    {
        $this->configInstallmentsBannerCartBottomLayout = $configInstallmentsBannerCartBottomLayout;
    }


    /**
     * @return string
     */
    public function getConfigInstallmentsBannerCartBottomLogotype()
    {
        return $this->configInstallmentsBannerCartBottomLogotype;
    }


    /**
     * @param string $configInstallmentsBannerCartBottomLogotype
     */
    public function setConfigInstallmentsBannerCartBottomLogotype($configInstallmentsBannerCartBottomLogotype)
    {
        $this->configInstallmentsBannerCartBottomLogotype = $configInstallmentsBannerCartBottomLogotype;
    }


    /**
     * @return string
     */
    public function getConfigInstallmentsBannerCartBottomTextcolor()
    {
        return $this->configInstallmentsBannerCartBottomTextcolor;
    }


    /**
     * @param string $configInstallmentsBannerCartBottomTextcolor
     */
    public function setConfigInstallmentsBannerCartBottomTextcolor($configInstallmentsBannerCartBottomTextcolor)
    {
        $this->configInstallmentsBannerCartBottomTextcolor = $configInstallmentsBannerCartBottomTextcolor;
    }


    /**
     * @return string
     */
    public function getConfigInstallmentsBannerCartBottomColor()
    {
        return $this->configInstallmentsBannerCartBottomColor;
    }


    /**
     * @param string $configInstallmentsBannerCartBottomColor
     */
    public function setConfigInstallmentsBannerCartBottomColor($configInstallmentsBannerCartBottomColor)
    {
        $this->configInstallmentsBannerCartBottomColor = $configInstallmentsBannerCartBottomColor;
    }


    /**
     * @return string
     */
    public function getConfigInstallmentsBannerCartBottomRatio()
    {
        return $this->configInstallmentsBannerCartBottomRatio;
    }


    /**
     * @param string $configInstallmentsBannerCartBottomRatio
     */
    public function setConfigInstallmentsBannerCartBottomRatio($configInstallmentsBannerCartBottomRatio)
    {
        $this->configInstallmentsBannerCartBottomRatio = $configInstallmentsBannerCartBottomRatio;
    }

    /**
     * @return array|bool|mixed|string|null
     */
    public function getConfigInstallmentsBannerProductLayout()
    {
        return $this->configInstallmentsBannerProductLayout;
    }


    /**
     * @param array|bool|mixed|string|null $configInstallmentsBannerProductLayout
     */
    public function setConfigInstallmentsBannerProductLayout($configInstallmentsBannerProductLayout)
    {
        $this->configInstallmentsBannerProductLayout = $configInstallmentsBannerProductLayout;
    }


    /**
     * @return array|bool|mixed|string|null
     */
    public function getConfigInstallmentsBannerProductLogotype()
    {
        return $this->configInstallmentsBannerProductLogotype;
    }


    /**
     * @param array|bool|mixed|string|null $configInstallmentsBannerProductLogotype
     */
    public function setConfigInstallmentsBannerProductLogotype($configInstallmentsBannerProductLogotype)
    {
        $this->configInstallmentsBannerProductLogotype = $configInstallmentsBannerProductLogotype;
    }


    /**
     * @return array|bool|mixed|string|null
     */
    public function getConfigInstallmentsBannerProductTextcolor()
    {
        return $this->configInstallmentsBannerProductTextcolor;
    }


    /**
     * @param array|bool|mixed|string|null $configInstallmentsBannerProductTextcolor
     */
    public function setConfigInstallmentsBannerProductTextcolor($configInstallmentsBannerProductTextcolor)
    {
        $this->configInstallmentsBannerProductTextcolor = $configInstallmentsBannerProductTextcolor;
    }


    /**
     * @return array|bool|mixed|string|null
     */
    public function getConfigInstallmentsBannerProductColor()
    {
        return $this->configInstallmentsBannerProductColor;
    }


    /**
     * @param array|bool|mixed|string|null $configInstallmentsBannerProductColor
     */
    public function setConfigInstallmentsBannerProductColor($configInstallmentsBannerProductColor)
    {
        $this->configInstallmentsBannerProductColor = $configInstallmentsBannerProductColor;
    }


    /**
     * @return array|bool|mixed|string|null
     */
    public function getConfigInstallmentsBannerProductRatio()
    {
        return $this->configInstallmentsBannerProductRatio;
    }


    /**
     * @param array|bool|mixed|string|null $configInstallmentsBannerProductRatio
     */
    public function setConfigInstallmentsBannerProductRatio($configInstallmentsBannerProductRatio)
    {
        $this->configInstallmentsBannerProductRatio = $configInstallmentsBannerProductRatio;
    }

}
