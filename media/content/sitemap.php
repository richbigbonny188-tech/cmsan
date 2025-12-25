<?php
/* -----------------------------------------------------------------------------------------
   sitemap.php 2021-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com
   (c) 2003	 nextcommerce; www.nextcommerce.org
   (c) 2004 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: sitemap.php 1278 2005-10-02 07:40:25Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once DIR_FS_CATALOG . 'gm/classes/GMSitemap.php';

$sitemap = MainFactory::create('GMSitemap');
$gm_tree = $sitemap->get();

if (!empty($gm_tree)) {
    $sitemapContentView = MainFactory::create('ContentView');
    $sitemapContentView->set_content_template('sitemap.html');
    $sitemapContentView->set_flat_assigns(true);
    $sitemapContentView->set_content_data('module_content', $gm_tree);
    
    echo $sitemapContentView->get_html();
}
