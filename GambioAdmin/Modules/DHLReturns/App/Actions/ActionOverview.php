<?php
/* --------------------------------------------------------------
   ActionOverview.php 2021-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\App\Actions;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Modules\DHLReturns\App\Data\CountriesFacade;
use Gambio\Admin\Modules\DHLReturns\Services\DHLConfigurationService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

class ActionOverview extends VuePageAction // AdminModuleAction
{
    /**
     * @var DHLConfigurationService
     */
    private $configurationService;
    /**
     * @var CountriesFacade
     */
    private $countriesFacade;
    
    
    public function __construct(DHLConfigurationService $configurationService, CountriesFacade $countriesFacade)
    {
        $this->configurationService = $configurationService;
        $this->countriesFacade      = $countriesFacade;
    }
    
    
    public function handle(Request $request, Response $response): Response
    {
        $this->addPhrases();
        
        $orderId = $request->getAttribute('orderid') ? : 'undefined';
        
        $title        = $this->translate('title', 'dhlreturns');
        $templateData = [
            'dhlreturns' => json_encode([
                                            'countries_json'     => $this->buildDhlReturnsCountriesList(),
                                            'all_countries_json' => $this->buildAllCountriesList(),
                                            'order_id'           => $orderId,
            
                                        ]),
        ];
        $renderedPage = $this->render($title,
                                      dirname(__DIR__, 2) . '/ui/templates/dhlreturns.html',
                                      $templateData);
        
        return $response->write($renderedPage);
    }
    
    
    private function addPhrases(): void
    {
        $phrases = [
            'title',
            'name1',
            'name2',
            'name3',
            'street_name',
            'house_number',
            'postcode',
            'city',
            'country',
            'customer_reference',
            'shipment_reference',
            'email',
            'telephone_number',
            'weight_in_grams',
            'value',
            'customs_currency',
            'customs_original_shipment_number',
            'customs_original_operator',
            'customs_acommpanying_document',
            'customs_original_invoice_number',
            'customs_original_invoice_date',
            'customs_comment',
            'customspos_description',
            'customspos_count',
            'customspos_weight',
            'customspos_values',
            'customspos_origin_country',
            'customspos_article_reference',
            'customspos_tarif_number',
            'make_label',
            'shipment_number',
            'label_creation_date',
            'download',
            'send_by_email',
            'returns_sender',
            'customs_declaration',
            'country_unknown',
        ];
        
        foreach ($phrases as $phrase) {
            $this->addVuePageTranslation($phrase, 'dhlreturns');
        }
    }
    
    
    private function buildDhlReturnsCountriesList(): array
    {
        $countries = [
            ['iso3' => 'BEL', 'iso2' => 'BE', 'name' => $this->translate('BE', 'countries'),],
            ['iso3' => 'BGR', 'iso2' => 'BG', 'name' => $this->translate('BG', 'countries'),],
            ['iso3' => 'DNK', 'iso2' => 'DK', 'name' => $this->translate('DK', 'countries'),],
            ['iso3' => 'DEU', 'iso2' => 'DE', 'name' => $this->translate('DE', 'countries'),],
            ['iso3' => 'EST', 'iso2' => 'EE', 'name' => $this->translate('EE', 'countries'),],
            ['iso3' => 'FIN', 'iso2' => 'FI', 'name' => $this->translate('FI', 'countries'),],
            ['iso3' => 'FRA', 'iso2' => 'FR', 'name' => $this->translate('FR', 'countries'),],
            ['iso3' => 'GRC', 'iso2' => 'GR', 'name' => $this->translate('GR', 'countries'),],
            ['iso3' => 'GBR', 'iso2' => 'GB', 'name' => $this->translate('GB', 'countries'),],
            ['iso3' => 'IRL', 'iso2' => 'IE', 'name' => $this->translate('IE', 'countries'),],
            ['iso3' => 'ITA', 'iso2' => 'IT', 'name' => $this->translate('IT', 'countries'),],
            ['iso3' => 'HRV', 'iso2' => 'HR', 'name' => $this->translate('HR', 'countries'),],
            ['iso3' => 'LVA', 'iso2' => 'LV', 'name' => $this->translate('LV', 'countries'),],
            ['iso3' => 'LTU', 'iso2' => 'LT', 'name' => $this->translate('LT', 'countries'),],
            ['iso3' => 'LUX', 'iso2' => 'LU', 'name' => $this->translate('LU', 'countries'),],
            ['iso3' => 'MLT', 'iso2' => 'MT', 'name' => $this->translate('MT', 'countries'),],
            ['iso3' => 'NLD', 'iso2' => 'NL', 'name' => $this->translate('NL', 'countries'),],
            ['iso3' => 'AUT', 'iso2' => 'AT', 'name' => $this->translate('AT', 'countries'),],
            ['iso3' => 'POL', 'iso2' => 'PL', 'name' => $this->translate('PL', 'countries'),],
            ['iso3' => 'PRT', 'iso2' => 'PT', 'name' => $this->translate('PT', 'countries'),],
            ['iso3' => 'ROU', 'iso2' => 'RO', 'name' => $this->translate('RO', 'countries'),],
            ['iso3' => 'SWE', 'iso2' => 'SE', 'name' => $this->translate('SE', 'countries'),],
            ['iso3' => 'CHE', 'iso2' => 'CH', 'name' => $this->translate('CH', 'countries'),],
            ['iso3' => 'SVK', 'iso2' => 'SK', 'name' => $this->translate('SK', 'countries'),],
            ['iso3' => 'SVN', 'iso2' => 'SI', 'name' => $this->translate('SI', 'countries'),],
            ['iso3' => 'ESP', 'iso2' => 'ES', 'name' => $this->translate('ES', 'countries'),],
            ['iso3' => 'CZE', 'iso2' => 'CZ', 'name' => $this->translate('CZ', 'countries'),],
            ['iso3' => 'HUN', 'iso2' => 'HU', 'name' => $this->translate('HU', 'countries'),],
            ['iso3' => 'CYP', 'iso2' => 'CY', 'name' => $this->translate('CY', 'countries'),],
        ];
        $countries = $this->sortCountries($countries);
        
        return $countries;
    }
    
    
    private function buildAllCountriesList(): array
    {
        $countries = $this->countriesFacade->getAllCountries();
        foreach ($countries as &$country) {
            $country['name'] = $this->translate($country['iso2'], 'countries');
        }
        unset($country);
        $countries = $this->sortCountries($countries);
        
        return $countries;
    }
    
    
    private function sortCountries(array $countries): array
    {
        usort($countries,
            static function ($a, $b) {
                $sortNormalization = [
                    'ä' => 'a',
                    'ö' => 'o',
                    'ü' => 'u',
                    'Ä' => 'A',
                    'Ö' => 'O',
                    'Ü' => 'U',
                    'ß' => 'ss'
                ];
                $aNorm             = strtr($a['name'], $sortNormalization);
                $bNorm             = strtr($b['name'], $sortNormalization);
                
                return $aNorm <=> $bNorm;
            });
        
        return $countries;
    }
    
    
    protected function jsEntrypoint(): string
    {
        return 'd_h_l_returns';
    }
}

