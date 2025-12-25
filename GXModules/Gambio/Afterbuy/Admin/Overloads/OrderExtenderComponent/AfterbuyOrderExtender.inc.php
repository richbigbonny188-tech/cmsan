<?php
/* --------------------------------------------------------------
   AfterbuyOrderExtender.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderExportService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyOrderExtender
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Overloads\OrderExtenderComponent
 */
class AfterbuyOrderExtender extends AfterbuyOrderExtender_parent
{
    /**
     * @var array
     */
    public $v_output_buffer;
    
    
    /**
     * Proceed with the execution of the extender.
     *
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function proceed()
    {
        parent::proceed();
        
        if ((bool)gm_get_conf('MODULE_CENTER_GAMBIOAFTERBUY_INSTALLED') === true) {
            $afterbuyData = $this->getAfterbuyData((int)$_GET['oID']);
            
            $contentView = MainFactory::create('ContentView');
            $contentView->set_template_dir(DIR_FS_CATALOG . 'GXModules/Gambio/Afterbuy/Admin/Templates/');
            $contentView->set_content_template('order_info.html');
            $contentView->set_flat_assigns(true);
            $contentView->set_caching_enabled(false);
            $contentView->set_content_data('afterbuy_success', $afterbuyData['afterbuy_success']);
            $contentView->set_content_data('afterbuy_export_time', $this->getExportTime());
            $contentView->set_content_data('afterbuy_id', $afterbuyData['afterbuy_id']);
            $contentView->set_content_data('afterbuy_order_id', $afterbuyData['afterbuy_order_id']);
            
            $this->v_output_buffer                     = is_array($this->v_output_buffer) ? $this->v_output_buffer : [];
            $this->v_output_buffer['below_order_info'] = $contentView->get_html();
            
            $txt                                               = MainFactory::create('LanguageTextManager',
                                                                                     'afterbuy',
                                                                                     $_SESSION['languages_id']);
            $this->v_output_buffer['below_order_info_heading'] = $txt->get_text('order_info_heading');
            $this->addContent();
        }
    }
    
    
    /**
     * @param int $ordersId
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getAfterbuyData(int $ordersId): array
    {
        /** @var Connection $connection */
        $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $qb         = $connection->createQueryBuilder();
        $row        = $qb->select('o.afterbuy_success', 'o.afterbuy_id', 'ao.afterbuy_order_id')
            ->from('orders', 'o')
            ->leftJoin('o', 'afterbuy_orders', 'ao', 'o.orders_id = ao.order_id')
            ->where("o.orders_id = {$qb->createNamedParameter($ordersId, ParameterType::INTEGER)}")
            ->executeQuery()
            ->fetchAssociative();
        
        return [
            'afterbuy_success'  => $row['afterbuy_success'],
            'afterbuy_id'       => $row['afterbuy_id'],
            'afterbuy_order_id' => $row['afterbuy_order_id'],
        ];
    }
    
    
    /**
     * @return string|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getExportTime(): ?string
    {
        $orderId = $_GET['oID'] ?? null;
        if ($orderId === null) {
            return null;
        }
        
        $service   = LegacyDependencyContainer::getInstance()->get(AfterbuyOrderExportService::class);
        $timestamp = $service->getInitialExportTimestamp(new OrderId((int)$orderId));
        
        return date('d.m.Y H:i:s', $timestamp);
    }
}
