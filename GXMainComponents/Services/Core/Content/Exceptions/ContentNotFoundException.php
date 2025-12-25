<?php
/* --------------------------------------------------------------
  ContentNotFoundException.php 2019-07-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ContentNotFoundException
 */
class ContentNotFoundException extends Exception
{
    /**
     * ContentNotFoundException constructor.
     *
     * @param                $id
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $id, $code = 0, Throwable $previous = null)
    {
        $message = "No content was found with the id (".$id.")";
        
        parent::__construct($message, $code, $previous);
    }
}