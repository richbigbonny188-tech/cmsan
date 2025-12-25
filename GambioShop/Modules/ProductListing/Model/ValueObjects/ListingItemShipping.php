<?php
/* --------------------------------------------------------------
  ListingItemShipping.php 2023-05-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingItemShipping
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemShipping
{
    private int                       $days;
    private string                    $name;
    private string                    $image;
    private bool                      $linkIsActive;
    private ?ListingItemShippingRange $range;


    /**
     * @param int                           $days
     * @param string                        $name
     * @param string                        $image
     * @param bool                          $linkIsActive
     * @param ListingItemShippingRange|null $range
     */
    public function __construct(
        int                       $days,
        string                    $name,
        string                    $image,
        bool                      $linkIsActive,
        ?ListingItemShippingRange $range
    )
    {
        $this->days         = $days;
        $this->name         = $name;
        $this->image        = $image;
        $this->linkIsActive = $linkIsActive;
        $this->range        = $range;
    }


    /**
     * @return int
     */
    public function days(): int
    {
        return $this->days;
    }


    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function image(): string
    {
        return $this->image;
    }


    /**
     * @return bool
     */
    public function linkIsActive(): bool
    {
        return $this->linkIsActive;
    }


    /**
     * @return ListingItemShippingRange|null
     */
    public function range(): ?ListingItemShippingRange
    {
        return $this->range;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'days'         => $this->days(),
            'name'         => $this->name(),
            'image'        => $this->image(),
            'linkIsActive' => $this->linkIsActive(),
            'range'        => is_null($this->range) ? null : $this->range()->toArray(),
        ];
    }
}