<?php
/*--------------------------------------------------------------------
 NoActiveStyleEdit3StyleExceptions.php 2020-3-5
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class NoActiveStyleEdit3StyleExceptions
 */
class NoActiveStyleEdit3StyleExceptions extends Exception
{
    /**
     * @param string $theme
     *
     * @return static
     */
    public static function forTheme(string $theme): self
    {
        return new self('No active Style found for ' . $theme);
    }
}