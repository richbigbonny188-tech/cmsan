<?php
/* --------------------------------------------------------------
   QuickEditOverviewTooltips.inc.php 2017-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditOverviewTooltips
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditOverviewTooltips implements QuickEditOverviewTooltipsInterface
{
    /**
     * @var ContentView
     */
    protected $contentView;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * QuickEditTooltips constructor.
     */
    public function __construct()
    {
        $this->db          = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->contentView = MainFactory::create('ContentView');
        $this->contentView->set_escape_html(true);
        $this->contentView->set_flat_assigns(true);
        $this->contentView->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/quick_edit/tooltips/');
    }
    
    
    /**
     * Returns the data of a product for the tooltip.
     *
     * @param QuickEditProductListItem $data QuickEdit product collection.
     *
     * @return array Returns the data of a product for the tooltip.
     */
    public function getRowTooltips(QuickEditProductListItem $data)
    {
        return [
            'special_price' => $this->_getSpecials($data),
        ];
    }
    
    
    /**
     * Returns the view for the tooltip.
     *
     * @param string $templateFile Template file.
     * @param array  $contentArray Content Array.
     *
     * @return string Returns the view for the tooltip.
     */
    protected function _render($templateFile, array $contentArray)
    {
        $this->contentView->set_content_template($templateFile);
        
        foreach ($contentArray as $contentItemKey => $contentItemValue) {
            $this->contentView->set_content_data($contentItemKey, $contentItemValue);
        }
        
        return $this->contentView->get_html();
    }
    
    
    /**
     * Provides the data for a product as a special offer.
     *
     * @param QuickEditProductListItem $data QuickEdit product collection.
     *
     * @return string Returns the data for a product as a special offer.
     */
    protected function _getSpecials(QuickEditProductListItem $data)
    {
        $expiresDate    = $data->getSpecialPriceExpiresDate();
        $dividedByPrice = (double)$data->getPrice();
        
        if ($dividedByPrice === 0.0) {
            $dividedByPrice = 1;
        }
        
        $templateData['specials'] = [
            'products_id'                => $data->getId(),
            'products_price'             => $data->getPrice(),
            'special_price_id'           => $data->getSpecialPriceId(),
            'special_price_percentage'   => round(abs(($data->getPrice() - $data->getSpecialPrice()) / $dividedByPrice
                                                      * 100),
                                                  2),
            'special_price'              => $data->getSpecialPriceId() !== 0 ? $data->getSpecialPrice() : false,
            'special_price_expires_date' => $expiresDate !== '01.01.1970'
                                            && $expiresDate !== '01.01.1000' ? $expiresDate : '-',
            'special_price_quantity'     => $data->getSpecialPriceQuantity(),
            'special_price_status'       => $data->getSpecialPriceStatus() ? 'Aktiv' : 'Inaktiv',
        ];
        
        return $this->_render('special_price.html', $templateData);
    }
}