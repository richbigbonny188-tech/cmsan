<?php
/*--------------------------------------------------------------
   Search.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Filter;

/**
 * Interface Search
 *
 * @package Gambio\Core\Filter
 */
interface Search
{
    /**
     * @return string
     */
    public function keyword(): string;
}