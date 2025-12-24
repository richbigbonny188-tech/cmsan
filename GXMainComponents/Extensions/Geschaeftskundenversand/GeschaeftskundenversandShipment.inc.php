<?php
/* --------------------------------------------------------------
   GeschaeftskundenversandShipment.inc.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GeschaeftskundenversandShipment
 *
 * @category   System
 * @package    Extensions
 * @subpackage Geschaeftskundenversand
 */
class GeschaeftskundenversandShipment
{
    protected $ekp;
    
    protected $shipmentData;
    
    protected $product;
    
    
    /**
     * GeschaeftskundenversandShipment constructor.
     *
     * @param $ekp
     *
     * @throws Exception
     */
    public function __construct($ekp)
    {
        if (preg_match('/^\d{10}$/', $ekp) !== 1) {
            throw new Exception('invalid EKP format - must be 10 digits');
        }
        $this->ekp = $ekp;
        
        $this->shipmentData = [
            'Version'       => [
                'majorRelease' => (string)GeschaeftskundenversandConfigurationStorage::MAJOR_VERSION,
                'minorRelease' => (string)GeschaeftskundenversandConfigurationStorage::MINOR_VERSION,
                'build'        => (string)GeschaeftskundenversandConfigurationStorage::BUILD,
            ],
            'ShipmentOrder' => [
                'sequenceNumber'      => '1',
                'Shipment'            => [
                    'ShipmentDetails' => [
                        'product'           => '',
                        'accountNumber'     => '',
                        // Abrechnungsnummer = EKP + Verfahren + Teilnahme // aka account number
                        'shipmentDate'      => date('Y-m-d'),
                        'ShipmentItem'      => [
                            'weightInKG' => '0.100',
                            # 'lengthInCM' => '42', // optional
                            # 'widthInCM'  => '23', // optional
                            # 'heightInCM' => '17', // optional
                        ],
                        'customerReference' => '',
                        // optional
                        # 'returnShipmentAccountNumber' => '', // optional
                        # 'returnShipmentReference'     => '', // optional
                        'Service'           => [],
                        #'Service' => [
                        #	'DayOfDelivery' => [
                        #		'_'       => '',
                        #		'active'  => '0', // 0|1
                        #		'details' => 'yyyy-mm-dd',
                        #	],
                        #	'DeliveryTimeframe' => [ // <DeliveryTimeframe active="1" type="10001200" />
                        #		'_'       => '',
                        #		'active'  => '0', // 0|1
                        #		'type'    => '10001200', // 10001200|12001400|14001600|16001800|18002000|19002100
                        #	],
                        #	'IndividualSenderRequirement' => [
                        #		'_'       => '',
                        #		'active'  => '0', // 0|1
                        #		'details' => 'lorem ipsum', // string 1..250
                        #	],
                        #	'ShipmentHandling' => [
                        #		'_'       => '',
                        #		'active'  => '0',
                        #		'type'    => 'a' // a|b|c|d|e, cf. https://entwickler.dhl.de/group/ep/wsapis/geschaeftskundenversand/operationen/createshipmentorder/ioreference
                        #	],
                        #	'Endorsement' => [
                        #		'_'       => '',
                        #		'active'  => '0',
                        #		'type'    => 'SOZU', // SOZU|ZWZU|IMMEDIATE|AFTER_DEADLINE|ABANDONMENT
                        #	],
                        #	'VisualCheckOfAge' => [
                        #		'_'       => '',
                        #		'active'  => '0',
                        #		'type'    => 'A16', // A16|A18
                        #	],
                        #	'PreferredLocation' => [
                        #		'_'       => '',
                        #		'active'  => '0',
                        #		'details' => 'Hundehütte', // string 1..100
                        #	],
                        #	'PreferredNeighbour' => [
                        #		'_'       => '',
                        #		'active'  => '0',
                        #		'...'     => '...', // ??? not documented
                        #	],
                        #	'GoGreen'        => ['_' => '', 'active' => '0', ],
                        #	'Perishables'    => ['_' => '', 'active' => '0', ],
                        #	'Personally'     => ['_' => '', 'active' => '0', ],
                        #	'ReturnReceipt'  => ['_' => '', 'active' => '0', ],
                        #	'Premium'        => ['_' => '', 'active' => '0', ],
                        #	'CashOnDelivery' => [
                        #		'_'               => '',
                        #		'active'          => '0',
                        #		'codAmount'       => '0.00',
                        #	],
                        #	'AdditionalInsurance' => [
                        #		'_'               => '',
                        #		'active'          => '0',
                        #		'insuranceAmount' => '0.00',
                        #	],
                        #	'BulkyGoods' => [
                        #		'_'               => '',
                        #		'active'          => '0',
                        #	],
                        #],
                        #'Notification' => [ // optional
                        #	'recipientEmailAddress' => 'foo@bar.example.invalid',
                        #],
                        #'BankData' => [ // optional, e.g. for CoD
                        #	'accountOwner'     => '', // string 1..80
                        #	'bankName'         => '', // string 1..80
                        #	'iban'             => '', // string 1..34
                        #	'note1'            => '', // string 1..35, optional
                        #	'note2'            => '', // string 1..35, optional
                        #	'bic'              => '', // string 1..11, optional
                        #	'accountreference' => '', // string 1..35, optional
                        #],
                    ],
                    // ShipmentDetails
                    'Shipper'         => [
                        'Name'          => [
                            'name1' => '', // 1..50, name
                            # 'name2' => '', // 1..50, optional, company
                            # 'name3' => '', // 1..50, optional, company
                        ],
                        'Address'       => [
                            'streetName'   => '', // 1..35
                            'streetNumber' => '', // 1..5
                            # 'addressAddition' => '', // 1..35, optional
                            'zip'          => '', // 1..10
                            'city'         => '', // 1..35
                            'Origin'       => [
                                # 'country'        => '', // 1..30, optional
                                'countryISOCode' => '', // 2, ISO Code
                                # 'state'          => '', // 1..30, optional
                            ],
                        ],
                        'Communication' => [ // required!
                                             # 'phone'         => '', // 1..20, optional
                                             # 'email'         => '', // 1..50, optional
                                             # 'contactPerson' => '', // 1..50, optional
                        ],
                    ],
                    'Receiver'        => [
                        'name1'         => '', // 1..50
                        'Communication' => [ // required
                                             # 'phone'         => '', // 1..20, optional
                                             # 'email'         => '', // 1..50, optional
                                             # 'contactPerson' => '', // 1..50, optional
                        ],
                        'Address'       => [
                            # 'name2'                  => '', // 1..50, optional
                            # 'name3'                  => '', // 1..50, optional
                            'streetName'   => '', // 1..35
                            'streetNumber' => '', // 1..5
                            # 'addressAddition'        => '', // 1..35, optional
                            # 'dispatchingInformation' => '', // 1..35, optional
                            'zip'          => '', // 1..10
                            'city'         => '', // 1..35
                            'Origin'       => [ // optional
                                                # 'country'        => '', // 1..30
                                                # 'countryISOCode' => '', // required
                                                # 'state'          => '', // optional
                            ],
                        ],
                        # 'Packstation' => [
                        # 	'postNumber'        => '', // 1..10, optional if e-mail/mobile phone number given
                        # 	'packstationNumber' => '', // 3, required
                        # 	'zip'               => '', // 1..10
                        # 	'city'              => '', // 1..35
                        # 	'Origin'            => [
                        # 		'country'        => '', // 1..30, optional
                        # 		'countryISOCode' => '', // 2, required
                        # 		'state'          => '', // 1..30, optional
                        # 	],
                        # ],
                        # 'Postfiliale' => [
                        # 	'postfilialNumber' => '', // 3
                        # 	'postNumber'       => '', // 1..10
                        # 	'zip'              => '', // 1..10
                        # 	'city'             => '', // 1..35
                        # 	'Origin'           => [
                        # 		'country'        => '', // 1..30, optional
                        # 		'countryISOCode' => '', // 2, required
                        # 		'state'          => '', // 1..30
                        # 	],
                        # ],
                        # 'ParcelShop' => [
                        # 	'parcelShopNumber' => '', // 3
                        # 	'streetName'       => '', // 1..35, optional
                        # 	'streetNumber'     => '', // 1..5, optional
                        # 	'zip'              => '', // 1..10, required
                        # 	'city'             => '', // 1..35, required
                        # 	'Origin'           => [
                        # 		'country'        => '', // 1..30, optional
                        # 		'countryISOCode' => '', // 2
                        # 		'state'          => '', // 1..30
                        # 	],
                        # ],
                    ],
                    # 'ReturnReceiver' => [
                    # 	'Name' => [
                    # 		'name1' => '', // 1..50, optional
                    # 		'name2' => '', // 1..50, required
                    # 		'name3' => '', // 1..50, required
                    # 	],
                    # 	'Address' => [
                    # 		'streetName'             => '', // 1..35
                    # 		'streetNumber'           => '', // 1..5
                    # 		'addressAddition'        => '', // 1..35, optional
                    # 		'dispatchingInformation' => '', // 1..35, optional
                    # 		'zip'                    => '', // 1..5
                    # 		'city'                   => '', // 1..35
                    # 		'Origin'                 => [
                    # 			'country'        => '', // 1..30, optional
                    # 			'countryISOCode' => '', // 2
                    # 			'state'          => '', // 1..30
                    # 		],
                    # 	],
                    # 	'Communication' => [
                    # 		'phone'         => '', // 1..20, optional
                    # 		'email'         => '', // 1..50, optional
                    # 		'contactPerson' => '', // 1..50, optional
                    # 	],
                    # ],
                    # 'ExportDocument' => [
                    # 	'invoiceNumber'              => '', // 1..35, optional
                    # 	'exportType'                 => '', // OTHER|PRESENT|COMMERCIAL_SAMPLE|DOCUMENT|RETURN_OF_GOODS|COMMERCIAL_GOODS, required if non-EU
                    # 	'exportTypeDescription'      => '', // 1..256, optional; required for exportType OTHER
                    # 	'termsOfTrade'               => '', // DDP|DXV|DDU|DDX, optional (incoterms code)
                    # 	'placeOfCommital'            => '', // 1..35
                    # 	'additionalFee'              => '', // decimal
                    # 	'permitNumber'               => '', // 1..10, optional
                    # 	'attestationNumber'          => '', // 1..35, optional
                    # 	'WithElectronicExportNtfctn' => [ // optional
                    # 		'_'      => '',
                    # 		'active' => '0', // 0|1
                    # 	],
                    # 	'ExportDocPosition' => [ // multiple child elements for intl, one for EU
                    # 		'description'         => '', // 1..256
                    # 		'countryCodeOrigin'   => '', // 2, ISO code
                    # 		'customsTariffNumber' => '', // 1..10
                    # 		'amount'              => '', // int, quantity
                    # 		'netWeightKG'         => '', // dec
                    # 		'customsValue'        => '', // dec
                    # 	],
                    # ],
                ],
                'PrintOnlyIfCodeable' => ['_' => '', 'active' => '0'],
                'labelResponseType'   => 'URL', // URL|B64
            ],
        ];
    }
    
    
    public function toArray()
    {
        return $this->shipmentData;
    }
    
    
    public function setProduct(GeschaeftskundenversandProduct $product, $setReturnAccount = false)
    {
        $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['product']       = $product->getApiProductCode();
        $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['accountNumber'] = sprintf('%s%s%s',
                                                                                                       $this->ekp,
                                                                                                       $product->getProcedure(),
                                                                                                       $product->getAttendance());
        
        $this->product = $product;
    }
    
    
    public function setReturnShipmentAccountNumber(string $accountNumber): void
    {
        $returnProcedures = [
            '01' => '07',
            '06' => '07',
            '62' => '07',
            '86' => '83',
            '87' => '85',
        ];
        if (array_key_exists($this->product->getProcedure(), $returnProcedures)) {
            $accountNumber   = substr($accountNumber, 0, 14);
            $returnProcedure = $returnProcedures[$this->product->getProcedure()];
            $accountNumber   = substr_replace($accountNumber, $returnProcedure, 10, strlen($returnProcedure));
            
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['returnShipmentAccountNumber'] = $accountNumber;
        }
    }
    
    
    public function setShipmentDate($date)
    {
        $shipmentDateTime                                                                   = new DateTime($date);
        $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['shipmentDate'] = $shipmentDateTime->format('Y-m-d');
    }
    
    
    public function setWeight($weight)
    {
        $weight                                                                                           = max((double)$weight,
                                                                                                                0.1);
        $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['ShipmentItem']['weightInKG'] = sprintf('%.3f',
                                                                                                                    $weight);
    }
    
    
    public function setCustomerReference($customerReference)
    {
        $customerReference                                                                       = mb_substr($customerReference,
                                                                                                             0,
                                                                                                             35);
        $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['customerReference'] = $customerReference;
    }
    
    
    public function setNotification($emailAddress)
    {
        $emailAddress = mb_substr($emailAddress, 0, 50);
        if (!empty($emailAddress)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Notification'] = ['recipientEmailAddress' => $emailAddress];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Notification']);
        }
    }
    
    
    public function setBankData(
        $accountOwner,
        $bankName,
        $iban,
        $note1 = '',
        $note2 = '',
        $bic = '',
        $accountreference = ''
    ) {
        $accountOwner     = mb_substr($accountOwner, 0, 80);
        $bankName         = mb_substr($bankName, 0, 80);
        $iban             = mb_substr($iban, 0, 34);
        $note1            = mb_substr($note1, 0, 35);
        $note2            = mb_substr($note2, 0, 35);
        $bic              = mb_substr($bic, 0, 11);
        $accountreference = mb_substr($accountreference, 0, 35);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['BankData'] = [
            'accountOwner'     => $accountOwner,
            'bankName'         => $bankName,
            'iban'             => $iban,
            'note1'            => $note1,
            'note2'            => $note2,
            'bic'              => $bic,
            'accountreference' => $accountreference,
        ];
    }
    
    
    public function setServiceDayOfDelivery($date = '')
    {
        try {
            $dateTime                                                                                       = new DateTime($date);
            $date                                                                                           = $dateTime->format('Y-m-d');
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['DayOfDelivery'] = [
                '_'       => '',
                'active'  => '1',
                'details' => $date,
            ];
        } catch (Exception $e) {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['DayOfDelivery']);
        }
    }
    
    
    public function setServiceDeliveryTimeframe($timeframe = '')
    {
        $allowedTimeframes = ['10001200', '12001400', '14001600', '16001800', '18002000', '19002100'];
        if (in_array($timeframe, $allowedTimeframes)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['DeliveryTimeframe'] = [
                '_'      => '',
                'active' => '1',
                'type'   => $timeframe,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['DeliveryTimeframe']);
        }
    }
    
    
    public function setServiceIndividualSenderRequirement($requirementText = '')
    {
        $requirementText = trim($requirementText);
        if (!empty($requirementText)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['IndividualSenderRequirement'] = [
                '_'       => '',
                'active'  => '1',
                'details' => $requirementText,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['IndividualSenderRequirement']);
        }
    }
    
    
    public function setServiceShipmentHandling($type = '')
    {
        if (in_array($type, ['a', 'b', 'c', 'd', 'e'])) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ShipmentHandling'] = [
                '_'      => '',
                'active' => '1',
                'type'   => $type,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ShipmentHandling']);
        }
    }
    
    
    public function setServiceEndorsement($type = '')
    {
        $type = strtoupper((string)$type);
        if (in_array($type, ['IMMEDIATE', 'ABANDONMENT',])) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Endorsement'] = [
                '_'      => '',
                'active' => '1',
                'type'   => $type,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Endorsement']);
        }
    }
    
    
    public function setServiceVisualCheckOfAge($type = '')
    {
        if (in_array($type, ['A16', 'A18'])) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['VisualCheckOfAge'] = [
                '_'      => '',
                'active' => '1',
                'type'   => $type,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['VisualCheckOfAge']);
        }
    }
    
    
    public function setServicePreferredLocation($details = '')
    {
        if (isset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredNeighbour'])) {
            // PreferredLocation and PreferredNeighbour are mutually exclusive
            return;
        }
        $details = mb_substr(trim($details), 0, 100);
        if (!empty($details)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredLocation'] = [
                '_'       => '',
                'active'  => '1',
                'details' => $details,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredLocation']);
        }
    }
    
    
    public function setServiceGoGreen($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['GoGreen'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['GoGreen']);
        }
    }
    
    
    public function setServicePerishables($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Perishables'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Perishables']);
        }
    }
    
    
    public function setServicePersonally($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Personally'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Personally']);
        }
    }
    
    
    public function setServiceSignedForByRecipient(bool $activate = false)
    {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['SignedForByRecipient']);
        if ($activate) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['SignedForByRecipient'] = [
                '_'      => '',
                'active' => '1',
            ];
        }
    }

    
    public function setServiceReturnReceipt($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ReturnReceipt'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ReturnReceipt']);
        }
    }
    
    
    public function setServicePremium($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Premium'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Premium']);
        }
    }
    
    
    /**
     * Sets service Postal Delivery Duty Paid
     *
     * @param bool $activate
     *
     * @return void
     */
    public function setServicePDDP(bool $activate = false): void
    {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PDDP']);
        if ($activate) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PDDP'] = [
                '_'      => '',
                'active' => '1',
            ];
        }
    }
    
    
    /**
     * Sets service Closest Drop Point
     *
     * @param bool $activate
     *
     * @return void
     */
    public function setServiceCDP(bool $activate = false): void
    {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['CDP']);
        if ($activate) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['CDP'] = [
                '_'      => '',
                'active' => '1',
            ];
        }
    }
    
    
    /**
     * Sets service Economy
     *
     * @param bool $activate
     *
     * @return void
     */
    public function setServiceEconomy(bool $activate = false): void
    {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Economy']);
        if ($activate) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['Economy'] = [
                '_'      => '',
                'active' => '1',
            ];
        }
    }
    
    
    public function setServiceBulkyGoods($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['BulkyGoods'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['BulkyGoods']);
        }
    }
    
    
    /**
     * Sets COD amount.
     *
     * @param float $codAmount
     * @param bool  $addFee deprecated, unused
     */
    public function setServiceCashOnDelivery($codAmount = 0.0, $addFee = false)
    {
        $codAmount = (double)$codAmount;
        if ($codAmount > 0) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['CashOnDelivery'] = [
                '_'         => '',
                'active'    => '1',
                'codAmount' => number_format($codAmount, 2, '.', ''),
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['CashOnDelivery']);
        }
    }
    
    
    public function setServiceAdditionalInsurance($insuranceAmount = 0)
    {
        $insuranceAmount = (double)$insuranceAmount;
        if ($insuranceAmount > 0) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['AdditionalInsurance'] = [
                '_'               => '',
                'active'          => '1',
                'insuranceAmount' => number_format($insuranceAmount, 2, '.', ''),
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['AdditionalInsurance']);
        }
    }
    
    
    public function setServicePreferredNeighbour($details = '')
    {
        if (isset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredLocation'])) {
            // PreferredLocation and PreferredNeighbour are mutually exclusive
            return;
        }
        $details = mb_substr((string)$details, 0, 100);
        if (!empty($details)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredNeighbour'] = [
                '_'       => '',
                'active'  => '1',
                'details' => $details,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredNeighbour']);
        }
    }
    
    
    public function setServiceNamedPersonOnly($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['NamedPersonOnly'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['NamedPersonOnly']);
        }
    }
    
    
    public function setServiceIdentCheck($surname = '', $givenName = '', $dateOfBirth = '', $minimumAge = '')
    {
        $surname = mb_substr((string)$surname, 0, 255);
        if (!empty($surname)) {
            $givenName   = mb_substr((string)$givenName, 0, 255);
            $dateOfBirth = (new DateTime($dateOfBirth))->format('Y-m-d');
            $minimumAge  = in_array($minimumAge, ['', 'A16', 'A18',], true) ? $minimumAge : '';
            
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['IdentCheck'] = [
                'active' => '1',
                'Ident'  => [
                    'surname'     => $surname,
                    'givenName'   => $givenName,
                    'dateOfBirth' => $dateOfBirth,
                    'minimumAge'  => $minimumAge,
                ],
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['IdentCheck']);
        }
    }
    
    
    public function setServicePreferredDay($details = '')
    {
        if (!empty($details)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredDay'] = [
                '_'       => '',
                'active'  => '1',
                'details' => $details,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredDay']);
        }
    }
    
    
    public function setServicePreferredTime($preferredTime = 0)
    {
        $timeFrames = ['10001200', '12001400', '14001600', '16001800', '18002000', '19002100'];
        if (!empty($preferredTime) && in_array($preferredTime, $timeFrames)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredTime'] = [
                '_'      => '',
                'active' => '1',
                'type'   => $preferredTime,
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PreferredTime']);
        }
    }
    
    
    public function setServiceNoNeighbourDelivery($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['NoNeighbourDelivery'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['NoNeighbourDelivery']);
        }
    }
    
    
    public function setServicePackagingReturn($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PackagingReturn'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['PackagingReturn']);
        }
    }
    
    
    public function setServiceNoticeOfNonDeliverability($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['NoticeOfNonDeliverability'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['NoticeOfNonDeliverability']);
        }
    }
    
    
    public function setServiceReturnImmediately($activate = false)
    {
        $activate = (bool)$activate;
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ReturnImmediately'] = [
                '_'      => '',
                'active' => '1',
            ];
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ReturnImmediately']);
        }
    }
    
    
    public function setServiceParcelOutletRouting(bool $activate = false, string $email = ''): void
    {
        if ($activate === true) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ParcelOutletRouting'] = [
                '_'      => '',
                'active' => '1',
            ];
            if (!empty($email)) {
                $this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ParcelOutletRouting']['details'] = $email;
            }
        } else {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['ShipmentDetails']['Service']['ParcelOutletRouting']);
        }
    }
    
    
    public function setAllServices(array $servicesArray)
    {
        if (!empty($servicesArray['visualcheckofage'])) {
            $this->setServiceVisualCheckofAge(strtoupper($servicesArray['visualcheckofage']));
        }
        if (!empty($servicesArray['gogreen'])) {
            $this->setServiceGoGreen((bool)$servicesArray['gogreen'] === true);
        }
        if (!empty($servicesArray['returnreceipt'])) {
            $this->setServiceReturnReceipt((bool)$servicesArray['returnreceipt'] === true);
        }
        if (!empty($servicesArray['cdp']) && (bool)$servicesArray['cdp'] === true) {
            $this->setServiceCDP(true);
            $this->setServicePremium(false);
            $this->setServiceEconomy(false);
        } elseif (!empty($servicesArray['premium']) && (bool)$servicesArray['premium'] === true) {
            $this->setServicePremium(true);
            $this->setServiceCDP(false);
            $this->setServiceEconomy(false);
        } else {
            $this->setServiceEconomy(true);
            $this->setServicePremium(false);
            $this->setServiceCDP(false);
        }
        if (!empty($servicesArray['pddp'])) {
            $this->setServicePDDP((bool)$servicesArray['pddp']);
        }
        if (!empty($servicesArray['bulkygoods'])) {
            $this->setServiceBulkyGoods((bool)$servicesArray['bulkygoods'] === true);
        }
        if (!empty($servicesArray['cashondelivery'])) {
            $codAddFee = isset($servicesArray['cod_add_fee']) && (bool)$servicesArray['cod_add_fee'] === true;
            $this->setServiceCashOnDelivery((double)$servicesArray['cashondelivery'], $codAddFee);
        }
        if (!empty($servicesArray['additionalinsurance'])) {
            $this->setServiceAdditionalInsurance((double)$servicesArray['additionalinsurance']);
        }
        
        if (!empty($servicesArray['preferredneighbour'])) {
            $this->setServicePreferredNeighbour($servicesArray['preferredneighbour']);
        }
        if (!empty($servicesArray['preferredday'])) {
            $this->setServicePreferredDay($servicesArray['preferredday']);
        }
        if (!empty($servicesArray['preferredlocation'])) {
            $this->setServicePreferredLocation($servicesArray['preferredlocation']);
        }
        if (!empty($servicesArray['namedpersononly'])) {
            $this->setServiceNamedPersonOnly((bool)$servicesArray['namedpersononly'] === true);
        }
        if (!empty($servicesArray['signedforbyrecipient'])) {
            $this->setServiceSignedForByRecipient((bool)$servicesArray['signedforbyrecipient']);
        }
        if (!empty($servicesArray['identcheck']) && (bool)$servicesArray['identcheck'] === true) {
            $this->setServiceIdentCheck($servicesArray['identcheck_surname'],
                                        $servicesArray['identcheck_givenname'],
                                        $servicesArray['identcheck_dateofbirth'],
                                        $servicesArray['identcheck_minimumage']);
        }
        if (!empty($servicesArray['noneighbourdelivery'])) {
            $this->setServiceNoNeighbourDelivery((bool)$servicesArray['noneighbourdelivery'] === true);
        }
        if (!empty($servicesArray['packagingreturn'])) {
            $this->setServicePackagingReturn((bool)$servicesArray['packagingreturn'] === true);
        }
        if (!empty($servicesArray['returnimmediately'])) {
            $this->setServiceReturnImmediately((bool)$servicesArray['preferredtime'] === true);
        }
        if (!empty($servicesArray['parceloutletrouting'])) {
            $this->setServiceParcelOutletRouting((bool)$servicesArray['parceloutletrouting'],
                                                 (string)$servicesArray['parceloutletrouting_email']);
        }
    }
    
    
    /* === RETURNRECEIVER === */
    
    public function setReturnReceiverName($name1, $name2 = '', $name3 = '')
    {
        $maxLength = 50;
        $name1     = mb_substr($name1, 0, $maxLength);
        $name2     = mb_substr($name2, 0, $maxLength);
        $name3     = mb_substr($name3, 0, $maxLength);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['ReturnReceiver']['Name']['name1'] = $name1;
        $this->shipmentData['ShipmentOrder']['Shipment']['ReturnReceiver']['Name']['name2'] = $name2;
        $this->shipmentData['ShipmentOrder']['Shipment']['ReturnReceiver']['Name']['name3'] = $name3;
    }
    
    
    public function setReturnReceiverAddress(
        $streetName,
        $streetNumber,
        $addition,
        $zip,
        $city,
        $iso2,
        $state = '',
        $country = ''
    ) {
        $streetName   = mb_substr($streetName, 0, 50);
        $streetNumber = mb_substr($streetNumber, 0, 10);
        $addition     = mb_substr($addition, 0, 35);
        $zip          = mb_substr($zip, 0, 17);
        $city         = mb_substr($city, 0, 50);
        $iso2         = mb_substr($iso2, 0, 2);
        $state        = mb_substr($state, 0, 30);
        $country      = mb_substr($country, 0, 30);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['ReturnReceiver']['Address'] = [
            'streetName'             => $streetName,
            'streetNumber'           => $streetNumber,
            'addressAddition'        => $addition,
            'dispatchingInformation' => $addition,
            'zip'                    => $zip,
            'city'                   => $city,
            'Origin'                 => [
                'country'        => $country,
                'countryISOCode' => $iso2,
                'state'          => $state,
            ],
        ];
    }
    
    
    public function setReturnReceiverCommunication($phone = '', $email = '', $contactPerson = '')
    {
        $phone         = mb_substr($phone, 0, 20);
        $email         = mb_substr($email, 0, 50);
        $contactPerson = mb_substr($contactPerson, 0, 50);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['ReturnReceiver']['Communication'] = [
            'phone'         => $phone,
            'email'         => $email,
            'contactPerson' => $contactPerson,
        ];
    }
    
    
    /* === SHIPPER === */
    
    public function setShipperName($name1, $name2 = '', $name3 = '')
    {
        $maxLength = 50;
        $name1     = mb_substr($name1, 0, $maxLength);
        $name2     = mb_substr($name2, 0, $maxLength);
        $name3     = mb_substr($name3, 0, $maxLength);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Shipper']['Name']['name1'] = $name1;
        $this->shipmentData['ShipmentOrder']['Shipment']['Shipper']['Name']['name2'] = $name2;
        $this->shipmentData['ShipmentOrder']['Shipment']['Shipper']['Name']['name3'] = $name3;
    }
    
    
    public function setShipperAddress(
        $streetName,
        $streetNumber,
        $addition,
        $zip,
        $city,
        $iso2,
        $state = '',
        $country = ''
    ) {
        $streetName   = mb_substr($streetName, 0, 50);
        $streetNumber = mb_substr($streetNumber, 0, 10);
        $addition     = mb_substr($addition, 0, 35);
        $zip          = mb_substr($zip, 0, 17);
        $city         = mb_substr($city, 0, 50);
        $iso2         = mb_substr($iso2, 0, 2);
        $state        = mb_substr($state, 0, 30);
        $country      = mb_substr($country, 0, 30);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Shipper']['Address'] = [
            'streetName'             => $streetName,
            'streetNumber'           => $streetNumber,
            'addressAddition'        => $addition,
            'dispatchingInformation' => $addition,
            'zip'                    => $zip,
            'city'                   => $city,
            'Origin'                 => [
                'country'        => $country,
                'countryISOCode' => $iso2,
                'state'          => $state,
            ],
        ];
    }
    
    
    public function setShipperCommunication($phone = '', $email = '', $contactPerson = '')
    {
        $phone         = mb_substr($phone, 0, 20);
        $email         = mb_substr($email, 0, 50);
        $contactPerson = mb_substr($contactPerson, 0, 50);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Shipper']['Communication'] = [
            'phone'         => $phone,
            'email'         => $email,
            'contactPerson' => $contactPerson,
        ];
    }
    
    
    /**
     * Sets a ShipperReference.
     *
     * Note that setting a ShipperReference will override (unset) Shipper data, i.e. ShipmentOrder.Shipment.Shipper.
     *
     * @param string $shipperReference
     */
    public function setShipperReference(string $shipperReference): void
    {
        $shipperReference = mb_substr(trim($shipperReference), 0, 50);
        if (!empty($shipperReference)) {
            unset($this->shipmentData['ShipmentOrder']['Shipment']['Shipper']);
            $this->shipmentData['ShipmentOrder']['Shipment']['ShipperReference'] = $shipperReference;
        }
    }
    
    
    public function setReceiverName($name1)
    {
        $name1 = mb_substr($name1, 0, 50);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['name1'] = $name1;
    }
    
    
    public function setReceiverCommunication($phone = '', $email = '', $contactPerson = '')
    {
        $phone         = mb_substr($phone, 0, 20);
        $email         = mb_substr($email, 0, 50);
        $contactPerson = mb_substr($contactPerson, 0, 50);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Communication'] = [
            'phone'         => $phone,
            'email'         => $email,
            'contactPerson' => $contactPerson,
        ];
    }
    
    
    public function setReceiverAddress(
        $streetName,
        $streetNumber,
        $addition,
        $zip,
        $city,
        $iso2,
        $state = '',
        $country = ''
    ) {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Packstation']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Postfiliale']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['ParcelShop']);
        
        $streetName   = mb_substr($streetName, 0, 50);
        $streetNumber = mb_substr($streetNumber, 0, 10);
        $addition     = mb_substr($addition, 0, 35);
        $zip          = mb_substr($zip, 0, 17);
        $city         = mb_substr($city, 0, 50);
        $iso2         = mb_substr($iso2, 0, 2);
        $state        = mb_substr($state, 0, 30);
        $country      = mb_substr($country, 0, 30);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address'] = [
            'streetName'             => $streetName,
            'streetNumber'           => $streetNumber,
            'addressAddition'        => $addition,
            'dispatchingInformation' => $addition,
            'zip'                    => $zip,
            'city'                   => $city,
            'Origin'                 => [
                'country'        => $country,
                'countryISOCode' => $iso2,
                'state'          => $state,
            ],
        ];
    }
    
    
    public function setReceiverAdditionalNames($name2, $name3)
    {
        $name2 = mb_substr($name2, 0, 50);
        $name3 = mb_substr($name3, 0, 50);
        if (is_array($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address'])) {
            $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address']['name2'] = $name2;
            $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address']['name3'] = $name3;
        }
    }
    
    
    public function setReceiverPackstation(
        $packstationNumber,
        $zip,
        $city,
        $iso2,
        $postNumber = '',
        $country = '',
        $state = ''
    ) {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Postfiliale']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['ParcelShop']);
        
        $packstationNumber = sprintf('%03d',
                                     (int)$packstationNumber);
        $postNumber        = empty($postNumber) ? '' : (string)(int)$postNumber;
        $zip               = mb_substr($zip, 0, 10);
        $city              = mb_substr($city, 0, 35);
        $iso2              = mb_substr($iso2, 0, 2);
        $state             = mb_substr($state, 0, 30);
        $country           = mb_substr($country, 0, 30);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Packstation'] = [
            'postNumber'        => $postNumber,
            'packstationNumber' => $packstationNumber,
            'zip'               => $zip,
            'city'              => $city,
            'Origin'            => [
                'country'        => $country,
                'countryISOCode' => $iso2,
                'state'          => $state,
            ],
        ];
    }
    
    
    public function setReceiverPostfiliale(
        $postfilialNumber,
        $zip,
        $city,
        $iso2,
        $postNumber,
        $country = '',
        $state = ''
    ) {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Packstation']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['ParcelShop']);
        
        $postfilialNumber = sprintf('%03d',
                                    (int)$postfilialNumber);
        $postNumber       = (int)$postNumber;
        $zip              = mb_substr($zip, 0, 10);
        $city             = mb_substr($city, 0, 35);
        $iso2             = mb_substr($iso2, 0, 2);
        $state            = mb_substr($state, 0, 30);
        $country          = mb_substr($country, 0, 30);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Postfiliale'] = [
            'postNumber'       => $postNumber,
            'postfilialNumber' => $postfilialNumber,
            'zip'              => $zip,
            'city'             => $city,
            'Origin'           => [
                'country'        => $country,
                'countryISOCode' => $iso2,
                'state'          => $state,
            ],
        ];
    }
    
    
    /** @deprecated Parcel shop receiver no longer supported */
    public function setReceiverParcelShop(
        $parcelShopNumber,
        $zip,
        $city,
        $iso2,
        $streetName = '',
        $streetNumber = '',
        $country = '',
        $state = ''
    ) {
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Address']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Packstation']);
        unset($this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['Postfiliale']);
        
        $parcelShopNumber = mb_substr($parcelShopNumber, 0, 3);
        $zip              = mb_substr($zip, 0, 10);
        $city             = mb_substr($city, 0, 35);
        $iso2             = mb_substr($iso2, 0, 2);
        $streetName       = mb_substr($streetName, 0, 35);
        $streetNumber     = mb_substr($streetNumber, 0, 5);
        $country          = mb_substr($country, 0, 30);
        $state            = mb_substr($state, 0, 30);
        
        $this->shipmentData['ShipmentOrder']['Shipment']['Receiver']['ParcelShop'] = [
            'parcelShopNumber' => $parcelShopNumber,
            'streetName'       => $streetName,
            'streetNumber'     => $streetNumber,
            'zip'              => $zip,
            'city'             => $city,
            'Origin'           => [
                'country'        => $country,
                'countryISOCode' => $iso2,
                'state'          => $state,
            ],
        ];
    }
    
    
    public function setExportDocument(
        $exportType,
        $placeOfCommital,
        $additionalFee,
        $invNumber = '',
        $termsOfTrade = '',
        $permitNumber = '',
        $attestationNumber = '',
        $withElectronicExportNtfctn = null
    ) {
        if (!in_array((string)$exportType,
                      [
                          'EU',
                          'OTHER',
                          'PRESENT',
                          'COMMERCIAL_SAMPLE',
                          'DOCUMENT',
                          'RETURN_OF_GOODS',
                          'COMMERCIAL_GOODS',
                      ])) {
            throw new GeschaeftskundenversandShipmentInvalidExportTypeException();
        }
        if (!in_array((string)$termsOfTrade, ['DDP', 'DXV', 'DDU', 'DDX', ''])) {
            throw new GeschaeftskundenversandShipmentInvalidTermsOfTradeException();
        }
        if ($exportType === 'EU') {
            $exportType = '';
        }
        $this->shipmentData['ShipmentOrder']['Shipment']['ExportDocument'] = [
            'invoiceNumber'              => mb_substr($invNumber, 0, 35),
            'exportType'                 => $exportType,
            'exportTypeDescription'      => '',
            'placeOfCommital'            => mb_substr($placeOfCommital, 0, 35),
            'additionalFee'              => number_format((double)$additionalFee, 2, '.', ''),
            'permitNumber'               => mb_substr($permitNumber, 0, 10),
            'attestationNumber'          => mb_substr($attestationNumber, 0, 35),
            'WithElectronicExportNtfctn' => ['_' => '', 'active' => $withElectronicExportNtfctn === true ? '1' : '0'],
            'ExportDocPosition'          => [],
        ];
        if (!empty($termsOfTrade)) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ExportDocument']['termsOfTrade'] = $termsOfTrade;
        }
    }
    
    
    public function setExportTypeDescription($exportTypeDescription)
    {
        if (is_array($this->shipmentData['ShipmentOrder']['Shipment']['ExportDocument'])) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ExportDocument']['exportTypeDescription'] = mb_substr($exportTypeDescription,
                                                                                                                    0,
                                                                                                                    256);
        }
    }
    
    
    public function addExportDocPosition(
        $description,
        $countryCode,
        $customsTariffNumber,
        $amount,
        $netWeightKG,
        $customsValue
    ) {
        if (is_array($this->shipmentData['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition'])) {
            $this->shipmentData['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition'][] = [
                'description'         => mb_substr($description, 0, 256),
                'countryCodeOrigin'   => $countryCode,
                'customsTariffNumber' => mb_substr($customsTariffNumber, 0, 10),
                'amount'              => (int)$amount,
                'netWeightInKG'       => number_format((float)$netWeightKG, 2, '.', ''),
                'customsValue'        => number_format((float)$customsValue, 2, '.', ''),
            ];
        }
    }
    
    
    public function setPrintOnlyIfCodeable($onlyIfCodeable)
    {
        $this->shipmentData['ShipmentOrder']['PrintOnlyIfCodeable']['active'] = (bool)$onlyIfCodeable
                                                                                === true ? '1' : '0';
    }
    
    
    public function setLabelResponseType($type = 'URL')
    {
        $this->shipmentData['ShipmentOrder']['labelResponseType'] = $type === 'B64' ? 'B64' : 'URL';
    }
    
    
    /**
     * Sets label format.
     *
     * Valid formats are:
     * A4: common label laser printing A4 plain paper;
     * 910-300-700: common label laser printing 105 x 205 mm (A5 plain paper, 910-300-700);
     * 910-300-700-oz: common label laser printing 105 x 205 mm without additional barcode labels (A5 plain paper,
     * 910-300-700);
     * 910-300-300: common label laser printing 105 x 148 mm (A5 plain paper, 910-300-700);
     * 910-300-300-oz: common label laser printing 105 x 148 mm without additional barcode labels (A5 plain paper,
     * 910-300-300);
     * 910-300-710: common label laser printing 105 x 208 mm (910-300-710);
     * 910-300-600: common label thermal printing 103 x 199 mm (910-300-600, 910-300-610);
     * 910-300-400: common label thermal printing 103 x 150 mm (910-300-400, 910-300-410);
     * 100x70mm: 100 x 70 mm label (only for Warenpost and Warenpost International);
     *
     * @param string $format
     *
     * @return void
     */
    public function setLabelFormat(string $format = 'A4')
    {
        $validFormats = [
            'A4',
            '910-300-700',
            '910-300-700-oZ',
            '910-300-300',
            '910-300-300-oz',
            '910-300-710',
            '910-300-600',
            '910-300-400',
            '100x70mm',
        ];
        if (in_array($format, $validFormats, true)) {
            $this->shipmentData['labelFormat'] = $format;
        }
    }
    
    
    /**
     * Sets label format for return labels.
     *
     * Valid formats are:
     * A4: common label laser printing A4 plain paper;
     * 910-300-700: common label laser printing 105 x 205 mm (A5 plain paper, 910-300-700);
     * 910-300-700-oz: common label laser printing 105 x 205 mm without additional barcode labels (A5 plain paper,
     * 910-300-700);
     * 910-300-300: common label laser printing 105 x 148 mm (A5 plain paper, 910-300-700);
     * 910-300-300-oz: common label laser printing 105 x 148 mm without additional barcode labels (A5 plain paper,
     * 910-300-300);
     * 910-300-710: common label laser printing 105 x 208 mm (910-300-710);
     * 910-300-600: common label thermal printing 103 x 199 mm (910-300-600, 910-300-610);
     * 910-300-400: common label thermal printing 103 x 150 mm (910-300-400, 910-300-410);
     * 100x70mm: 100 x 70 mm label (only for Warenpost and Warenpost International);
     *
     * @param string $format
     *
     * @return void
     */
    public function setLabelFormatRetoure(string $format = 'A4')
    {
        $validFormats = [
            'A4',
            '910-300-700',
            '910-300-700-oZ',
            '910-300-300',
            '910-300-300-oz',
            '910-300-710',
            '910-300-600',
            '910-300-400',
            '100x70mm',
        ];
        if (in_array($format, $validFormats, true)) {
            $this->shipmentData['labelFormatRetoure'] = $format;
        }
    }
    
    
    public function setCombinedPrinting(bool $active = false)
    {
        $this->shipmentData['combinedPrinting'] = $active ? 'true' : 'false';
    }
    
}
