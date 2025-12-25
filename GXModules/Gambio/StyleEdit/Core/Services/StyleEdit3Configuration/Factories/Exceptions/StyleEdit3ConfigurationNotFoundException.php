<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationNotFoundException.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Exceptions;

use FileNotFoundException;

/**
 * Class StyleEdit3ConfigurationNotFoundException
 */
class StyleEdit3ConfigurationNotFoundException extends FileNotFoundException
{
    /**
     * StyleEdit3ConfigurationNotFoundException constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $errorMessage = 'File(' . $path . ') was not found';
        
        parent::__construct($errorMessage);
    }
}