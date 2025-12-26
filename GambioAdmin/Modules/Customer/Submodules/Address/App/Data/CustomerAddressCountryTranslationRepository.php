<?php
/*--------------------------------------------------------------------------------------------------
    CustomerCountryTranslationRepository.php 2023-02-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\Data;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressCountry;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressCountryTranslationRepository as CustomerCountryTranslationRepositoryInterface;
use Gambio\Core\TextManager\Services\TextManager;

/**
 *
 */
class CustomerAddressCountryTranslationRepository implements CustomerCountryTranslationRepositoryInterface
{
    /**
     * @var CustomerAddressFactory
     */
    private CustomerAddressFactory $addressFactory;
    
    /**
     * @var TextManager
     */
    private TextManager $textManager;
    
    
    /**
     * @param CustomerAddressFactory $addressFactory
     */
    public function __construct(TextManager $textManager, CustomerAddressFactory $addressFactory)
    {
        $this->addressFactory = $addressFactory;
        $this->textManager    = $textManager;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerCountryByIsoCode2(string $countryIsoCode2): CustomerAddressCountry
    {
        $countryName = $this->textManager->getPhraseText($countryIsoCode2, 'countries');
        
        return $this->addressFactory->createCustomerAddressCountry($countryName, $countryIsoCode2);
    }
}