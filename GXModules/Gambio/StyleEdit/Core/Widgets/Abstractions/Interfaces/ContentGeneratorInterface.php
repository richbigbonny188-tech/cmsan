<?php
/*--------------------------------------------------------------------------------------------------
    ContentGeneratorInterface.php 2022-08-09
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces;

use Exception;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use stdClass;

/**
 * Interface ContentGeneratorInterface
 * @package Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces
 */
interface ContentGeneratorInterface
{
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage): string;
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function previewContent(?Language $currentLanguage): string;
    
    
    /**
     * @param stdClass $jsonObject
     *
     * @return ContentGeneratorInterface
     *
     * @throws Exception
     */
    public static function createFromJsonObject(stdClass $jsonObject);
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize();
}