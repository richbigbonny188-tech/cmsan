<?php
/* --------------------------------------------------------------
   WishListContentViewInterface.inc.php 2020-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WishListContentViewInterface
 */
interface WishListContentViewInterface extends ContentViewInterface
{
    public function getProductMessages();
    public function setProductMessages(array $productErrorMessages);
}
