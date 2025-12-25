<?php
/* --------------------------------------------------------------
   KlarnaOSMFooterThemeContentView.inc.php 2022-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaOSMFooterThemeContentView extends KlarnaOSMFooterThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();

        if (!KlarnaOSMHelper::isModuleInstalledAndActive()) {
            return;
        }
    
        $configuration = MainFactory::create('KlarnaOSMConfigurationStorage');
        $snippetBottom = $configuration->get('snippet_footer_bottom');
        $snippetBottom = $this->prepareKlarnaSnippet($snippetBottom);
        $this->set_content_data('KLARNAOSM_FOOTER_BOTTOM', $snippetBottom);
    }
    
    
    /**
     * @param string $rawSnippet
     *
     * @return string
     */
    protected function prepareKlarnaSnippet($rawSnippet)
    {
        $snippet = KlarnaOSMHelper::setSnippetLocale($rawSnippet);
        $snippet = KlarnaOSMHelper::setSnippetPurchaseAmount($snippet);
        return $snippet;
    }
}
