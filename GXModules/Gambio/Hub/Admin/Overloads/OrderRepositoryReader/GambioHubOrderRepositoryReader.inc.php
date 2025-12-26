<?php

/* --------------------------------------------------------------
   GambioHubOrderRepositoryReader.inc.php 2017-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubOrderRepositoryReader
 *
 * Enables the output of the payment title and module of a gambio hub order.
 */
class GambioHubOrderRepositoryReader extends GambioHubOrderRepositoryReader_parent
{
    /**
     * Creates an order instance.
     *
     * @param array $data Order data.
     *
     * @return GXEngineOrder Created order object.
     */
    protected function _createOrderByArray(array $data)
    {
        $alteredData = $data;
        $alteredData['payment_method'] = $alteredData['gambio_hub_module_title'] ?: $alteredData['payment_method'];
        $alteredData['payment_class'] = $alteredData['gambio_hub_module'] ?: $alteredData['payment_class'];

        return parent::_createOrderByArray($alteredData);
    }
}