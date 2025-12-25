<?php
/*--------------------------------------------------------------------------------------------------
    PresentationTypeInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Presentation\Core;

/**
 * Interface PresentationFormatInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers\PresentationFormat
 */
interface PresentationTypeInterface
{
    /**
     * @return string
     */
    public static function type(): string;
    
    /**
     * @param PresentationTypeInterface $type
     *
     * @return bool
     */
    public function equals(PresentationTypeInterface $type): bool;
}