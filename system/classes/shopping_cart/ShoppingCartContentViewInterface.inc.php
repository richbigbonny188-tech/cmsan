<?php
/* --------------------------------------------------------------
   ShoppingCartContentViewInterface.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

//ask if there is some reason for emty interface (Mirko)

interface ShoppingCartContentViewInterface extends ContentViewInterface
{
    public function setCustomerStatusMinOrder($p_customerStatusMinOrder);


    public function setLanguagesId($p_languageId);


    public function setLanguageCode($p_languageCode);


    public function setCart($p_cart);


    public function setCartCountContents($p_countContents);


    public function setCustomerStatusMaxOrder($p_customerStatusMaxOrder);


    public function prepare_data();


    public function getOrderDetailsCartContentView();


    public function setXtcPrice(xtcPrice $p_xtPrice);


    public function setProducts(array $products);


    public function setProductMessages(array $messages);


    public function getProductMessages();


    public function set_shopping_cart_button_template();


    public function set_shopping_cart_messages_template();


}
