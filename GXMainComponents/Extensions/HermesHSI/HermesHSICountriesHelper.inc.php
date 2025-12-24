<?php
/* --------------------------------------------------------------
   HermesHSICountriesHelper.inc.php 2019-10-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSICountriesHelper
{
    /**
     * Returns a list of countries (active/inactive) sorted by the lexicographic conventions of a given locale.
     *
     * If ext-intl (Collator class) is unavailable, strcmp() will be used instead.
     *
     * Returns an array of arrays with keys countries_id, iso2 and name.
     *
     * @param bool   $active
     * @param string $locale
     *
     * @return array
     */
    public static function getCountries(bool $active, string $locale = 'de_DE'): array
    {
        $countryNames  = MainFactory::create('LanguageTextManager', 'countries');
        $countryMapper = static function ($countriesRow) use ($countryNames) {
            return [
                'countries_id' => $countriesRow['countries_id'],
                'iso2'         => $countriesRow['countries_iso_code_2'],
                'name'         => $countryNames->get_text($countriesRow['countries_iso_code_2']),
            ];
        };
        $db            = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $countries     = $db->select('countries_id, countries_iso_code_2')
            ->get_where('countries', ['status' => ($active ? 1 : 0)])
            ->result_array();
        $countries     = array_map($countryMapper, $countries);
        if (class_exists('Collator')) {
            $collator = new Collator($locale);
            usort($countries,
                static function ($countryA, $countryB) use ($collator) {
                    return $collator->compare($countryA['name'], $countryB['name']);
                });
        } else {
            usort($countries,
                static function ($countryA, $countryB) {
                    return strcmp($countryA['name'], $countryB['name']);
                });
        }
        
        return $countries;
    }
}
