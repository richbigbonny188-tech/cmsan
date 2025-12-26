<?php
/* --------------------------------------------------------------
   AfterbuyGlobalRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\AfterbuyGlobal;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\AfterbuyGlobal;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyGlobalRepository as ABGlobalRepository;

/**
 * Class AfterbuyGlobalRepository
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyGlobal
 */
class AfterbuyGlobalRepository implements ABGlobalRepository
{
    /**
     * @var AfterbuyGlobalReader
     */
    private AfterbuyGlobalReader $reader;
    
    
    /**
     * AfterbuyGlobalRepositoryBackup constructor.
     *
     * @param AfterbuyGlobalReader $reader
     */
    public function __construct(AfterbuyGlobalReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAfterbuyGlobal(string $callName): AfterbuyGlobal
    {
        if (!$this->reader->isInstalled()) {
            $message = 'The Afterbuy module is not installed! Please visit the Module-Center in the GambioAdmin and install the module';
            throw new AfterbuyNotInstalledException($message, 403);
        }
        if (!$this->reader->isEnabled()) {
            $message = 'The Afterbuy module is not enabled! Please visit the Module-Center in the GambioAdmin and enable the module';
            throw new AfterbuyNotEnabledException($message, 403);
        }
        $partnerToken = $this->reader->fetchPartnerToken();
        $accountToken = $this->reader->fetchAccountToken();
        
        return new AfterbuyGlobal($partnerToken, $accountToken, $callName);
    }
    
    
    /**
     * @inheritDoc
     */
    public function isInstalled(): bool
    {
        return $this->reader->isInstalled();
    }
    
    
    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->reader->isEnabled();
    }
    
    
    /**
     * @inheritDoc
     */
    public function hasXmlCredentials(): bool
    {
        $hasPartnerToken = $this->reader->fetchPartnerToken() !== '';
        $hasAccountToken = $this->reader->fetchAccountToken() !== '';
        
        return $hasPartnerToken && $hasAccountToken;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLogLevel(): string
    {
        return $this->reader->fetchLogLevel();
    }
}