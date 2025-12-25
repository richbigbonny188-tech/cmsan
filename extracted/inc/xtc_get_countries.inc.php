<?php
/* --------------------------------------------------------------
   xtc_get_countries.inc.php 2022-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
   http://www.xtc-webservice.de
   info@xtc-webservice.de
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_get_countries.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_countries.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_get_countriesList($countries_id = '', $with_iso_codes = true, $only_active = true)
{
	$languageTextManager = MainFactory::create_object('LanguageTextManager', array('countries', $_SESSION['languages_id']));

	$countries_array = array();
	if (xtc_not_null($countries_id))
	{
		if ($with_iso_codes == true)
		{
			$countries = xtc_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'" . ($only_active ? " and status = '1'" : "") . " order by countries_name");
			$countries_values = xtc_db_fetch_array($countries);
            
            $country_iso_code_2 = $countries_values['countries_iso_code_2'] ?? null;
            $country_name_translation = $languageTextManager->get_text($country_iso_code_2);
            $country_name = $country_name_translation !== $country_iso_code_2 ? $country_name_translation : $countries_values['countries_name'];
			
			$countries_array = array('countries_name' => $country_name,
									 'countries_iso_code_2' => $countries_values['countries_iso_code_2'] ?? '',
									 'countries_iso_code_3' => $countries_values['countries_iso_code_3'] ?? '');
		}
		else
		{
			$countries = xtc_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'" . ($only_active ? " and status = '1'" : ""));
			$countries_values = xtc_db_fetch_array($countries);
			$countries_array = array('countries_name' => $countries_values['countries_name']);
		}
	}
	else
	{
		if($with_iso_codes == true)
		{
			$countries = xtc_db_query("select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . ($only_active ? " where status = '1'" : "") . " order by countries_name");
			while ($countries_values = xtc_db_fetch_array($countries))
			{
                $country_iso_code_2 = $countries_values['countries_iso_code_2'];
                
                $country_name_translation = $languageTextManager->get_text($country_iso_code_2);
                
                $country_name = $country_name_translation !== $country_iso_code_2 ? $country_name_translation : $countries_values['countries_name'];
                
                $countries_array[] = [
                    'countries_id' => $countries_values['countries_id'],
                    'countries_name' => $country_name,
                    'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
                    'countries_iso_code_3' => $countries_values['countries_iso_code_3']
                ];
			}
		}
		else
		{
			$countries = xtc_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . ($only_active ? " where status = '1'" : "") . " order by countries_name");
			while ($countries_values = xtc_db_fetch_array($countries))
			{
				$countries_array[] = array('countries_id' => $countries_values['countries_id'],
										   'countries_name' => $countries_values['countries_name']);
			}
		}
		usort($countries_array, 'sortCountriesByCountriesName');
	}

	return $countries_array;
}

/**
 * @param array $a
 * @param array $b
 * @return int
 */
function sortCountriesByCountriesName(array $a, array $b)
{
	if($a['countries_name'] == $b['countries_name'])
	{
		return 0;
	}
	$arr_search  = array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ô', 'ß');
	$arr_replace = array('A', 'O', 'U', 'a', 'o', 'u', 'o', 'ss');
	$a['countries_name']   = str_replace( $arr_search, $arr_replace, $a['countries_name']);
	$b['countries_name']   = str_replace( $arr_search, $arr_replace, $b['countries_name']);
	$return = ($a['countries_name'] < $b['countries_name']) ? -1 : +1;
	$a['countries_name']   = str_replace( $arr_replace, $arr_search, $a['countries_name']);
	$b['countries_name']   = str_replace( $arr_replace, $arr_search, $b['countries_name']);
	return $return;

	return $countries_array;
}