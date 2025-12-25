<?php
/* --------------------------------------------------------------
   PayPalLocaleFactory.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayPalLocaleFactory
{
    protected $paypalLocales;

	public function __construct()
	{
		$this->paypalLocales = json_decode(
			'{
				"AL": ["en_US"],
				"DZ": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"AD": ["en_US","fr_XC","es_XC","zh_XC"],
				"AO": ["en_US","fr_XC","es_XC","zh_XC"],
				"AI": ["en_US","fr_XC","es_XC","zh_XC"],
				"AG": ["en_US","fr_XC","es_XC","zh_XC"],
				"AR": ["es_XC","en_US"],
				"AM": ["en_US","fr_XC","es_XC","zh_XC"],
				"AW": ["en_US","fr_XC","es_XC","zh_XC"],
				"AU": ["en_AU"],
				"AT": ["de_DE","en_US"],
				"AZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"BS": ["en_US","fr_XC","es_XC","zh_XC"],
				"BH": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"BB": ["en_US","fr_XC","es_XC","zh_XC"],
				"BY": ["en_US"],
				"BE": ["en_US","nl_NL","fr_FR"],
				"BZ": ["es_XC","en_US","fr_XC","zh_XC"],
				"BJ": ["fr_XC","en_US","es_XC","zh_XC"],
				"BM": ["en_US","fr_XC","es_XC","zh_XC"],
				"BT": ["en_US"],
				"BO": ["es_XC","en_US","fr_XC","zh_XC"],
				"BA": ["en_US"],
				"BW": ["en_US","fr_XC","es_XC","zh_XC"],
				"BR": ["pt_BR","en_US"],
				"VG": ["en_US","fr_XC","es_XC","zh_XC"],
				"BN": ["en_US"],
				"BG": ["en_US"],
				"BF": ["fr_XC","en_US","es_XC","zh_XC"],
				"BI": ["fr_XC","en_US","es_XC","zh_XC"],
				"KH": ["en_US"],
				"CM": ["fr_XC","en_US"],
				"CA": ["en_US","fr_CA"],
				"CV": ["en_US","fr_XC","es_XC","zh_XC"],
				"KY": ["en_US","fr_XC","es_XC","zh_XC"],
				"TD": ["fr_XC","en_US","es_XC","zh_XC"],
				"CL": ["es_XC","en_US","fr_XC","zh_XC"],
				"CN": ["zh_CN"],
				"C2": ["zh_XC","en_US"],
				"CO": ["es_XC","en_US","fr_XC","zh_XC"],
				"KM": ["fr_XC","en_US","es_XC","zh_XC"],
				"CG": ["en_US","fr_XC","es_XC","zh_XC"],
				"CD": ["fr_XC","en_US","es_XC","zh_XC"],
				"CK": ["en_US","fr_XC","es_XC","zh_XC"],
				"CR": ["es_XC","en_US","fr_XC","zh_XC"],
				"CI": ["fr_XC","en_US"],
				"HR": ["en_US"],
				"CY": ["en_US"],
				"CZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"DK": ["da_DK","en_US"],
				"DJ": ["fr_XC","en_US","es_XC","zh_XC"],
				"DM": ["en_US","fr_XC","es_XC","zh_XC"],
				"DO": ["es_XC","en_US","fr_XC","zh_XC"],
				"EC": ["es_XC","en_US","fr_XC","zh_XC"],
				"EG": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"SV": ["es_XC","en_US","fr_XC","zh_XC"],
				"ER": ["en_US","fr_XC","es_XC","zh_XC"],
				"EE": ["en_US","ru_RU","fr_XC","es_XC","zh_XC"],
				"ET": ["en_US","fr_XC","es_XC","zh_XC"],
				"FK": ["en_US","fr_XC","es_XC","zh_XC"],
				"FO": ["da_DK","en_US","fr_XC","es_XC","zh_XC"],
				"FJ": ["en_US","fr_XC","es_XC","zh_XC"],
				"FI": ["en_US","fr_XC","es_XC","zh_XC"],
				"FR": ["fr_FR","en_US"],
				"GF": ["en_US","fr_XC","es_XC","zh_XC"],
				"PF": ["en_US","fr_XC","es_XC","zh_XC"],
				"GA": ["fr_XC","en_US","es_XC","zh_XC"],
				"GM": ["en_US","fr_XC","es_XC","zh_XC"],
				"GE": ["en_US","fr_XC","es_XC","zh_XC"],
				"DE": ["de_DE","en_US"],
				"GI": ["en_US","fr_XC","es_XC","zh_XC"],
				"GR": ["en_US","fr_XC","es_XC","zh_XC"],
				"GL": ["da_DK","en_US","fr_XC","es_XC","zh_XC"],
				"GD": ["en_US","fr_XC","es_XC","zh_XC"],
				"GP": ["en_US","fr_XC","es_XC","zh_XC"],
				"GT": ["es_XC","en_US","fr_XC","zh_XC"],
				"GN": ["fr_XC","en_US","es_XC","zh_XC"],
				"GW": ["en_US","fr_XC","es_XC","zh_XC"],
				"GY": ["en_US","fr_XC","es_XC","zh_XC"],
				"HN": ["es_XC","en_US","fr_XC","zh_XC"],
				"HK": ["en_GB","zh_HK"],
				"HU": ["en_US","fr_XC","es_XC","zh_XC"],
				"IS": ["en_US"],
				"IN": ["en_GB"],
				"ID": ["id_ID","en_US"],
				"IE": ["en_US","fr_XC","es_XC","zh_XC"],
				"IL": ["he_IL","en_US"],
				"IT": ["it_IT","en_US"],
				"JM": ["es_XC","en_US","fr_XC","zh_XC"],
				"JP": ["ja_JP","en_US"],
				"JO": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"KZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"KE": ["en_US","fr_XC","es_XC","zh_XC"],
				"KI": ["en_US","fr_XC","es_XC","zh_XC"],
				"KW": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"KG": ["en_US","fr_XC","es_XC","zh_XC"],
				"LA": ["en_US"],
				"LV": ["en_US","ru_RU","fr_XC","es_XC","zh_XC"],
				"LS": ["en_US","fr_XC","es_XC","zh_XC"],
				"LI": ["en_US","fr_XC","es_XC","zh_XC"],
				"LT": ["en_US","ru_RU","fr_XC","es_XC","zh_XC"],
				"LU": ["en_US","de_DE","fr_XC","es_XC","zh_XC"],
				"MK": ["en_US"],
				"MG": ["en_US","fr_XC","es_XC","zh_XC"],
				"MW": ["en_US","fr_XC","es_XC","zh_XC"],
				"MY": ["en_US"],
				"MV": ["en_US"],
				"ML": ["fr_XC","en_US","es_XC","zh_XC"],
				"MT": ["en_US"],
				"MH": ["en_US","fr_XC","es_XC","zh_XC"],
				"MQ": ["en_US","fr_XC","es_XC","zh_XC"],
				"MR": ["en_US","fr_XC","es_XC","zh_XC"],
				"MU": ["en_US","fr_XC","es_XC","zh_XC"],
				"YT": ["en_US","fr_XC","es_XC","zh_XC"],
				"MX": ["es_XC","en_US"],
				"FM": ["en_US"],
				"MD": ["en_US"],
				"MC": ["fr_XC","en_US"],
				"MN": ["en_US"],
				"ME": ["en_US"],
				"MS": ["en_US","fr_XC","es_XC","zh_XC"],
				"MA": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"MZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"NA": ["en_US","fr_XC","es_XC","zh_XC"],
				"NR": ["en_US","fr_XC","es_XC","zh_XC"],
				"NP": ["en_US"],
				"NL": ["nl_NL","en_US"],
				"NC": ["en_US","fr_XC","es_XC","zh_XC"],
				"NZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"NI": ["es_XC","en_US","fr_XC","zh_XC"],
				"NE": ["fr_XC","en_US","es_XC","zh_XC"],
				"NG": ["en_US"],
				"NU": ["en_US","fr_XC","es_XC","zh_XC"],
				"NF": ["en_US","fr_XC","es_XC","zh_XC"],
				"NO": ["no_NO","en_US"],
				"OM": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"PW": ["en_US","fr_XC","es_XC","zh_XC"],
				"PA": ["es_XC","en_US","fr_XC","zh_XC"],
				"PG": ["en_US","fr_XC","es_XC","zh_XC"],
				"PY": ["es_XC","en_US"],
				"PE": ["es_XC","en_US","fr_XC","zh_XC"],
				"PH": ["en_US"],
				"PN": ["en_US","fr_XC","es_XC","zh_XC"],
				"PL": ["pl_PL","en_US"],
				"PT": ["pt_PT","en_US"],
				"QA": ["en_US","fr_XC","es_XC","zh_XC","ar_EG"],
				"RE": ["en_US","fr_XC","es_XC","zh_XC"],
				"RO": ["en_US","fr_XC","es_XC","zh_XC"],
				"RU": ["ru_RU","en_US"],
				"RW": ["fr_XC","en_US","es_XC","zh_XC"],
				"WS": ["en_US"],
				"SM": ["en_US","fr_XC","es_XC","zh_XC"],
				"ST": ["en_US","fr_XC","es_XC","zh_XC"],
				"SA": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"SN": ["fr_XC","en_US","es_XC","zh_XC"],
				"RS": ["en_US","fr_XC","es_XC","zh_XC"],
				"SC": ["fr_XC","en_US","es_XC","zh_XC"],
				"SL": ["en_US","fr_XC","es_XC","zh_XC"],
				"SG": ["en_GB"],
				"SK": ["en_US","fr_XC","es_XC","zh_XC"],
				"SI": ["en_US","fr_XC","es_XC","zh_XC"],
				"SB": ["en_US","fr_XC","es_XC","zh_XC"],
				"SO": ["en_US","fr_XC","es_XC","zh_XC"],
				"ZA": ["en_US","fr_XC","es_XC","zh_XC"],
				"KR": ["ko_KR","en_US"],
				"ES": ["es_ES","en_US"],
				"LK": ["en_US"],
				"SH": ["en_US","fr_XC","es_XC","zh_XC"],
				"KN": ["en_US","fr_XC","es_XC","zh_XC"],
				"LC": ["en_US","fr_XC","es_XC","zh_XC"],
				"PM": ["en_US","fr_XC","es_XC","zh_XC"],
				"VC": ["en_US","fr_XC","es_XC","zh_XC"],
				"SR": ["en_US","fr_XC","es_XC","zh_XC"],
				"SJ": ["en_US","fr_XC","es_XC","zh_XC"],
				"SZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"SE": ["sv_SE","en_US"],
				"CH": ["de_DE","fr_FR","en_US"],
				"TW": ["zh_TW","en_US"],
				"TJ": ["en_US","fr_XC","es_XC","zh_XC"],
				"TZ": ["en_US","fr_XC","es_XC","zh_XC"],
				"TH": ["th_TH","en_GB"],
				"TG": ["fr_XC","en_US","es_XC","zh_XC"],
				"TO": ["en_US"],
				"TT": ["en_US","fr_XC","es_XC","zh_XC"],
				"TN": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"TM": ["en_US","fr_XC","es_XC","zh_XC"],
				"TC": ["en_US","fr_XC","es_XC","zh_XC"],
				"TV": ["en_US","fr_XC","es_XC","zh_XC"],
				"UG": ["en_US","fr_XC","es_XC","zh_XC"],
				"UA": ["en_US","ru_RU","fr_XC","es_XC","zh_XC"],
				"AE": ["en_US","fr_XC","es_XC","zh_XC","ar_EG"],
				"GB": ["en_GB","fr_FR"],
				"US": ["en_US","fr_XC","es_XC","zh_XC"],
				"UY": ["es_XC","en_US","fr_XC","zh_XC"],
				"VU": ["en_US","fr_XC","es_XC","zh_XC"],
				"VA": ["en_US","fr_XC","es_XC","zh_XC"],
				"VE": ["es_XC","en_US","fr_XC","zh_XC"],
				"VN": ["en_US"],
				"WF": ["en_US","fr_XC","es_XC","zh_XC"],
				"YE": ["ar_EG","en_US","fr_XC","es_XC","zh_XC"],
				"ZM": ["en_US","fr_XC","es_XC","zh_XC"],
				"ZW": ["en_US"]
			}',
			true
		);
	}

	/**
	 * Returns the PayPal locale for a language and country.
	 *
	 * @param string $language 2-letter language code
	 * @param string $country 2-letter country code
	 *
	 * @return string PayPal locale
	 */
	public function getLocaleByLanguageAndCountry($language, $country)
	{
		$language = strtolower(substr($language, 0, 2));
		$country  = strtoupper(substr($country,  0, 2));
		$stdLocale = $language . '_' . $country;
		$payPalLocale = '';
		if(isset($this->paypalLocales[$country])) {
			$countryLocales = $this->paypalLocales[$country];
			if(in_array($stdLocale, $countryLocales)) {
				$payPalLocale = $stdLocale;
			} else {
				$payPalLocale = $countryLocales[0];
				foreach($countryLocales as $countryLocale) {
					if($language === substr($countryLocale, 0 , 2)) {
						$payPalLocale = $countryLocale;
					}
				}
			}
		}
		return $payPalLocale;
	}
}
