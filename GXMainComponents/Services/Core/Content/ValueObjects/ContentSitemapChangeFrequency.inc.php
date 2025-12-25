<?php
/* --------------------------------------------------------------
   ContentSitemapChangeFrequency.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentSitemapChangeFrequency
 *
 * This class represents the site map content change frequency
 *
 * @category   System
 * @package    Content
 */
class ContentSitemapChangeFrequency
{
    /**
     * Always changing frequency
     */
    const ALWAYS = 'always';
    
    /**
     * Hourly changing frequency
     */
    const HOURLY = 'hourly';
    
    /**
     * Daily changing frequency
     */
    const DAILY = 'daily';
    
    /**
     * Daily changing frequency
     */
    const WEEKLY = 'weekly';
    
    /**
     * Monthly changing frequency
     */
    const MONTHLY = 'monthly';
    
    /**
     * Yearly changing frequency
     */
    const YEARLY = 'yearly';
    
    /**
     * Never changing
     */
    const NEVER = 'never';
    
    /**
     * Change frequency
     *
     * @var string
     */
    protected $frequency;
    
    
    /**
     * ContentSitemapChangeFrequency constructor
     *
     * @param string $frequency Change frequency
     */
    protected function __construct(string $frequency)
    {
        $this->frequency = $frequency;
    }
    
    
    /**
     * Create instance for always changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForAlwaysChangingFrequency(): self
    {
        return new self(self::ALWAYS);
    }
    
    
    /**
     * Create instance for hourly changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForHourlyChanges(): self
    {
        return new self(self::HOURLY);
    }
    
    
    /**
     * Create instance for daily changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForDailyChanges(): self
    {
        return new self(self::DAILY);
    }
    
    
    /**
     * Create instance for weekly changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForWeeklyChanges(): self
    {
        return new self(self::WEEKLY);
    }
    
    
    /**
     * Create instance for monthly changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForMonthlyChanges(): self
    {
        return new self(self::MONTHLY);
    }
    
    
    /**
     * Create instance for yearly changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForYearlyChanges(): self
    {
        return new self(self::YEARLY);
    }
    
    
    /**
     * Create instance for yearly changing frequency
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createForNeverChangingFrequency(): self
    {
        return new self(self::NEVER);
    }
    
    
    /**
     * Create instance from a site map change frequency string
     *
     * @param string $frequency Site map change frequency as string
     *
     * @return ContentSitemapChangeFrequency
     */
    public static function createFromString(string $frequency): self
    {
        $validFrequencies = [
            self::ALWAYS,
            self::HOURLY,
            self::DAILY,
            self::WEEKLY,
            self::MONTHLY,
            self::YEARLY,
            self::NEVER
        ];
        
        if (!in_array($frequency, $validFrequencies)) {
            throw new InvalidArgumentException('Invalid frequency');
        }
        
        return new self($frequency);
    }
    
    
    /**
     * Return the site map content change frequency
     *
     * @return string
     */
    public function frequencyOfChange(): string
    {
        return $this->frequency;
    }
}