<?php

/* --------------------------------------------------------------
   UnfinishedBuildException.inc.php 2019-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UnfinishedBuildException
 *
 * This class represents an error for unfinished builds when using entity builders
 * This exception is meant to be thrown if a user wants finish the build process before having all properties set
 *
 * @category   System
 * @package    Content
 */
class UnfinishedBuildException extends Exception
{
}