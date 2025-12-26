<?php
/*--------------------------------------------------------------
   ListingItemDownloadInformation.php 2023-03-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use JetBrains\PhpStorm\Pure;

/**
 * Class ListingItemDownloadInformation
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemDownloadInformation
{
    /**
     * @param bool $isDownloadable
     */
    private function __construct(
        private bool $isDownloadable
    ) {
    }
    
    
    /**
     * Create ListingItemDownloadInformation for a downloadable product
     *
     * @return ListingItemDownloadInformation
     */
    #[Pure]
    public static function createForDownloadProduct(): ListingItemDownloadInformation
    {
        return new static(true);
    }
    
    
    /**
     * Create ListingItemDownloadInformation for a regular product
     *
     * @return ListingItemDownloadInformation
     */
    #[Pure]
    public static function createForNoneDownloadProduct(): ListingItemDownloadInformation
    {
        return new static(false);
    }
    
    
    /**
     * @return bool
     */
    public function isDownloadable(): bool
    {
        return $this->isDownloadable;
    }
}