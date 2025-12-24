<?php
/*--------------------------------------------------------------------
 PropertiesViewAdmin.inc.php 2020-2-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

MainFactory::load_class('PropertiesView');

/**
 * Class PropertiesViewAdmin
 */
class PropertiesViewAdmin extends PropertiesView
{
    protected const PROPERTY_DROPDOWN_TEMPLATE_FILE = 'product_info_property_template_dropdowns.html';
    
    /**
     * @inheritDoc
     */
    protected function getFullTemplatePath(string $templateFile): string
    {
        if ($templateFile !== self::PROPERTY_DROPDOWN_TEMPLATE_FILE) {
            
            return parent::getFullTemplatePath($templateFile);
        }
        
        return DIR_FS_CATALOG . 'admin/html/content/order_property_dropdowns.html';
    }
}