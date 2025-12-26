<?php
/*--------------------------------------------------------------
   LanguagesApiV2Controller.php 2021-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);


/**
 * Class LanguagesApiV2Controller
 */
class LanguagesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var LanguagesReadService
     */
    protected $service;
    
    
    /**
     * Initializes API Controller
     */
    protected function init(): void
    {
        $this->service = StaticGXCoreLoader::getService('LanguagesRead');
    }
    
    
    public function get()
    {
        $response   = $this->service->getLanguages()->toArray();
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        $this->_sortResponse($response);
        $this->_paginateResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        $this->_writeResponse($response);
    }
}