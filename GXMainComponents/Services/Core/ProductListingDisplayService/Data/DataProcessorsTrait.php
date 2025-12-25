<?php
/* --------------------------------------------------------------
  DataProcessorsTrait.php 2023-03-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

/**
 * Trait DataProcessorsTrait
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
trait DataProcessorsTrait
{
    /**
     * @param string   $key
     * @param array    $rawData
     * @param callable $callback
     *
     * @return mixed
     */
    private function process(string $key, array $rawData, callable $callback)
    {
        return $callback($this->find($key, $rawData));
    }


    /**
     * @param string $key
     * @param array  $rawData
     *
     * @return mixed|null
     */
    private function find(string $key, array $rawData)
    {
        return array_key_exists($key, $rawData) ? $rawData[$key] : null;
    }


    /**
     * @param $value
     *
     * @return string
     */
    private function processString($value): string
    {
        if (
            empty($value)
            || !is_scalar($value)
            || is_bool($value)
            || (is_numeric($value) && (int)$value === 0)
        ) {
            $value = null;
        }

        settype($value, 'string');

        return $value;
    }


    /**
     * @param $value
     *
     * @return int
     */
    private function processInt($value): int
    {
        if (!empty($value) && (is_numeric($value) || is_bool($value))) {
            settype($value, 'int');
        } else {
            $value = 0;
        }

        return $value;
    }
}