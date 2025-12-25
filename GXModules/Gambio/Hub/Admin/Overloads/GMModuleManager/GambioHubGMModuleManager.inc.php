<?php

/* --------------------------------------------------------------
   GambioHubGMModuleManager.inc.php 2017-02-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubGMModuleManager
 * 
 * Handle "gambio_hub" display in old payment methods page. 
 */
class GambioHubGMModuleManager extends GambioHubGMModuleManager_parent
{
	/**
	 * Moves "gambio_hub" entry into its own "ghost" group. 
	 * 
	 * Groups marked as "ghosts" won't be displayed unless a "showghosts" GET parameter is provided. 
	 * 
	 * @param array $structure Contains the payment modules structure. 
	 */
	public function draw_output($structure)
	{
		foreach($structure as &$entry)
		{
			if(!is_array($entry))
			{
				continue;
			}
			
			$index = array_search('gambio_hub', $entry['MODULES']);
			
			if ($index !== false) {
				unset($entry['MODULES'][$index]); 
				
				$structure[] = array(
					'TITLE' => 'Gambio Hub',
					'MODULES' => array(
						'gambio_hub'
					),
					'GHOST' => true
				);
				
				break; 
			}
		}
		
		parent::draw_output($structure);
	}
}