<?php
/* --------------------------------------------------------------
   AfterbuyXmlRequest.php 2022-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\Model;

/**
 * Interface AfterbuyXmlRequest
 *
 * @package GXModules\Gambio\Afterbuy\AfterbuyCommon\Model
 */
interface AfterbuyXmlRequest
{
    /**
     * XML serialization.
     *
     * @return string
     */
    public function toXmlString(): string;
}