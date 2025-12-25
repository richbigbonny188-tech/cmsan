<?php
/* --------------------------------------------------------------
   ProductStockService.inc.php 2019-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

interface ProductStockServiceInterface
{
    
    /**
     * The method identify if and product stock should be changed with it has the given configuration
     *
     * @param int  $combinationQtdType The product combination quantity configuration
     * @param bool $isCombination      is a combination?
     *
     * @return boolean
     */
    public function isCombinationConfigChangeProductStock($combinationQtdType, $isCombination);
    
    
    /**
     * The method identify if the stock should be changed with the given parameters
     *
     * @param int    $combinationConfigurationType The combination configuration of the current product
     * @param int    $combinationId                the combination if relative to the current product
     * @param string $attributeFilename            The filename attribute related to the product
     *
     * @return boolean
     */
    public function isChangeProductStock($combinationConfigurationType, $combinationId, $attributeFilename);
    
}