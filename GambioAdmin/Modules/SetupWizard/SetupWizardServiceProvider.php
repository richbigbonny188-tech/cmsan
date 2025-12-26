<?php
/*------------------------------------------------------------------------------
 SetupWizardServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard;

use Curl\Curl;
use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\SetupWizard\Commands\Done\UploadLogoStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings\BasicSettingsStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings\BasicSettingsStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings\BasicSettingsStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings\BasicStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\Catalog\CatalogStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\Catalog\CatalogStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\Catalog\CatalogStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\Catalog\CatalogStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\Design\DesignStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\Design\DesignStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\Design\DesignStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\Design\DesignStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections\TermsAndConditionTextHashesCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections\WithdrawalTextHashesCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\LegalTextStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\LegalTextStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\LegalTextStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\LegalTextStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Reader\LegalTextReader;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Reader\MysqlLegalTextReader;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\Hub\HubApiClientFactory;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\Hub\HubApiClientFactoryAdapter;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\PaymentStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\PaymentStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\PaymentStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\PaymentStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader\BasicPaymentModuleReader;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader\PaymentModuleReader;
use Gambio\Admin\Modules\SetupWizard\Steps\Shipping\ShippingStepDoneCommand;
use Gambio\Admin\Modules\SetupWizard\Steps\Shipping\ShippingStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\Shipping\ShippingStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\Shipping\ShippingStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader\MySqlLogoReader;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader\UploadLogoReader;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\UploadLogoStepIsDoneStorage;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\UploadLogoStepKey;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\UploadLogoStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects\LogoDirectoryPath;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects\LogoProperties;
use Gambio\Admin\Modules\SetupWizard\Storage\SetupWizardStorage;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\HideSetupWizardKey;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\Application\ValueObjects\Environment;

class SetupWizardServiceProvider extends AbstractServiceProvider
{
    
    public function provides(): array
    {
        return [SetupWizardServiceInterface::class];
    }
    
    
    public function register(): void
    {
        $this->registerBasicSettings();
        $this->registerCatalog();
        $this->registerDesign();
        $this->registerLegalText();
        $this->registerPayment();
        $this->registerShipping();
        $this->registerUploadLogo();
        
        $this->application->registerShared(SetupWizardStorage::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(SetupWizardServiceInterface::class, SetupWizardService::class)
            ->addArgument(SetupWizardStorage::class)
            ->addArgument(BasicSettingsStepService::class)
            ->addArgument(CatalogStepService::class)
            ->addArgument(DesignStepService::class)
            ->addArgument(LegalTextStepService::class)
            ->addArgument(PaymentStepService::class)
            ->addArgument(ShippingStepService::class)
            ->addArgument(UploadLogoStepService::class);
    }
    
    
    protected function registerBasicSettings(): void
    {
        $key = new BasicStepKey();
        $this->application->registerShared(BasicSettingsStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(BasicSettingsStepDoneCommand::class)
            ->addArgument(BasicSettingsStepIsDoneStorage::class);
        
        $this->application->registerShared(BasicSettingsStepService::class)
            ->addArgument($key)
            ->addArgument(BasicSettingsStepIsDoneStorage::class)
            ->addArgument(TextManager::class);
    }
    
    
    protected function registerCatalog(): void
    {
        $key = new CatalogStepKey();
        $this->application->registerShared(CatalogStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(CatalogStepDoneCommand::class)->addArgument(CatalogStepIsDoneStorage::class);
        
        $this->application->registerShared(CatalogStepService::class)
            ->addArgument($key)
            ->addArgument(CatalogStepIsDoneStorage::class)
            ->addArgument(TextManager::class);
    }
    
    
    protected function registerDesign(): void
    {
        $key = new DesignStepKey();
        $this->application->registerShared(DesignStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(DesignStepDoneCommand::class)->addArgument(DesignStepIsDoneStorage::class);
        
        $this->application->registerShared(DesignStepService::class)
            ->addArgument($key)
            ->addArgument(DesignStepIsDoneStorage::class)
            ->addArgument(TextManager::class);
    }
    
    
    protected function registerLegalText(): void
    {
        $key = new LegalTextStepKey();
        $this->application->registerShared(LegalTextStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(LegalTextStepDoneCommand::class)
            ->addArgument(LegalTextStepIsDoneStorage::class);
        
        $this->application->registerShared(WithdrawalTextHashesCollection::class)->addArgument(Environment::class);
        $this->application->registerShared(TermsAndConditionTextHashesCollection::class)->addArgument(Environment::class);
        $this->application->registerShared(LegalTextReader::class, MysqlLegalTextReader::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(LegalTextStepService::class)
            ->addArgument($key)
            ->addArgument(LegalTextStepIsDoneStorage::class)
            ->addArgument(TextManager::class)
            ->addArgument(WithdrawalTextHashesCollection::class)
            ->addArgument(TermsAndConditionTextHashesCollection::class)
            ->addArgument(LegalTextReader::class);
    }
    
    
    protected function registerPayment(): void
    {
        $key = new PaymentStepKey();
        $this->application->registerShared(PaymentStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(PaymentModuleReader::class, BasicPaymentModuleReader::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(PaymentStepDoneCommand::class)->addArgument(PaymentStepIsDoneStorage::class);
        
        $this->application->registerShared(HubApiClientFactory::class, HubApiClientFactoryAdapter::class);
        
        $this->application->registerShared(PaymentStepService::class)
            ->addArgument($key)
            ->addArgument(PaymentStepIsDoneStorage::class)
            ->addArgument(TextManager::class)
            ->addArgument(PaymentModuleReader::class)
            ->addArgument(PaymentStepDoneCommand::class)
            ->addArgument(Path::class)
            ->addArgument(HubApiClientFactory::class)
            ->addArgument(Curl::class);
    }
    
    
    protected function registerShipping(): void
    {
        $key = new ShippingStepKey();
        $this->application->registerShared(ShippingStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(ShippingStepDoneCommand::class)
            ->addArgument(ShippingStepIsDoneStorage::class);
        
        $this->application->registerShared(ShippingStepService::class)
            ->addArgument($key)
            ->addArgument(ShippingStepIsDoneStorage::class)
            ->addArgument(TextManager::class);
    }
    
    
    protected function registerUploadLogo(): void
    {
        $key = new UploadLogoStepKey;
        $this->application->registerShared(UploadLogoStepIsDoneStorage::class)
            ->addArgument($key)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(UploadLogoStepDoneCommand::class)
            ->addArgument(UploadLogoStepIsDoneStorage::class);
        
        $this->application->registerShared(UploadLogoReader::class, MySqlLogoReader::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(LogoDirectoryPath::class);
        $this->application->registerShared(LogoProperties::class)->addArgument(Environment::class);
        
        $this->application->registerShared(UploadLogoStepService::class)
            ->addArgument($key)
            ->addArgument(UploadLogoReader::class)
            ->addArgument(UploadLogoStepIsDoneStorage::class)
            ->addArgument(UploadLogoStepDoneCommand::class)
            ->addArgument(LogoDirectoryPath::class)
            ->addArgument(LogoProperties::class)
            ->addArgument(TextManager::class);
    }
    
}