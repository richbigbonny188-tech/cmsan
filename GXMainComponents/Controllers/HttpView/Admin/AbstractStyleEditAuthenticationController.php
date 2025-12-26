<?php
/* --------------------------------------------------------------
  AbstractStyleEditAuthenticationController.php 2019-10-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class AbstractStyleEditAuthenticationController
 */
abstract class AbstractStyleEditAuthenticationController extends AdminHttpViewController
{
    
    /**
     *
     */
    protected function redirectToFrontEnd(): void
    {
        $this->redirect('');
    }
    
    
    
    /**
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        $baseUrl = ENABLE_SSL_CATALOG ? HTTP_CATALOG_SERVER : HTTPS_CATALOG_SERVER;
        $baseUrl .= DIR_WS_CATALOG;
    
        header('Location: ' . $baseUrl . $url);
        exit;
    }
}