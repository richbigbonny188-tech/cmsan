<?php
/* --------------------------------------------------------------
   ABInformationService.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\App;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyGlobalRepository;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyInformationService;

/**
 * Class ABInformationService
 *
 * @package GXModules\Gambio\Afterbuy\AfterbuyCommon\App
 */
class ABInformationService implements AfterbuyInformationService
{
    /**
     * @var AfterbuyGlobalRepository
     */
    private AfterbuyGlobalRepository $repository;
    
    
    /**
     * ABInformationService constructor.
     *
     * @param AfterbuyGlobalRepository $repository
     */
    public function __construct(AfterbuyGlobalRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isInstalledAndEnabled(): bool
    {
        $isInstalled       = $this->isInstalled();
        $isEnabled         = $this->isEnabled();
        $hasXmlCredentials = $this->repository->hasXmlCredentials();
        
        return $isInstalled && $isEnabled && $hasXmlCredentials;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isInstalled(): bool
    {
        return $this->repository->isInstalled();
    }
    
    
    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->repository->isEnabled();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLogLevel(): string
    {
        return $this->repository->getLogLevel();
    }
}