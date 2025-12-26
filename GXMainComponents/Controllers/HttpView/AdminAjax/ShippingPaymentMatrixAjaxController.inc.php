<?php
/* --------------------------------------------------------------
   ShippingPaymentMatrixAjaxController.inc.php 2017-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

class ShippingPaymentMatrixAjaxController extends AdminHttpViewController
{
    public function actionGetCountryList()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $joinColumnNames = 'languages.languages_id, languages.name, languages.code, languages.image';
        $joinColumnNames .= ', languages.directory, languages.sort_order, languages.language_charset';
        $joinColumnNames .= ', languages.date_format, languages.date_format_long, languages.date_format_short';
        $joinColumnNames .= ', languages.date_time_format, languages.dob_format_string, languages.html_params';
        $joinColumnNames .= ', languages.language_currency, languages.php_date_time_format, languages.status_admin';
        
        $languageTextManager = MainFactory::create_object('LanguageTextManager',
                                                          ['countries', $_SESSION['languages_id']]);
        
        $countriesData = $db->select("countries.*, {$joinColumnNames}")
                            ->from('countries')
                            ->join('languages','countries.countries_iso_code_2 = languages.code','left')
                            ->get()
                            ->result_array();
        
        $matrixData = $db->get('shipping_and_payment_matrix')->result_array();
        
        $data = [];
        foreach ($countriesData as $country) {
            $active  = false;
            $oldData = [];
            
            foreach ($matrixData as $matrixDataSet) {
                if ($country['countries_iso_code_2'] === $matrixDataSet['country_code']) {
                    $active                                                                 = true;
                    $oldData[$matrixDataSet['language_id']][$matrixDataSet['country_code']] = [
                        'shippingInfo' => $matrixDataSet['shipping_info'],
                        'shippingTime' => $matrixDataSet['shipping_time'],
                        'paymentInfo'  => $matrixDataSet['payment_info'],
                    ];
                }
            }
            
            $data['countries'][] = [
                'id'         => $country['countries_id'],
                'languageId' => $country['languages_id'],
                'code'       => $country['countries_iso_code_2'],
                'name'       => $languageTextManager->get_text($country['countries_iso_code_2']),
                'active'     => (int)$country['status'] === 1 || $active,
                'data'       => count($oldData) > 0 ? $oldData : null
            ];
        }
        
        $languages = $db->order_by('sort_order')->get('languages')->result_array();
        foreach ($languages as $language) {
            $data['languages'][] = [
                'id'        => $language['languages_id'],
                'directory' => $language['directory'],
                'code'      => $language['code']
            ];
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $data);
    }
    
    
    public function actionSave()
    {
        $db                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $shippingInformation = $this->_getPostData('shipping_info');
        $shippingTimes       = $this->_getPostData('shipping_time');
        $paymentInformation  = $this->_getPostData('payment_info');
        
        $db->truncate('shipping_and_payment_matrix');
        foreach ($shippingInformation as $languageId => $shippingInfoData) {
            foreach ($shippingInfoData as $languageCode => $shippingInfo) {
                $db->replace('shipping_and_payment_matrix',
                             [
                                 'country_code'  => $languageCode,
                                 'language_id'   => $languageId,
                                 'shipping_info' => $shippingInfo,
                                 'payment_info'  => $paymentInformation[$languageId][$languageCode],
                                 'shipping_time' => $shippingTimes[$languageId][$languageCode]
                             ]);
            }
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
}