<?php
/* --------------------------------------------------------------
  UrlRewriteNotFoundException.php 2019-07-31
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class UrlRewriteNotFoundException
 */
class UrlRewriteNotFoundException extends Exception
{
    /**
     * UrlRewriteNotFoundException constructor.
     *
     * @param                $id
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($id, $code = 0, Throwable $previous = null)
    {
        $message = "No url rewrite was found with the content id ($id)";
        
        parent::__construct($message, $code, $previous);
    }
}