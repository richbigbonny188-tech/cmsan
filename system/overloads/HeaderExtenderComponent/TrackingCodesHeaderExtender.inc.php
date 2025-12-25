<?php
/* --------------------------------------------------------------
   TrackingCodesHeaderExtender.inc.php 2019-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class TrackingCodesHeaderExtender extends TrackingCodesHeaderExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        if ((bool)gm_get_conf('GM_HEAD_TRACKING_CODE_USE')) {
            $this->v_output_buffer = is_array($this->v_output_buffer) ? $this->v_output_buffer : [];
            $trackingCode          = (string)gm_get_conf('GM_HEAD_TRACKING_CODE');
            $htmlOutput = $trackingCode;
            if ((bool)gm_get_conf('GM_HEAD_TRACKING_CODE_USE_SMARTY')) {
                $contentView = MainFactory::create('ContentView');
                $contentView->set_content_template_from_string($trackingCode, false);
                $contentView->set_flat_assigns(true);
                $content = [];
                foreach ($content as $contentKey => $contentValue) {
                    $contentView->set_content_data($contentKey, $contentValue);
                }
                try {
                    $htmlOutput = $contentView->get_html();
                } catch (Exception $e) {
                    gm_set_conf('GM_HEAD_TRACKING_CODE_USE_SMARTY', false);
                }
            }
            $this->v_output_buffer['trackingCodes'] = $htmlOutput;
        }
    }
}
