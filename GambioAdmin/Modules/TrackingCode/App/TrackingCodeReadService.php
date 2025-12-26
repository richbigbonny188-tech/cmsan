<?php
/* --------------------------------------------------------------
   TrackingCodeReadService.php 2020-09-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App;

use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFactory;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeReadService as TrackingCodeReadServiceInterface;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository;

/**
 * Class TrackingCodeService
 *
 * @package Gambio\Admin\Modules\TrackingCode
 */
class TrackingCodeReadService implements TrackingCodeReadServiceInterface
{
    /**
     * @var TrackingCodeRepository
     */
    private $repository;
    
    /**
     * @var TrackingCodeFactory
     */
    private $factory;
    
    
    /**
     * TrackingCodeReadService constructor.
     *
     * @param TrackingCodeRepository $repository
     * @param TrackingCodeFactory    $factory
     */
    public function __construct(TrackingCodeRepository $repository, TrackingCodeFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTrackingCodes(): TrackingCodes
    {
        return $this->repository->getAllTrackingCodes();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTrackingCodeById(int $id): TrackingCode
    {
        return $this->repository->getTrackingCodeById($this->factory->createTrackingCodeId($id));
    }
    
    
    /**
     * @inheritcDoc
     */
    public function getTrackingCodesByOrderId(int $orderId): TrackingCodes
    {
        return $this->repository->getTrackingCodesByOrderId($this->factory->createOrderId($orderId));
    }
}