<?php

/* --------------------------------------------------------------
   ZonesController.inc.php 2017-03-28
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StateController
 */
class ZonesController extends HttpViewController
{
    /**
     * @var \CountryService
     */
    protected $countryService;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        $this->db             = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->countryService = StaticGXCoreLoader::getService('Country');
    }
    
    
    /**
     * The JSEngine Component sets the federal state dropdown depending on the country. This
     * method provides the countries required for this purpose.
     *
     * If the display for the state is deactivated, the dropdown must still be displayed
     * for some countries, otherwise the address is incomplete. At the moment, this applies to the following
     * countries (iso2, countries id, countries name):
     *
     * AR  10 Argentina
     * BR  30 Brazil
     * CA  38 Canada
     * CN  44 China
     * ID 100 Indonesia
     * IN  99 India
     * JP 107 Japan
     * MX 138 Mexico
     * TH 209 Thailand
     * US 223 United States of America
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        $countryId = ($this->_getPostData('country')
                      !== null) ? new IdType($this->_getPostData('country')) : new IdType($this->_getPostData('entry_country_id'));
        
        $customerCountryZones = $this->countryService->findCountryZonesByCountryId($countryId);
        $isStateMandatory     = $this->countryService->isStateMandatory($countryId);
        
        if ($countryId !== null && !empty($customerCountryZones) && (ACCOUNT_STATE == 'true' || $isStateMandatory)) {
            $zones = [];
            
            foreach ($customerCountryZones as $customerCountryZone) {
                $zones[] = [
                    'id'       => (string)$customerCountryZone->getId(),
                    'name'     => (string)$customerCountryZone->getName(),
                    'selected' => $this->_getPostData('selected_zone_id') === (string)$customerCountryZone->getId()
                ];
            }
            
            return new JsonHttpControllerResponse(['success' => true, 'data' => $zones]);
        }
        
        return new JsonHttpControllerResponse(['success' => false]);
    }
    
    
    public function actionOrderAddressEdit()
    {
        $selectors              = (null !== $this->_getPostData('selectors')) ? $this->_getPostData('selectors') : '';
        $country                = $this->countryService->getCountryByName($this->_getPostData($selectors['country']));
        $countryHasCountryZones = $this->countryService->countryHasCountryZones($country);
        $selectedState          = '';
        
        if (null !== $this->_getPostData($selectors['selected'])) {
            $selectedState = $this->_getPostData($selectors['selected']);
        }
        
        if ($countryHasCountryZones) {
            $countryZones = $this->countryService->findCountryZonesByCountryId(new IdType($country->getId()));
            
            if (!$this->countryService->isStateMandatory(new IdType($country->getId()))) {
                $zones[] = [
                    'id'       => '',
                    'name'     => '',
                    'selected' => $this->_getPostData('selected_zone_name') === 0
                ];
            }
            
            foreach ($countryZones as $countryZone) {
                $zones[] = [
                    'id'       => (string)$countryZone->getName(),
                    'name'     => (string)$countryZone->getName(),
                    'selected' => $selectedState === (string)$countryZone->getName()
                ];
            }
            
            return new JsonHttpControllerResponse([
                                                      'success'  => true,
                                                      'data'     => $zones,
                                                      'selector' => $selectors['country']
                                                  ]);
        }
        
        return new JsonHttpControllerResponse(['success' => false, 'selector' => $selectors['country']]);
    }
}