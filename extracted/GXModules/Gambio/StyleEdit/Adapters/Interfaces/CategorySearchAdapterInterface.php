<?php
/*--------------------------------------------------------------------------------------------------
    CategorySearchAdapterInterface.php 2020-05-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace GXModules\Gambio\StyleEdit\Adapters\Interfaces;


use Gambio\StyleEdit\Core\Components\BasicController;

interface CategorySearchAdapterInterface
{
    public function searchByTerm(string $term);

}