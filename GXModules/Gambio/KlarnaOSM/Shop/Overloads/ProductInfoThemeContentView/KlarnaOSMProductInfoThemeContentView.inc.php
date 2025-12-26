<?php
/* --------------------------------------------------------------
   KlarnaOSMProductInfoThemeContentView.inc.php 2022-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Shop\ProductModifiers\Groups\Collections\GroupCollection;
use Gambio\Shop\SellingUnit\Unit\SellingUnit;

class KlarnaOSMProductInfoThemeContentView extends KlarnaOSMProductInfoThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        
        if (!$this->_productIsForSale()) {
            return;
        }
        
        if (!KlarnaOSMHelper::isModuleInstalledAndActive()) {
            return;
        }
        
        $configuration = MainFactory::create('KlarnaOSMConfigurationStorage');
        $snippetTop    = $configuration->get('snippet_product_top');
        $snippetTop    = $this->prepareKlarnaSnippet($snippetTop);
        $this->set_content_data('KLARNAOSM_PRODUCT_TOP', $snippetTop);
        $snippetBottom = $configuration->get('snippet_product_bottom');
        $snippetBottom = $this->prepareKlarnaSnippet($snippetBottom);
        $this->set_content_data('KLARNAOSM_PRODUCT_BOTTOM', $snippetBottom);
    }
    
    
    /**
     * @param string $rawSnippet
     *
     * @return string
     * @throws \Gambio\Shop\SellingUnit\Unit\Builders\Exceptions\UnfinishedBuildException
     */
    private function prepareKlarnaSnippet($rawSnippet)
    {
        $hasAttributes = trim(isset($this->content_array['MODULE_product_options']) ? $this->content_array['MODULE_product_options'] : '')
                         !== '';
        
        $snippet = KlarnaOSMHelper::setSnippetLocale($rawSnippet);
        
        include DIR_FS_CATALOG . 'release_info.php';
        if (version_compare(preg_replace('/(v?)(\d+\.\d+\.\d+\.\d+)(.*)/', '$2', $gx_version), '4.1.3.0', '>=')) {
            /** @var SellingUnit $sellingUnit */
            $sellingUnit    = $this->selling_unit;
            $purchaseAmount = $sellingUnit->price()->pricePlain()->value();
            $snippet        = KlarnaOSMHelper::setSnippetPurchaseAmount($snippet, $purchaseAmount);
        } elseif (strpos($snippet, 'data-purchase-amount=""') !== false && ($this->hasProperties || $hasAttributes)) {
            // sorry, can’t use banners with purchase amount in pre-4.1.3 shops
            return '';
        } else {
            $purchaseAmount = $this->productPriceArray['plain'];
            $snippet        = KlarnaOSMHelper::setSnippetPurchaseAmount($snippet, $purchaseAmount);
        }
        
        return $snippet;
    }
}
