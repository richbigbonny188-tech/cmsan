<?php
/* --------------------------------------------------------------
  HtmlUtilityTrait.php 2023-01-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

/**
 * Trait HtmlUtilityTrait
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
trait HtmlUtilityTrait
{
    /**
     * Sanitize array of attributes and flatten for HTML display.
     *
     * @param array $attributes
     *
     * @return string
     */
    private function prepareHtmlAttributes(array $attributes): string
    {
        return $this->flattenInputAttributes($attributes);
    }


    /**
     * @source src/inc/xtc_parse_input_field_data.inc.php
     *
     * @param array $attributes
     *
     * @return string
     */
    private function flattenInputAttributes(array $attributes): string
    {
        return implode(
            ' ',
            array_map(
                function ($k, $v): string {
                    $return = '';
                    $key    = trim($k);
                    $value  = $this->parseAttributeValue($v);

                    if (!empty($key)) {
                        $return .= "$key";
                    }

                    if ($value !== '') {
                        if (!empty($key)) {
                            $return .= "=";
                        }

                        $return .= "\"$value\"";
                    }

                    return $return;
                },
                array_keys($attributes),
                array_values($attributes)
            )
        );
    }


    /**
     * @source src/inc/xtc_parse_input_field_data.inc.php
     *
     * @param $data
     *
     * @return string
     */
    private function parseAttributeValue($data): string
    {
        if (is_string($data) || is_numeric($data)) {
            return strtr(
                trim(strval($data) ?? ''),
                [
                    '"' => '&quot;',
                ]
            );
        }

        return '';
    }
}