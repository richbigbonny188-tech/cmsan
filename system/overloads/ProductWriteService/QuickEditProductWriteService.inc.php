<?php

/* --------------------------------------------------------------
   QuickEditProductWriteService.inc.php 2018-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductWriteService
 */
class QuickEditProductWriteService extends QuickEditProductWriteService_parent
{
	/**
	 * Stores the changes of the product.
	 *
	 * @param int $productId Id of the product that should be updated.
	 * @param array $changes An array containing the changes of the product.
	 *
	 * @return bool Returns true after the data has been successfully written - otherwise, false.
	 */
	public function updateProductByClause($productId, array $changes)
	{
		return $this->productRepo->updateProductByClause($productId, $changes);
	}
}