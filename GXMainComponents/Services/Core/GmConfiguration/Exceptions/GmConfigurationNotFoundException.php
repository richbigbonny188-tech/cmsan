<?php
/* --------------------------------------------------------------
  GmConfigurationNotFoundException.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class GmConfigurationNotFoundException
 */
class GmConfigurationNotFoundException extends Exception
{
    /**
     * GmConfigurationNotFoundException constructor.
     *
     * @param string         $key
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $key = '', int $code = 0, Throwable $previous = null)
    {
        $message = "No configuration was found with the key ($key)";
        
        parent::__construct($message, $code, $previous);
    }
}