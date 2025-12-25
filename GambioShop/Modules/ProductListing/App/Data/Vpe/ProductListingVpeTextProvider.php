<?php
/*--------------------------------------------------------------
   ProductListingVpeTextProvider.php 2023-06-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Vpe;

use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingVpeTextProvider
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Vpe
 */
class ProductListingVpeTextProvider
{
    /**
     * ProductListingVpeTextProvider constructor.
     *
     * @param TextManager $textManager
     */
    public function __construct(private TextManager $textManager) { }
    
    
    /**
     * @param ListingSettings $listingSettings
     *
     * @return string
     */
    public function getPerPhrase(ListingSettings $listingSettings): string
    {
        $section = 'general';
        $phrase  = 'TXT_PER';
    
        return $this->textManager->getPhraseText($phrase, $section, $listingSettings->languageId());
    }
}