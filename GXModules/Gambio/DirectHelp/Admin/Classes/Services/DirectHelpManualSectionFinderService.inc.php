<?php

/* --------------------------------------------------------------
   DirectHelpManualSectionFinderService.inc.php 2018-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the online manual section finder service
 */
class DirectHelpManualSectionFinderService
{
    /**
     * Online manual page links mapping
     *
     * @var array
     */
    protected $mapping;
    
    /**
     * Sections found
     *
     * @var array
     */
    protected $matches = [];
    
    
    /**
     * Create instance
     *
     * @param array $mapping Online manual page link mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }
    
    
    /**
     * Return the appropriate section by the location provided
     *
     * @param NonEmptyStringType $origin Origin URL
     *
     * @return string
     */
    public function sectionByOrigin(NonEmptyStringType $origin)
    {
        $this->matches = [];
        
        $originUrl = DirectHelpManualParsedUrl::createUsing($origin);
        
        foreach ($this->mapping as $url => $section) {
            $sectionUrl = DirectHelpManualParsedUrl::createUsingRawString($url);
            
            if (!$sectionUrl->isSameFileAs($originUrl)) {
                continue;
            }
            
            if ($sectionUrl->hasSameParametersAs($originUrl)) {
                $intersections                 = $sectionUrl->parameterIntersectionWith($originUrl);
                $this->matches[$intersections] = $section;
            }
        }
        
        return $this->result();
    }
    
    
    /**
     * Return the nearest section match.
     * It is done by sorting the match array by the keys,
     * that represent the intersection count and then return the last element.
     *
     * @return string|null
     */
    protected function result()
    {
        if (count($this->matches)) {
            ksort($this->matches);
            
            return array_pop($this->matches);
        }
        
        return null;
    }
}