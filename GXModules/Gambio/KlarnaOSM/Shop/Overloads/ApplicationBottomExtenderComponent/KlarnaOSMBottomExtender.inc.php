<?php
/* --------------------------------------------------------------
   KlarnaOSMBottomExtender.inc.php 2022-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaOSMBottomExtender extends KlarnaOSMBottomExtender_parent
{
    /**
     * Includes Javascript library snippet.
     * 
     * @return void
     */
    public function proceed()
    {
        parent::proceed();
        
        if (!KlarnaOSMHelper::isModuleInstalledAndActive())
        {
            return;
        }

        $configuration = MainFactory::create('KlarnaOSMConfigurationStorage');
        $snippet   = $configuration->get('snippet_library');
        $purposeId = $configuration->get('cookie_consent_purpose_id');
        if ($purposeId > 0) {
            $snippet = strtr($snippet, [
                'src'  => 'data-src',
                'type' => 'data-type',
                '<script' => '<script type="as-oil" data-type="application/javascript" data-managed="as-oil" data-purposes="' . $purposeId . '" '
            ]);
        }
        
        $this->v_output_buffer['KLARNAOSM'] = $snippet;
    }
}
