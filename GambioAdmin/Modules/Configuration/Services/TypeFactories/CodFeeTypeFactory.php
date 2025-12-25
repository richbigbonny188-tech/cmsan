<?php
/* --------------------------------------------------------------
 CodFeeFactory.php 2023-06-21
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
 * Class CodFeeFactory
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class CodFeeTypeFactory implements TypeFactory
{
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * CodFeeTypeFactory constructor.
     *
     * @param Connection  $connection
     * @param TextManager $textManager
     */
    public function __construct(Connection $connection, TextManager $textManager)
    {
        $this->connection  = $connection;
        $this->textManager = $textManager;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createType(array $params): Type
    {
        $shippingModules = $this->installedShippingModules();
        $fees            = $this->parseFee($params['value'] ?? '', $shippingModules);
        
        return Type::create('cod-fee', ['items' => $fees]);
    }
    
    
    /**
     * Processes $feeValue to be formatted into an Options compatible format.
     *
     * This function parses the $feeValue into an "text"-, "value"-key array and
     * filters not installed shipping modules from the final data set.
     *
     * @param string $feeValue
     * @param array  $shippingModules
     *
     * @return array
     */
    private function parseFee(string $feeValue, array $shippingModules): array
    {
        $options = [];
        $values  = explode('|', $feeValue);
        $count   = count($values);
        
        for ($i = 0; $i < $count; $i += 2) {
            $key = $values[$i];
            
            if (in_array($key, $shippingModules, true)) {
                $value = $values[$i + 1];
                $name  = $this->textManager->getPhraseText('MODULE_SHIPPING_' . strtoupper($key) . '_TEXT_TITLE',
                                                           $key);
                
                $options[] = [
                    'text'    => "{$name} ({$key})",
                    'value'   => $value,
                    'context' => ['key' => $key]
                ];
            }
        }
        
        return $options;
    }
    
    
    /**
     * Fetches installed shipping modules.
     *
     * This function fetches the installed shipping modules and returns
     * the identifiers of them.
     *
     * @return array
     */
    private function installedShippingModules(): array
    {
        $query     = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_SHIPPING_INSTALLED";';
        $freequery = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING";';
        
        try {
            $shippingModulesString = $this->connection->executeQuery($query)->fetchAssociative()['value'];
            $freeShippingActive    = (bool)$this->connection->executeQuery($freequery)->fetchAssociative()['value'];
        } catch (DBALException $e) {
            return [];
        }
        $shippingModules = explode(';', $shippingModulesString);
        if ($freeShippingActive) {
            $shippingModules[] = 'free';
        }
        
        return array_map(static function (string $element): string {
            return str_replace('.php', '', $element);
        },
            $shippingModules);
    }
}
