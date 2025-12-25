<?php
/* --------------------------------------------------------------
   VatValidationServiceProvider.php 2024-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\VatValidation;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Logging\LoggerBuilder;
use Gambio\Core\VatValidation\App\Validators\AustriaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\BelgiumVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\BulgariaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\CroatiaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\CyprusVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\CzechRepublicVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\DenmarkVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\EstoniaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\EuViesValidator;
use Gambio\Core\VatValidation\App\Validators\FinlandVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\FranceVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\GermanyVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\GreatBritainVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\GreeceVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\HungaryVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\IrelandVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\ItalyVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\LatviaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\LithuaniaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\LuxembourgVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\MaltaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\NetherlandsVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\PolandVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\PortugalVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\RomaniaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\SlovakiaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\SloveniaVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\SpainVatIdValidator;
use Gambio\Core\VatValidation\App\Validators\SwedenVatIdValidator;
use Gambio\Core\VatValidation\Services\VatValidationService;

/**
 * Class VatValidationServiceProvider
 *
 * @package Gambio\Core\VatValidation
 */
class VatValidationServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            VatValidationService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(VatValidationService::class, App\VatValidationService::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(EuViesValidator::class)
            ->addArgument(LoggerBuilder::class);
        
        $this->application->register(EuViesValidator::class)
            ->addArgument(LoggerBuilder::class)
            ->addArgument(Connection::class);
        
        $this->application->register(AustriaVatIdValidator::class);
        $this->application->register(BelgiumVatIdValidator::class);
        $this->application->register(BulgariaVatIdValidator::class);
        $this->application->register(CroatiaVatIdValidator::class);
        $this->application->register(CyprusVatIdValidator::class);
        $this->application->register(CzechRepublicVatIdValidator::class);
        $this->application->register(DenmarkVatIdValidator::class);
        $this->application->register(EstoniaVatIdValidator::class);
        $this->application->register(FinlandVatIdValidator::class);
        $this->application->register(FranceVatIdValidator::class);
        $this->application->register(GermanyVatIdValidator::class);
        $this->application->register(GreatBritainVatIdValidator::class);
        $this->application->register(GreeceVatIdValidator::class);
        $this->application->register(HungaryVatIdValidator::class);
        $this->application->register(IrelandVatIdValidator::class);
        $this->application->register(ItalyVatIdValidator::class);
        $this->application->register(LatviaVatIdValidator::class);
        $this->application->register(LithuaniaVatIdValidator::class);
        $this->application->register(LuxembourgVatIdValidator::class);
        $this->application->register(MaltaVatIdValidator::class);
        $this->application->register(NetherlandsVatIdValidator::class);
        $this->application->register(PolandVatIdValidator::class);
        $this->application->register(PortugalVatIdValidator::class);
        $this->application->register(RomaniaVatIdValidator::class);
        $this->application->register(SlovakiaVatIdValidator::class);
        $this->application->register(SloveniaVatIdValidator::class);
        $this->application->register(SpainVatIdValidator::class);
        $this->application->register(SwedenVatIdValidator::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        foreach ([
                     AustriaVatIdValidator::class,
                     BelgiumVatIdValidator::class,
                     BulgariaVatIdValidator::class,
                     CroatiaVatIdValidator::class,
                     CyprusVatIdValidator::class,
                     CzechRepublicVatIdValidator::class,
                     DenmarkVatIdValidator::class,
                     EstoniaVatIdValidator::class,
                     FinlandVatIdValidator::class,
                     FranceVatIdValidator::class,
                     GermanyVatIdValidator::class,
                     GreatBritainVatIdValidator::class,
                     GreeceVatIdValidator::class,
                     HungaryVatIdValidator::class,
                     IrelandVatIdValidator::class,
                     ItalyVatIdValidator::class,
                     LatviaVatIdValidator::class,
                     LithuaniaVatIdValidator::class,
                     LuxembourgVatIdValidator::class,
                     MaltaVatIdValidator::class,
                     NetherlandsVatIdValidator::class,
                     PolandVatIdValidator::class,
                     PortugalVatIdValidator::class,
                     RomaniaVatIdValidator::class,
                     SlovakiaVatIdValidator::class,
                     SloveniaVatIdValidator::class,
                     SpainVatIdValidator::class,
                     SwedenVatIdValidator::class,
                 ] as $manualValidator) {
            $this->application->inflect(VatValidationService::class)
                ->invokeMethod('registerVatIdValidator', [$manualValidator]);
        }
    }
}