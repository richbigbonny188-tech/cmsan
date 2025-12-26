<?php
/* --------------------------------------------------------------
   KlarnaOSMCartThemeContentView.inc.php 2022-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaOSMCartThemeContentView extends KlarnaOSMCartThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        if ($this->cartCountContents <= 0) {
            return;
        }
    
        if (!KlarnaOSMHelper::isModuleInstalledAndActive()) {
            return;
        }
    
        $configuration = MainFactory::create('KlarnaOSMConfigurationStorage');
        $snippetCart = $this->prepareKlarnaSnippet($configuration->get('snippet_cart'));
        $snippetCartTop = $this->prepareKlarnaSnippet($configuration->get('snippet_cart_top'));
        
        $this->set_content_data('KLARNAOSM_CART', $snippetCart);
        $this->set_content_data('KLARNAOSM_CART_TOP', $snippetCartTop);
    }
    
    
    /**
     * @param string $rawSnippet
     *
     * @return string
     */
    private function prepareKlarnaSnippet($rawSnippet)
    {
        $snippet = KlarnaOSMHelper::setSnippetLocale($rawSnippet);
        $snippet = KlarnaOSMHelper::setSnippetPurchaseAmount($snippet);
        
        return $snippet;
    }
    
    
}