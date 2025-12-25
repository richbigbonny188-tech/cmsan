<?php
/* --------------------------------------------------------------
   GiftVouchersConfigurationStorage.inc.php 2023-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class GiftVouchersConfigurationStorage extends ConfigurationStorage
{
    protected const CONFIG_STORAGE_NAMESPACE = 'extensions/giftsystem';
    
    protected $default_configuration;
    
    
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
        $this->setDefaultConfiguration();
    }
    
    
    protected function setDefaultConfiguration(): void
    {
        $this->default_configuration = [
            'releaseOrderStatuses'       => '',
            'securityCodeLength'         => 10,
            'newSignupGiftVoucherAmount' => 0.0,
            'newSignupDiscountCoupon'    => '',
        ];
    }
    
    
    public function get($key)
    {
        $value = parent::get($key);
        if ($value === false && array_key_exists($key, $this->default_configuration)) {
            $value = $this->default_configuration[$key];
        }
        
        if ($key === 'releaseOrderStatuses') {
            if ($value !== '') {
                $value = explode('|', $value);
                $value = array_map(static function ($element) { return (int)$element; }, $value);
            } else {
                $value = [];
            }
        }
        
        if ($key === 'securityCodeLength') {
            $value = (int)(@constant('SECURITY_CODE_LENGTH') ?? $this->default_configuration['securityCodeLength']);
        }
        
        if ($key === 'newSignupGiftVoucherAmount') {
            $value = (float)(@constant('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT') ??
                             $this->default_configuration['newSignupGiftVoucherAmount']);
        }
        
        if ($key === 'newSignupDiscountCoupon') {
            $value = (string)(@constant('NEW_SIGNUP_DISCOUNT_COUPON') ??
                              $this->default_configuration['newSignupDiscountCoupon']);
        }
        
        return $value;
    }
    
    
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
    
    
    public function set($name, $value): void
    {
        switch ($name) {
            case 'releaseOrderStatuses':
                if (is_array($value)) {
                    $statuses = array_filter($value,
                        static function ($item) {
                            return is_numeric($item);
                        });
                    $value    = implode('|', $statuses);
                } else {
                    throw new \RuntimeException('releaseOrderStatuses value must be an array of numeric (int) values');
                }
                break;
            case 'securityCodeLength':
                $value = (int)$value;
                $value = ($value >= 5 && $value <= 16) ? $value : $this->default_configuration['securityCodeLength'];
                $this->updateLegacyConfiguration('SECURITY_CODE_LENGTH', $value);
                break;
            case 'newSignupGiftVoucherAmount':
                $value = (float)$value;
                $value = ($value >= 0.0
                          && $value <= 1000.0) ? $value : $this->default_configuration['newSignupGiftVoucherAmount'];
                $this->updateLegacyConfiguration('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', $value);
                break;
            case 'newSignupDiscountCoupon':
                $value = trim(strip_tags($value));
                $this->updateLegacyConfiguration('NEW_SIGNUP_DISCOUNT_COUPON', $value);
                break;
            default:
                $value = null;
        }
        
        if ($value === null) {
            return;
        }
        parent::set($name, $value);
    }
    
    
    protected function updateLegacyConfiguration($configurationKey, $configurationValue)
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->update('gx_configurations',
                    ['value' => $configurationValue],
                    ['key' => 'configuration/' . $configurationKey]);
    }
    
}
