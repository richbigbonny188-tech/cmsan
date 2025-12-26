<?php
/* --------------------------------------------------------------
  HttpContextServerData.inc.php 2017-02-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class HttpContextServerData extends HttpContextServerData_parent
{
    /**
     * Returns an array which is equal to the global $_SERVER variable in an object oriented layer.
     *
     * @return array Array which is equal to $_SERVER.
     */
    public function getServerArray()
    {
        return $this->serverArray;
    }
}