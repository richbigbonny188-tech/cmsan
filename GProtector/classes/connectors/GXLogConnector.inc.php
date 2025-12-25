<?php
/* --------------------------------------------------------------
  GXLogConnector.inc.php 2019-06-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

class GXLogConnector implements GProtectorLogConnectorInterface
{
	public function log($message, $group, $filename, $severity, $errorType, $messageDetails)
	{
		if(class_exists('LogControl'))
		{
			LogControl::get_instance()
			          ->notice($message, $group, $filename, $severity, $errorType, 0, $messageDetails);
			LogControl::get_instance()->write_stack(array('security'));
			
			return true;
		}
		
		return false;
	}
}

$this->log_connectors_array[] = new GXLogConnector();
