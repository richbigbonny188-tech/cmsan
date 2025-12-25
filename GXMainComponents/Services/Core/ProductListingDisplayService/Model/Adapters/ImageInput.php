<?php
/* --------------------------------------------------------------
  ImageInput.php 2023-04-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\HtmlOutput;

/**
 * Class ImageInput
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class ImageInput implements HtmlOutput
{
    use HtmlUtilityTrait;


    /**
     * @param string $src
     * @param string $alt
     * @param array  $attributes
     */
    public function __construct(
        private string $src,
        private string $alt = '',
        private array  $attributes = []
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function toHtml(): string
    {
        $attributes = $this->flattenInputAttributes($this->attributes);

        return sprintf(
            '<input type="image" src="%s" alt="%s" title=" %s " %s/>',
            //@todo remove leading space once Gambio\Core\Verification\Service\VerificationService is not being used and data is being returned directly to consumers.
            $this->src,
            $this->alt,
            $this->alt,
            $attributes ? "$attributes " : ''
        );
    }
}