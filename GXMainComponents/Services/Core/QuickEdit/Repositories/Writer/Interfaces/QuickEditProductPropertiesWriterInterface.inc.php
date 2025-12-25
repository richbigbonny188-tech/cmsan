<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesRepositoryWriterInterface.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditProductPropertiesWriterInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditProductPropertiesWriterInterface
{
    /**
     * Saves product by product-combi ID.
     *
     * @param array $productCombi Contains product data to be saved.
     *
     * @return bool Returns the operation result.
     */
    public function setByCombisId(array $productCombi);
}