<?php

/* --------------------------------------------------------------
   ElementPosition.inc.php 2019-04-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ElementPosition
 *
 * This class represents the position of an element
 *
 * @category   System
 * @package    Content
 */
class ElementPosition implements ContentPositionInterface
{
    /**
     * Start position
     */
    const START = 'start';
    
    /**
     * Header position
     */
    const HEADER = 'header';
    
    /**
     * Boxes position
     */
    const BOXES = 'boxes';
    
    /**
     * Footer position
     */
    const FOOTER = 'footer';
    
    /**
     * Withdrawal position
     */
    const WITHDRAWAL = 'withdrawal';
    
    /**
     * Other position
     */
    const OTHER = 'other';
    
    /**
     * Stlyeedit position
     */
    const STYLEEDIT = 'styleedit';
    
    /**
     * Element position
     *
     * @var string
     */
    protected $position;
    
    
    /**
     * ElementPosition constructor
     *
     * @param string $position Element position
     */
    protected function __construct(string $position)
    {
        $this->position = $position;
    }
    
    
    /**
     * Create instance for start position
     *
     * @return ElementPosition
     */
    public static function createForStart(): self
    {
        return new self(self::START);
    }
    
    
    /**
     * Create instance for header position
     *
     * @return ElementPosition
     */
    public static function createForHeader(): self
    {
        return new self(self::HEADER);
    }
    
    
    /**
     * Create instance for boxes position
     *
     * @return ElementPosition
     */
    public static function createForBoxes(): self
    {
        return new self(self::BOXES);
    }
    
    
    /**
     * Create instance for footer position
     *
     * @return ElementPosition
     */
    public static function createForFooter(): self
    {
        return new self(self::FOOTER);
    }
    
    
    /**
     * Create instance for withdrawal position
     *
     * @return ElementPosition
     */
    public static function createForWithdrawal(): self
    {
        return new self(self::WITHDRAWAL);
    }
    
    
    /**
     * Create instance for other position
     *
     * @return ElementPosition
     */
    public static function createForOther(): self
    {
        return new self(self::OTHER);
    }
    
    
    /**
     * @return ElementPosition
     */
    public static function createForStyleEdit(): self
    {
        return new self(self::STYLEEDIT);
    }
    
    
    /**
     * Create instance from a position string
     *
     * @param string $position Position as string
     *
     * @return ElementPosition
     */
    public static function createFromString(string $position): self
    {
        $validPositions = [
            self::START,
            self::HEADER,
            self::BOXES,
            self::FOOTER,
            self::WITHDRAWAL,
            self::OTHER,
            self::STYLEEDIT
        ];
        
        if (!in_array($position, $validPositions, true)) {
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