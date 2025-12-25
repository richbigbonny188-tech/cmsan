<?php
/* --------------------------------------------------------------
   xtc_output_warning.inc.php 2016-03-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_href_link.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_output_warning.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
   
  function xtc_output_warning($warning) {
	echo '<div style="padding: 20px;margin: 10px auto;max-width: 1170px;text-align: center;color: #fff;background-color: #c20400;">' . $warning . '</div>';
  }
