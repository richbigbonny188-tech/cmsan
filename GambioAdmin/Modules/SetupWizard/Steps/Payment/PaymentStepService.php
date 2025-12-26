<?php
/*------------------------------------------------------------------------------
 SetupWizardCatalogStepService.php 2020-08-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Payment;

use Curl\Curl;
use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\Services\AbstractSetupWizardStepService;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader\PaymentModuleReader;
use Gambio\Admin\Modules\SetupWizard\Storage\StepIsDoneStorageInterface;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\Hub\HubApiClientFactory;

/**
 * Class PaymentStepService
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Payment
 */
class PaymentStepService extends AbstractSetupWizardStepService
{
    /**
     * @var HubApiClientFactory
     */
    protected $hubFactory;
    
    /**
     * @var PaymentModuleReader
     */
    protected $reader;
    
    /**
     * @var PaymentStepDoneCommand
     */
    protected $command;
    
    /**
     * @var Path
     */
    protected $path;
    
    /**
     * @var Curl
     */
    protected $curl;
    
    
    public function __construct(
        PaymentStepKey $key,
        StepIsDoneStorageInterface $storage,
        TextManager $textManager,
        PaymentModuleReader $paymentModuleReader,
        PaymentStepDoneCommand $command,
        Path $path,
        HubApiClientFactory $hubFactory,
        Curl $curl
    ) {
        parent::__construct($key, $storage, $textManager);
        
        $this->reader     = $paymentModuleReader;
        $this->command    = $command;
        $this->path       = $path;
        $this->hubFactory = $hubFactory;
        $this->curl       = $curl;
    }
    
    
    public function getStep(): SetupWizardStep
    {
        $paymentStatus = $this->storage->getValue();
        
        if ($paymentStatus->value()) {
            return $this->createPaymentStep($paymentStatus);
        }
        
        $modules        = $this->reader->paymentMethods();
        $paymentModules = str_replace(['gambio_hub.php', ';'], '', $modules->paymentMethods());
        
        if ($paymentModules !== '') {
            $this->command->execute();
        }
        
        if ($this->reader->hubClientKey()->clientKey() === '') {
            return $this->createPaymentStep($this->storage->getValue());
        }
        
        $session = $this->hubFactory->createHubSession();
        if ($session) {
            $url      = 'https://core-api.gambiohub.com/api.php/api/v1/sessions/' . $session . '/payment_modules';
            
            $response = $this->curl->get($url);
            
            if ($response !== '') {
                $this->storage->setStepComplete();
            }
        }
        
        return $this->createPaymentStep($this->storage->getValue());
    }
    
    
    /**
     * @param Status $status
     *
     * @return PaymentStep
     */
    protected function createPaymentStep(Status $status): PaymentStep
    {
        return new PaymentStep($status,
                               new Index(6),
                               new Title($this->textManager->getPhraseText('STEP_PAYMENT_MODULE_HEADLINE', 'setup_wizard')),
                               new Text($this->textManager->getPhraseText('STEP_PAYMENT_MODULE_DESCRIPTION', 'setup_wizard')),
                               $this->key);
    }
}