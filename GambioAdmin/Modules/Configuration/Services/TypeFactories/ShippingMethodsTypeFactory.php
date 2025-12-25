<?php
/* --------------------------------------------------------------
   ShippingMethodsTypeFactory.php 2023-06-21
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
use Doctrine\DBAL\Exception as DBALException;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class ShippingMethodsTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class ShippingMethodsTypeFactory implements TypeFactory
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
     * ShippingMethodsTypeFactory constructor.
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
        $params['items'] = $this->getShippingMethods();
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     */
    private function getShippingMethods(): array
    {
        $paymentMethods = [];
        foreach ($this->getInstalledModules() as $installedModule) {
            $phrase = 'MODULE_SHIPPING_' . strtoupper($installedModule) . '_TEXT_TITLE';
            $text   = $this->textManager->getPhraseText($phrase, $installedModule);
            if ($text === $phrase) {
                $text = $this->textManager->getPhraseText($phrase, 'shipping_' . $installedModule);
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
            $query            = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_SHIPPING_INSTALLED";';
            $installedModules = $this->db->executeQuery($query)->fetchAssociative()['value'];
            $installedModules = str_replace([';;', '.php'], [';', ''], $installedModules);
            $installedModules = explode(";", $installedModules);
            
            // filter empty items
            $installedModules = array_filter($installedModules,
                static function ($module): bool {
                    return !empty($module);
                });
        } catch (DBALException $e) {
            $installedModules = [];
        }
        
        return $installedModules;
    }
}