<?php
/*--------------------------------------------------------------
   WithLanguageCode.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Features;

use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;

/**
 * Interface representing an item containing a language code.
 * Classes that implement this interface usually are multilingual items.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Features
 */
interface WithLanguageCode
{
    /**
     * Return language code.
     *
     * @return LanguageCode Language code.
     */
    public function languageCode(): LanguageCode;
}