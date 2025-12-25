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

class ProductStockService implements ProductStockServiceInterface
{
    const STOCK_CHECK = 'STOCK_CHECK';
    
    protected $constant_reader;
    
    
    protected function valueOf($constant, $defaultValue = null)
    {
        return $this->constant_reader->valueOf($constant, $defaultValue);
    }
    
    
    public function __construct(ConstantReader $constant_reader = null)
    {
        $this->constant_reader = $constant_reader ? : new ConstantReader();
    }
    
    
    /**
     * The method identify if and product stock should be changed with it has the given configuration
     *
     * @param int  $combinationQtdType The product combination quantity configuration
     * @param bool $isCombination      is a combination?
     *
     * @return boolean
     */
    public function isCombinationConfigChangeProductStock($combinationQtdType, $isCombination)
    {
        $combinationQtdType = (int)$combinationQtdType;
        //if the product has configuration to change using the COMBIS_QTD_COMBI_STOCK but has no COMBIS_QUANTITY ID
        if (!$isCombination && $combinationQtdType === PropertiesCombisAdminControl::COMBI_STOCK) {
            $combinationQtdType = PropertiesCombisAdminControl::DEFAULT_GLOBAL;
        }
        
        switch ($combinationQtdType) {
            case PropertiesCombisAdminControl::DEFAULT_GLOBAL:
                //stock check is enabled and (its not a combination or attribute check is disabled)
                $result = $this->valueOf(self::STOCK_CHECK) == 'true'
                          && (!$isCombination || $this->valueOf('ATTRIBUTE_STOCK_CHECK') == 'false');
                break;
            
            case PropertiesCombisAdminControl::PRODUCT_STOCK:
                $result = $this->valueOf(self::STOCK_CHECK) == 'true';
                break;
            
            case PropertiesCombisAdminControl::COMBI_STOCK:
                $result = false;
                break;
            
            case PropertiesCombisAdminControl::NO_CHECK:
                $result = false;
                break;
            //if empty the use global configuration
            default:
                $result = $this->valueOf(self::STOCK_CHECK) == 'true';
                break;
        }
        
        return $result;
    }
    
    
    /**
     * The method identify if the stock should be changed with the given parameters
     *
     * @param int    $combinationConfigurationType The combination configuration of the current product
     * @param int    $combinationId                the combination if relative to the current product
     * @param string $attributeFilename            The filename attribute related to the product
     *
     * @return boolean
     */
    public function isChangeProductStock($combinationConfigurationType, $combinationId, $attributeFilename)
    {
        $hasFilename = !empty($attributeFilename);
        //if it's a downloadable file
        if ($hasFilename) {
            return $this->valueOf('DOWNLOAD_STOCK_CHECK', 'false') == 'true';
        } else {
            $isCombination = !empty((int)$combinationId);
            
            return $this->isCombinationConfigChangeProductStock((int)$combinationConfigurationType, $isCombination);
        }
    }
    
}