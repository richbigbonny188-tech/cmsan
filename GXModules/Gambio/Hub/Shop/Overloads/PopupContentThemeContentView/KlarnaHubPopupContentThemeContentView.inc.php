<?php
/* --------------------------------------------------------------
   KlarnaHubPopupContentThemeContentView.inc.php 2018-04-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaHubPopupContentThemeContentView extends KlarnaHubPopupContentThemeContentView_parent
{
	protected function get_data()
	{
        parent::get_data();
        
        if (isset($this->content_group_id) && $this->content_group_id == 3889891) {
            $contentBody = $this->content_data_array['content_text'];
            
            if (empty($contentBody)) {
                return;
            }
            
            $klarnaHubTermsAndConditions = MainFactory::create('KlarnaHubTermsAndConditions', $contentBody);
			
			$this->content_data_array['content_text'] = $klarnaHubTermsAndConditions->getContent();
		}
	}
}
