<?php
/* --------------------------------------------------------------
   GoogleAnalyticsApplicationBottomExtender.inc.php 2019-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleAnalyticsApplicationBottomExtender extends GoogleAnalyticsApplicationBottomExtender_parent
{
    public function proceed()
    {
        if ((bool)gm_get_conf('GM_ANALYTICS_CODE_USE')) {
            $bottomTrackingCode = (string)gm_get_conf('GM_ANALYTICS_CODE');
            $htmlOutput = $bottomTrackingCode;
            if ((bool)gm_get_conf('GM_ANALYTICS_CODE_USE_SMARTY')) {
                $contentView        = MainFactory::create('ContentView');
                $contentView->set_content_template_from_string($bottomTrackingCode, false);
                $contentView->set_flat_assigns(true);
                $content = [];
                foreach ($content as $contentKey => $contentValue) {
                    $contentView->set_content_data($contentKey, $contentValue);
                }
                try {
                    $htmlOutput = $contentView->get_html();
                } catch (Exception $e) {
                    gm_set_conf('GM_ANALYTICS_CODE_USE_SMARTY', false);
                }
            }
            $this->v_output_buffer['GOOGLE_ANALYTICS_CODE'] = $htmlOutput;
        }
        
        parent::proceed();
    }
}
