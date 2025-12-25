<?php

/* --------------------------------------------------------------
   PagePosition.inc.php 2019-04-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PagePosition
 *
 * This class represents the position of a page
 *
 * @category   System
 * @package    Content
 */
class PagePosition implements ContentPositionInterface
{
    /**
     * Main navigation position
     */
    const MAIN_NAVIGATION = 'mainNavigation';
    
    /**
     * Secondary navigation position
     */
    const SECONDARY_NAVIGATION = 'secondaryNavigation';
    
    /**
     * Info position
     */
    const INFO = 'info';
    
    /**
     * Info box position
     */
    const INFO_BOX = 'infoBox';
    
    /**
     * Additional Page position
     */
    const ADDITIONAL = 'additional';
    
    /**
     * Page position
     *
     * @var string
     */
    protected $position;
    
    
    /**
     * PagePosition constructor
     *
     * @param string $position Page position
     */
    protected function __construct(string $position)
    {
        $this->position = $position;
    }
    
    
    /**
     * Create instance for main navigation position
     *
     * @return PagePosition
     */
    public static function createForMainNavigation(): self
    {
        return new self(self::MAIN_NAVIGATION);
    }
    
    
    /**
     * Create instance for secondary navigation position
     *
     * @return PagePosition
     */
    public static function createForSecondaryNavigation(): self
    {
        return new self(self::SECONDARY_NAVIGATION);
    }
    
    
    /**
     * Create instance for info position
     *
     * @return PagePosition
     */
    public static function createForInfo(): self
    {
        return new self(self::INFO);
    }
    
    
    /**
     * Create instance for info box position
     *
     * @return PagePosition
     */
    public static function createForInfoBox(): self
    {
        return new self(self::INFO_BOX);
    }
    
    
    /**
     * Create instance for info box position
     *
     * @return PagePosition
     */
    public static function createForAdditional(): self
    {
        return new self(self::ADDITIONAL);
    }
    
    
    /**
     * Create instance from a position string
     *
     * @param string $position Position as string
     *
     * @return PagePosition
     */
    public static function createFromString(string $position): self
    {
        $validPositions = [
            self::MAIN_NAVIGATION,
            self::SECONDARY_NAVIGATION,
            self::INFO,
            self::INFO_BOX,
            self::ADDITIONAL,
        ];
        
        if (!in_array($position, $validPositions)) {
            throw new InvalidArgumentException('Invalid position');
        }
        
        return new self($position);
    }
    
    
    /**
     * Return the position
     *
     * @return string
     */
    public function position(): string
    {
        return $this->position;
    }
}
