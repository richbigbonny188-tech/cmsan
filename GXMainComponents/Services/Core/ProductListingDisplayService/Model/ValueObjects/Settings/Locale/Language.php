<?php
/* --------------------------------------------------------------
  Language.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;

/**
 * Class Language
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale
 */
class Language
{
    /**
     * @param int    $id
     * @param string $code
     * @param string $directory
     */
    public function __construct(
        private int    $id,
        private string $code,
        private string $directory
    )
    {
    }


    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }


    /**
     * @return string
     */
    public function directory(): string
    {
        return $this->directory;
    }
}