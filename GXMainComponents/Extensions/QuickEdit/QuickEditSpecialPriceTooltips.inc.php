<?php

/* --------------------------------------------------------------
   QuickEditSpecialPriceTooltips.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditSpecialPriceTooltips
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditSpecialPriceTooltips implements QuickEditSpecialPriceTooltipsInterface
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
     * QuickEditSpecialPriceTooltips constructor.
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
     * This method is not currently used. Can be removed.
     *
     * @param QuickEditProductPropertiesListItem $data QuickEdit properties collection
     *
     * @return array Returns an empty array
     */
    public function getRowTooltips(QuickEditProductPropertiesListItem $data)
    {
        // @todo This method is not currently used. Can be removed.
        return [];
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
}