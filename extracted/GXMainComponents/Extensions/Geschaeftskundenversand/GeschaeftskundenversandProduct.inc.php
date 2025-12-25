<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandProduct.inc.php 2022-03-04
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class GeschaeftskundenversandProduct
 *
 * Represents a product configuration (product and attendance code) for DHL Business Customer API
 *
 * @category   System
 * @package    Extensions
 * @subpackage Geschaeftskundenversand
 */
class GeschaeftskundenversandProduct
{
    protected $type;
    protected $attendance;
    protected $alias;
    
    
    /**
     * GeschaeftskundenversandProduct constructor.
     *
     * @param mixed  $type
     * @param mixed  $attendance
     * @param string $alias
     *
     * @throws InvalidGKVAttendanceFormatException
     * @throws InvalidGKVProductTypeException
     */
    public function __construct($type, $attendance, $alias = '')
    {
        $this->setType($type);
        $this->setAttendance($attendance);
        $this->setAlias($alias);
    }
    
    
    public static function getValidTypes()
    {
        $validTypes = [
            'dhl_paket',
            'dhl_paket_tag',
            'dhl_paket_intl',
            'dhl_europaket',
            'dhl_paket_connect',
            'dhl_paket_at',
            'dhl_paket_at_connect',
            'dhl_paket_at_intl',
            'dhl_warenpost',
            'dhl_warenpost_intl',
        ];
        
        return $validTypes;
    }
    
    
    public static function getDeprecatedTypes()
    {
        $deprecatedTypes = [
            'dhl_kurier_tag',
            'dhl_kurier_wunsch',
            'dhl_paket_prio',
        ];
        
        return $deprecatedTypes;
    }
    
    
    public function setType($type)
    {
        $validTypes = array_merge(self::getValidTypes(), self::getDeprecatedTypes());
        if (!in_array($type, $validTypes)) {
            throw new InvalidGKVProductTypeException((string)$type);
        }
        $this->type = $type;
    }
    
    
    public function getType()
    {
        return $this->type;
    }
    
    
    public function isDeprecated()
    {
        $isDeprecated = in_array($this->type, self::getDeprecatedTypes());
        
        return $isDeprecated;
    }
    
    
    public function getTargetArea()
    {
        $areaMap    = [
            'dhl_paket'            => 'domestic',
            'dhl_paket_prio'       => 'domestic',
            'dhl_paket_tag'        => 'domestic',
            'dhl_paket_intl'       => 'international',
            'dhl_europaket'        => 'europe',
            'dhl_paket_connect'    => 'international',
            'dhl_kurier_tag'       => 'domestic',
            'dhl_kurier_wunsch'    => 'domestic',
            'dhl_paket_at'         => 'domestic',
            'dhl_paket_at_connect' => 'europe',
            'dhl_paket_at_intl'    => 'international',
            'dhl_warenpost'        => 'domestic',
            'dhl_warenpost_intl'   => 'international',
        ];
        $targetArea = $areaMap[$this->type];
        
        return $targetArea;
    }
    
    
    public function setAttendance($attendance)
    {
        if (preg_match('/^[[:alnum:]]{2}$/', $attendance) !== 1) {
            throw new InvalidGKVAttendanceFormatException();
        }
        $this->attendance = $attendance;
    }
    
    
    public function getAttendance()
    {
        return $this->attendance;
    }
    
    
    public function setAlias($alias)
    {
        $this->alias = strip_tags(trim((string)$alias));
    }
    
    
    public function getAlias()
    {
        return (string)$this->alias;
    }
    
    
    public function getApiProductCode()
    {
        $mapping     = [
            'dhl_paket'            => 'V01PAK',
            'dhl_paket_prio'       => 'V01PRIO', // deprecated
            'dhl_paket_tag'        => 'V06PAK',
            'dhl_paket_intl'       => 'V53WPAK',
            'dhl_europaket'        => 'V54EPAK',
            'dhl_paket_connect'    => 'V55PAK',
            'dhl_kurier_tag'       => 'V06TG', // deprecated?
            'dhl_kurier_wunsch'    => 'V06WZ', // deprecated?
            'dhl_paket_at'         => 'V86PARCEL',
            'dhl_paket_at_connect' => 'V87PARCEL',
            'dhl_paket_at_intl'    => 'V82PARCEL',
            'dhl_warenpost'        => 'V62WP',
            'dhl_warenpost_intl'   => 'V66WPI',
        ];
        $productCode = array_key_exists($this->type, $mapping) ? $mapping[$this->type] : 'INVALID_TYPE';
        
        return $productCode;
    }
    
    
    public function getProcedure()
    {
        $mapping   = [
            'dhl_paket'            => '01',
            'dhl_paket_prio'       => '01',
            'dhl_paket_tag'        => '06',
            'dhl_paket_intl'       => '53',
            'dhl_europaket'        => '54',
            'dhl_paket_connect'    => '55',
            'dhl_kurier_tag'       => '01',
            'dhl_kurier_wunsch'    => '01',
            'dhl_paket_at'         => '86',
            'dhl_paket_at_connect' => '87',
            'dhl_paket_at_intl'    => '82',
            'dhl_warenpost'        => '62',
            'dhl_warenpost_intl'   => '66',
        ];
        $procedure = array_key_exists($this->type, $mapping) ? $mapping[$this->type] : 'INVALID_TYPE';
        
        return $procedure;
    }
    
    
    public static function getServicesMatrix()
    {
        $services = [
            'dhl_paket'            => [
                'AdditionalInsurance',
                'BulkyGoods',
                'CashOnDelivery',
                'GoGreen',
                'IdentCheck',
                'IndividualSenderRequirement',
                'NamedPersonOnly',
                'NoNeighbourDelivery',
                'NoticeOfNonDeliverability',
                'Notification',
                'PackagingReturn',
                'Personally',
                'PreferredDay',
                'PreferredLocation',
                'PreferredNeighbour',
                'PreferredTime',
                'ReturnReceiver',
                'VisualCheckOfAge',
                'ParcelOutletRouting',
            ],
            'dhl_paket_prio'       => [
                'AdditionalInsurance',
                'CashOnDelivery',
                'GoGreen',
                'IdentCheck',
                'IndividualSenderRequirement',
                'NamedPersonOnly',
                'NoNeighbourDelivery',
                'NoticeOfNonDeliverability',
                'Notification',
                'PackagingReturn',
                'PreferredDay',
                'PreferredLocation',
                'PreferredNeighbour',
                'PreferredTime',
                'ReturnReceiver',
                'VisualCheckOfAge',
            ],
            'dhl_paket_tag'        => [
                'AdditionalInsurance',
                'BulkyGoods',
                'CashOnDelivery',
                'GoGreen',
                'IdentCheck',
                'IndividualSenderRequirement',
                'NamedPersonOnly',
                'NoNeighbourDelivery',
                'NoticeOfNonDeliverability',
                'Notification',
                'PackagingReturn',
                'PreferredDay',
                'PreferredLocation',
                'PreferredNeighbour',
                'PreferredTime',
                'ReturnImmediately',
                'ReturnReceiver',
                'VisualCheckOfAge',
                'ParcelOutletRouting',
            ],
            'dhl_paket_intl'       => [
                'AdditionalInsurance',
                'BulkyGoods',
                'CashOnDelivery',
                'GoGreen',
                'Notification',
                'Premium',
                'ReturnReceipt',
            ],
            'dhl_europaket'        => [
                'AdditionalInsurance',
                'GoGreen',
                'Notification',
            ],
            'dhl_paket_connect'    => [
                'AdditionalInsurance',
                'BulkyGoods',
                'GoGreen',
                'Notification',
                'ReturnReceiver',
            ],
            'dhl_kurier_tag'       => [
                'AdditionalInsurance',
                'DayOfDelivery',
                'DeliveryTimeframe',
                'Endorsement',
                'GoGreen',
                'IndividualSenderRequirement',
                'Notification',
                'Perishables',
                'ReturnReceiver',
                'ShipmentHandling',
            ],
            'dhl_kurier_wunsch'    => [
                'AdditionalInsurance',
                'DayOfDelivery',
                'DeliveryTimeframe',
                'Endorsement',
                'GoGreen',
                'IndividualSenderRequirement',
                'Notification',
                'Perishables',
                'ReturnReceiver',
                'ShipmentHandling',
            ],
            'dhl_paket_at'         => [
                'AdditionalInsurance',
                'BulkyGoods',
                'CashOnDelivery',
                'Notification',
            ],
            'dhl_paket_at_connect' => [
                'AdditionalInsurance',
                'BulkyGoods',
                'CashOnDelivery',
                'Notification',
            ],
            'dhl_paket_at_intl'    => [
                'AdditionalInsurance',
                'BulkyGoods',
                'Endorsement',
                'Notification',
            ],
            'dhl_warenpost' => [
                'PreferredLocation',
                'PreferredNeighbour',
                'ParcelOutletRouting',
                'GoGreen',
                'Notification',
                'ReturnReceiver',
            ],
            'dhl_warenpost_intl' => [
                'GoGreen',
                'Notification',
                'Premium',
            ],
        ];
        
        return $services;
    }
    
    
    public function getServices()
    {
        $servicesMatrix = self::getServicesMatrix();
        $services       = $servicesMatrix[$this->getType()];
        
        return $services;
    }
}
