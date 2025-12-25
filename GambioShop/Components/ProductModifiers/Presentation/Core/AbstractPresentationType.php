<?php
/*--------------------------------------------------------------------------------------------------
    AbstractPresentationType.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Presentation\Core;

/**
 * Class AbstractPresentationFormat
 * @package Gambio\Shop\ProductModifiers\Modifiers\PresentationFormat
 */
abstract class AbstractPresentationType implements PresentationTypeInterface
{
    /**
     * @return PresentationTypeInterface
     */
    public static function instance(): PresentationTypeInterface
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    
    /**
     * @inheritDoc
     */
    public static function type(): string
    {
        return static::$TYPE;
    }
    
    
    /**
     * @inheritDoc
     */
    public function equals(PresentationTypeInterface $type): bool
    {
        return is_a($type, self::class);
    }
}