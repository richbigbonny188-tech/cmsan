<?php
/* --------------------------------------------------------------
  HttpContextReaderServerData.inc.php 2017-02-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class HttpContextReaderServerData extends HttpContextReaderServerData_parent
{
    /**
     * Returns an array which represents the global $_SERVER variable of the current http context.
     *
     * @param HttpContextInterface $httpContext Object which holds information about the current http context.
     *
     * @return array Array which hold information equal to the global $_SERVER variable in an object oriented layer.
     */
    public function getServerData(HttpContextInterface $httpContext)
    {
        return $httpContext->getServerArray();
    }
}