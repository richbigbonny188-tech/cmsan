<?php
/* --------------------------------------------------------------
   LoggingSessionHandler.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractSessionHandler');

class LoggingSessionHandler extends AbstractSessionHandler
{
    public function __construct()
    {
        $this->loggingEnabled = true;
        parent::__construct();
    }
}
