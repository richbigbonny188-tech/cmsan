<?php
/* --------------------------------------------------------------
  UrlBuilder.php 2023-05-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class UrlBuilder
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class UrlBuilder
{
    /**
     * @param Url $url
     */
    public function __construct(private Url $url)
    {
        //
    }


    /**
     * @param string $routeName
     * @param array  $params
     *
     * @return string
     */
    public function getLink(string $routeName = '', array $params = []): string
    {
        return sprintf(
            '%s/%s',
            $this->url->base(),
            $this->getRoute(
                trim(
                    $routeName,
                    '/'
                ),
                $params
            )
        );
    }


    /**
     * @param string $routeName
     * @param array  $params
     *
     * @return string
     */
    public function getRoute(string $routeName = '', array $params = []): string
    {
        return $this->getPath(
            trim(
                $routeName,
                '/'
            ),
            $params
        );
    }


    /**
     * @param string $routeName
     * @param array  $params
     *
     * @return string
     */
    public function getPath(string $routeName = '', array $params = []): string
    {
        return sprintf(
            '%s%s',
            $routeName,
            $this->prepareQueryString($params)
        );
    }


    /**
     * @param array $params
     *
     * @return string
     */
    private function prepareQueryString(array $params): string
    {
        $separator = '&amp;';
        $query     = array_map(
            function ($k, $v): string {
                return "$k=$v";
            },
            array_map([$this, 'stringify'], array_keys($params)),
            array_map([$this, 'stringify'], $params),
        );

        return empty($params) ? '' : '?' . implode($separator, $query);
    }


    /**
     * @param $value
     *
     * @return string
     */
    public function getSlug($value): string
    {
        if (!is_scalar($value)) {
            $value = json_encode($value);
        }

        $value = strval($value);

        return $this->slugify($value);
    }


    /**
     * @param string $value
     *
     * @return string
     */
    private function slugify(string $value): string
    {
        $separator   = '-';
        $searchFor   = ['ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', '&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', 'ß',
                        '&szlig;'];
        $replaceWith = ['ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ss', 'ss'];
        $value       = str_replace($searchFor, $replaceWith, $value);
        $replace     = '/[^a-zA-Z0-9]/';
        $value       = preg_replace($replace, $separator, $value);
        $value       = strtolower($value);

        return $value;
    }


    /**
     * @param $value
     *
     * @return string
     */
    private function stringify($value): string
    {
        if (is_scalar($value) || is_bool($value)) {
            return urlencode(
                trim(
                    strval(
                        false === $value ? '0' : $value
                    )
                )
            );
        }

        return '';
    }
}