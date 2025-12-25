<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerAdapterInterface.php 2021-02-16
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Adapters\Interfaces;

use InfoElementContent;

/**
 * Interface ContentManagerAdapterInterface
 * @package GXModules\Gambio\StyleEdit\Adapters\Interfaces
 */
interface ContentManagerAdapterInterface
{
    /**
     * @return InfoElementContent[]
     */
    public function getAllContentPages(): array;
}
