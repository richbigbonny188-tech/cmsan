<?php
/* --------------------------------------------------------------
   PaymentMethodTypeFactory.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Core\TextManager\Services\TextManager;
use GXModules\Gambio\Hub\Admin\Classes\Extensions\HubPaymentMethodProvider;

/**
 * Class PaymentMethodTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class PaymentMethodTypeFactory implements TypeFactory
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * PaymentMethodTypeFactory constructor.
     *
     * @param Connection  $db
     * @param TextManager $textManager
     */
    public function __construct(Connection $db, TextManager $textManager)
    {
        $this->db          = $db;
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param array $params
     *
     * @return Type
     */
    public function createType(array $params): Type
    {
        $id              = (isset($params['multiSelect']) && $params['multiSelect']) ? 'multi-select' : 'dropdown';
        $params['items'] = $this->getPaymentMethods();
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     */
    private function getPaymentMethods(): array
    {
        $paymentMethods = [];
        foreach ($this->getInstalledModules() as $installedModule) {
            if ($installedModule === 'gambio_hub') {
                if (class_exists(HubPaymentMethodProvider::class)) {
                    try {
                        $hubPayments = HubPaymentMethodProvider::getPaymentMethods($this->db);
                    } catch (\Exception $exception) {
                        $hubPayments = [];
                    }
                    $paymentMethods = array_merge($paymentMethods, $hubPayments);
                }
                continue;
            }
            
            $phrase = 'MODULE_PAYMENT_' . strtoupper($installedModule) . '_TEXT_TITLE';
            $text   = $this->textManager->getPhraseText($phrase, $installedModule);
            if ($text === $phrase) {
                $text = $this->textManager->getPhraseText($phrase, $installedModule . '_module');
            }
            
            $paymentMethods[] = [
                'value' => $installedModule,
                'text'  => $text,
            ];
        }
        
        return $paymentMethods;
    }
    
    
    private function getInstalledModules(): array
    {
        try {
            $query            = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_PAYMENT_INSTALLED";';
            $installedModules = $this->db->fetchAssociative($query)['value'];
            $installedModules = str_replace([';;', '.php'], [';', ''], $installedModules);
            $installedModules = explode(";", $installedModules);
            
            // filter empty items
            $installedModules = array_filter($installedModules,
                static function ($module): bool {
                    return !empty($module);
                });
            
            // Add cod if array is empty
            if (count($installedModules) === 0) {
                $installedModules[] = 'cod';
            }
        } catch (Exception $e) {
            $installedModules = ['cod'];
        }
        
        return $installedModules;
    }
}