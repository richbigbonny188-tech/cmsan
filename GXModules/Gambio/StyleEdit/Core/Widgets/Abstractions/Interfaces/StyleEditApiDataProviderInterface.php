<?php
/* --------------------------------------------------------------
  StyleEditApiDataProviderInterface.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces;

use JsonSerializable;
use stdClass;

/**
 * Interface StyleEditApiDataProviderInterface
 */
interface StyleEditApiDataProviderInterface
{
    /**
     * Data can be accessed on route:
     *
     * GXModules/Gambio/StyleEdit/Api/api.php/styleedit/$LANGUAGE_CODE/widget/$THEME_ID/$WIDGET_ID
     *
     * @return JsonSerializable|stdClass|array data
     */
    public static function apiData();
}