<?php
/*--------------------------------------------------------------
   ProductListingVerificationServiceProvider.php 2023-05-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification;

use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\ErrorHandling\Services\ExceptionTransmitter as ExceptionTransmitterInterface;
use Gambio\Core\Verification\App\Data\VerificationLogWriter;
use Gambio\Core\Verification\App\VerificationRepository;
use Gambio\Core\Verification\App\VerificationService;
use Gambio\Core\Verification\Service\VerificationRepository as VerificationRepositoryInterface;
use Gambio\Core\Verification\Service\VerificationService as VerificationServiceInterface;

/**
 * Class VerificationServiceProvider
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification
 * @codeCoverageIgnore
 */
class VerificationServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [VerificationServiceInterface::class];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(VerificationLogWriter::class)->addArgument(CacheFactory::class);
        $this->application->register(VerificationRepositoryInterface::class, VerificationRepository::class)
            ->addArgument(VerificationLogWriter::class)
            ->addArgument(ExceptionTransmitterInterface::class);
        
        $this->application->register(VerificationServiceInterface::class, VerificationService::class)
            ->addArgument(VerificationRepositoryInterface::class);
    }
}