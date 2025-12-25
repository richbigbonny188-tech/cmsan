<?php
/* --------------------------------------------------------------
   ListingItemMetaSitemapFrequency.php 2022-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use InvalidArgumentException;

/**
 * Class ListingItemMetaSitemapFrequency
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemMetaSitemapFrequency
{
    private const VALID_FREQUENCIES = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never',
    ];
    private string $frequency;
    
    
    /**
     * ListingItemMetaSitemapFrequency constructor.
     *
     * @param string $frequency
     */
    public function __construct(string $frequency)
    {
        if (!in_array($frequency, static::VALID_FREQUENCIES, true)) {
            $frequenciesString = implode("', '", static::VALID_FREQUENCIES);
            $message           = "Invalid frequency ($frequency) provided. Valid frequencies are: '$frequenciesString'";
            throw new InvalidArgumentException($message);
        }
        $this->frequency = $frequency;
    }
    
    
    /**
     * @return string
     */
    public function frequency(): string
    {
        return $this->frequency;
    }
}