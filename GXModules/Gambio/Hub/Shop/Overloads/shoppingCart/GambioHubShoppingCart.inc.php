<?php
/* --------------------------------------------------------------
   GambioHubShoppingCart.inc.php 2017-02-22
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubShoppingCart
 */
class GambioHubShoppingCart extends GambioHubShoppingCart_parent
{
    /**
     * When a new cart id is generated, the hub session key is deleted from the session.
     *
     * @param int $length
     *
     * @return mixed
     */
    public function generate_cart_id($length = 5)
    {
        unset($_SESSION['gambio_hub_session_key']);

        return parent::generate_cart_id($length);
    }
    
}