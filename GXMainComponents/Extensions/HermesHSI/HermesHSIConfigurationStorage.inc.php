<?php
/* --------------------------------------------------------------
   HermesHSIConfigurationStorage.inc.php 2020-04-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIConfigurationStorage extends ConfigurationStorage
{
    /** @var string namespace of the configuration_storage subtree */
    protected const CONFIG_STORAGE_NAMESPACE = 'modules/shipping/hermeshsi';
    
    protected $default_configuration;
    
    
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
        $this->default_configuration = $this->getDefaultConfiguration();
    }
    
    
    protected function getDefaultConfiguration(): array
    {
        return [
            'apiUser'                       => '',
            'apiPassword'                   => '',
            'testMode'                      => '0',
            'senderNameFirstname'           => '',
            'senderNameMiddlename'          => '',
            'senderNameLastname'            => '',
            'senderNameGender'              => 'O',
            'senderNameTitle'               => '',
            'senderAddressStreet'           => '',
            'senderAddressHouseNumber'      => '',
            'senderAddressZipCode'          => '',
            'senderAddressTown'             => '',
            'senderAddressCountryCode'      => '',
            'senderAddressAddressAddition'  => '',
            'senderAddressAddressAddition2' => '',
            'senderAddressAddressAddition3' => '',
            'orderStatusAfterSave'          => '-1',
            'orderStatusAfterPrint'         => '-1',
            'parcelServiceId'               => '-1',
            'labelDownloadMethod'           => 'inline', // inline (new tab)|attachment (download)
            'directDownload'                => '0',
            'parcelWeightMode'              => 'none', // none|products_weight|add_packing_weight
            'parcelDimensionPreset1Height'  => '100',
            'parcelDimensionPreset1Width'   => '270',
            'parcelDimensionPreset1Depth'   => '170',
            'parcelDimensionPreset2Height'  => '150',
            'parcelDimensionPreset2Width'   => '350',
            'parcelDimensionPreset2Depth'   => '340',
            'parcelDimensionPreset3Height'  => '250',
            'parcelDimensionPreset3Width'   => '550',
            'parcelDimensionPreset3Depth'   => '450',
            'parcelDimensionPreset4Height'  => '350',
            'parcelDimensionPreset4Width'   => '600',
            'parcelDimensionPreset4Depth'   => '500',
            'parcelDimensionPreset5Height'  => '450',
            'parcelDimensionPreset5Width'   => '700',
            'parcelDimensionPreset5Depth'   => '600',
        ];
    }
    
    
    public function get($key)
    {
        $dbValue = parent::get($key);
        if ($dbValue !== false) {
            return $dbValue;
        }
        
        return $this->default_configuration[$key] ?? false;
    }
    
    
    /**
     * Retrieves all keys/values from a given prefix namespace
     *
     * @param string $p_prefix
     *
     * @return array
     */
    public function get_all($p_prefix = ''): array
    {
        $values = [];
        foreach ($this->default_configuration as $key => $default_value) {
            $key_prefix = substr($key, 0, strlen($p_prefix));
            if ($key_prefix === $p_prefix) {
                $values[$key] = $default_value;
            }
        }
        $values = array_merge($values, parent::get_all($p_prefix));
        
        return $values;
    }
    
    
    public function set($key, $value): void
    {
        if (!array_key_exists($key, $this->default_configuration)) {
            throw new InvalidArgumentException('invalid configuration key');
        }
        
        $value = trim($value);
        switch ($key) {
            case 'apiUser':
            case 'apiPassword':
                $value = strip_tags(trim($value));
                break;
            case 'testMode':
            case 'directDownload':
                $value = (bool)$value === true ? '1' : '0';
                break;
            case 'orderStatusAfterSave':
            case 'orderStatusAfterPrint':
            case 'parcelServiceId':
                $value = max(-1, (int)$value);
                break;
            case 'labelDownloadMethod':
                $value = in_array($value, ['inline', 'attachment'], true) ? $value : 'inline';
                break;
            case 'parcelWeightMode':
                $value = in_array($value, ['none', 'products_weight', 'add_packing_weight'], true) ? $value : 'none';
                break;
            case 'parcelDimensionPreset1Height':
            case 'parcelDimensionPreset1Width' :
            case 'parcelDimensionPreset1Depth' :
            case 'parcelDimensionPreset2Height':
            case 'parcelDimensionPreset2Width' :
            case 'parcelDimensionPreset2Depth' :
            case 'parcelDimensionPreset3Height':
            case 'parcelDimensionPreset3Width' :
            case 'parcelDimensionPreset3Depth' :
            case 'parcelDimensionPreset4Height':
            case 'parcelDimensionPreset4Width' :
            case 'parcelDimensionPreset4Depth' :
            case 'parcelDimensionPreset5Height':
            case 'parcelDimensionPreset5Width' :
            case 'parcelDimensionPreset5Depth' :
                $value = abs((int)$value);
                break;
            default:
                $value = strip_tags($value);
        }
        
        parent::set($key, $value);
    }
    
}
