<?php
/* --------------------------------------------------------------
   AmazonSsoController.inc.php 2019-11-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AmazonSsoController extends HttpViewController
{
    public function init()
    {
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($moduleInstalled === false) {
            throw new Exception('Single Sign-on module not installed');
        }
    }
    
    
    public function proceed(HttpContextInterface $context)
    {
        return parent::proceed($context);
    }
    
    
    public function actionGetAddress()
    {
        $orderReferenceId = $this->_getPostData('orderReferenceId');
        try {
            $this->validateOrderReferenceId($orderReferenceId);
            $advancedPayments = MainFactory::create('AmazonAdvancedPayment');
            /** @var SimpleXMLElement $orderDetails */
            $orderDetailsXml     = $advancedPayments->get_order_reference_details($orderReferenceId, true);
            $physicalDestination = $orderDetailsXml->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination;
            $nameParts           = [];
            if (preg_match('/(\w+)\s(.*)/u', (string)$physicalDestination->Name, $nameParts) === 1) {
                $firstName = $nameParts[1];
                $lastName  = $nameParts[2];
            } else {
                $firstName = '';
                $lastName  = '';
            }
            $streetParts = [];
            if (preg_match('/(.*)\s(\d+.*)/', (string)$physicalDestination->AddressLine2, $streetParts) === 1) {
                $street      = $streetParts[1];
                $houseNumber = $streetParts[2];
            } else {
                $street      = '';
                $houseNumber = '';
            }
            
            /** @var CountryServiceInterface $countryService */
            $countryService  = StaticGXCoreLoader::getService('Country');
            $customerCountry = $countryService->getCountryByIso2((string)$physicalDestination->CountryCode);
            
            $responseData = [
                'address' => [
                    'city'          => (string)$physicalDestination->City,
                    'countryCode'   => (string)$physicalDestination->CountryCode,
                    'countryId'     => $customerCountry->getId(),
                    'countryStatus' => $customerCountry->getStatus(),
                    'postalCode'    => (string)$physicalDestination->PostalCode,
                    'name'          => (string)$physicalDestination->Name,
                    'firstName'     => $firstName,
                    'lastName'      => $lastName,
                    'addressLine1'  => (string)$physicalDestination->AddressLine1,
                    'addressLine2'  => (string)$physicalDestination->AddressLine2,
                    'street'        => $street,
                    'houseNumber'   => $houseNumber,
                    'stateOrRegion' => (string)$physicalDestination->StateOrRegion,
                    'phone'         => (string)$physicalDestination->phone,
                ],
            ];
        } catch (Exception $e) {
            $responseData = [
                'ERROR' => 'invalid order reference: ' . $e->getMessage(),
            ];
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $responseData);
    }
    
    
    protected function validateOrderReferenceId($orderReferenceId)
    {
        $pattern = '/^(S|P)\d{2}-\d{7}-\d{7}$/';
        if (preg_match($pattern, $orderReferenceId) !== 1) {
            throw new Exception('Invalid order reference ID ' . $orderReferenceId);
        }
    }
}
