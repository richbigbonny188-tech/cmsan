<?php
/*--------------------------------------------------------------------
 gm_pdf_is_installed.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
	
	function gm_pdf_is_installed() {
		
		if(file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php')) {

			return true;	
		
		} else {

			return false;
		}
		
	}