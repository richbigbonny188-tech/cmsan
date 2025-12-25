<?php
/*--------------------------------------------------------------------------------------------------
    ProductSearchAdapterInterface.php 2020-05-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\StyleEdit\Adapters\Interfaces;


interface ProductSearchAdapterInterface
{
    /**
     * @param string $term
     *
     * @return mixed
     */
    public function searchByTerm(string $term);

    /**
     * @param array $idCollection
     *
     * @return mixed
     */
    public function searchByIds(array $idCollection);

}