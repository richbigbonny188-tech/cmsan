<?php
/*--------------------------------------------------------------
   FieldKeyGenerator.php 2021-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\App\Data;

/**
 * Class FieldKeyGenerator
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\App\Data
 */
class FieldKeyGenerator
{
    /**
     * @return string
     */
    public function newFieldKey(): string
    {
        return 'product-' . md5(time() . rand());
    }
}