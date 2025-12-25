<?php
/* --------------------------------------------------------------
   ContentSitemapPriority.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentSitemapPriority
 *
 * This class represents the localized site map priority
 *
 * @category   System
 * @package    Content
 */
class ContentSitemapPriority
{
    /**
     * Minimum value
     */
    const MIN = 0.0;
    
    /**
     * Maximum value
     */
    const MAX = 1.0;
    
    
    /**
     * Content site map priority
     *
     * @var string
     */
    protected $priority;
    
    
    /**
     * ContentSitemapPriority constructor
     *
     * @param float        $priority     Content site map priority
     */
    public function __construct(float $priority)
    {
        if ($priority < self::MIN || $priority > self::MAX) {
            throw new OutOfBoundsException('Invalid priority range');
        }
        
        $this->priority     = $this->formatSitemapPriority($priority);
    }
    
    
    /**
     * Return the content site map priority
     *
     * @return string
     */
    public function content(): string
    {
        return $this->priority;
    }
    
    
    /**
     * Format the provided site map priority float to its according string representation
     * This method will NOT round the value of a provided float, that contains many decimals
     *
     * @param float $value Float site map priority value
     *
     * @return string
     *
     * @example $number = formatSitemapPriority(0.987654321) // $number === "0.9"
     *
     */
    protected function formatSitemapPriority(float $value): string
    {
        $formatted = floor(($value * 10)) / 10;
        
        return number_format($formatted, 1);
    }
}