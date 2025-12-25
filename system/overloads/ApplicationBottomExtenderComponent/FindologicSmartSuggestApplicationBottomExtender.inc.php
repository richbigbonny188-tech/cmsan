<?php
/* --------------------------------------------------------------
   FindologicSmartSuggestApplicationBottomExtender.inc.php 2018-01-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FindologicSmartSuggestApplicationBottomExtender extends FindologicSmartSuggestApplicationBottomExtender_parent
{
	function proceed()
	{
		$snippet = (string)gm_get_conf('FL_SMARTSUGGEST_SNIPPET');
		if(!empty($snippet))
		{
			$this->v_output_buffer['FL_SMARTSUGGEST_SNIPPET'] = $snippet;
		}
		
		parent::proceed();
	}
	
}
