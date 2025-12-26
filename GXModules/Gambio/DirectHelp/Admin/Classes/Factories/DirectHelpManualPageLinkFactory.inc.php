<?php

/* --------------------------------------------------------------
   DirectHelpManualPageLinkFactory.inc.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the online manual page link factory
 */
class DirectHelpManualPageLinkFactory
{
    /**
     * Online manual page link mapping storage
     *
     * @var DirectHelpManualMappingFileStorage
     */
    protected $mappingStorage;
    
    /**
     * Current shop version
     *
     * @var string
     */
    protected $shopVersion;
    
    
    /**
     * Create instance
     *
     * @param DirectHelpManualMappingFileStorage $mappingStorage Mapping file storage
     * @param NonEmptyStringType                 $shopVersion    Shop version
     */
    public function __construct(
        DirectHelpManualMappingFileStorage $mappingStorage,
        NonEmptyStringType $shopVersion
    ) {
        $this->mappingStorage = $mappingStorage;
        $this->shopVersion    = $shopVersion->asString();
    }
    
    
    /**
     * Return the online manual search page URL with the provided search term
     *
     * @param NonEmptyStringType $term Search term
     *
     * @return string
     */
    public function linkBySearchTerm(NonEmptyStringType $term)
    {
        return $this->linkToManual($term->asString());
    }
    
    
    /**
     * Return the appropriate online manual page URL based on the origin provided
     *
     * @param NonEmptyStringType $origin Origin URL
     *
     * @return string
     */
    public function linkByOrigin(NonEmptyStringType $origin)
    {
        $sectionFinder = MainFactory::create('DirectHelpManualSectionFinderService', $this->mappingStorage->mapping());
        $section       = $sectionFinder->sectionByOrigin($origin);
        
        $baseOrigin = urlencode($this->baseFromUrl($origin));
        
        if (!$section) {
            return '';
        }
        
        return $this->linkToManual('', $baseOrigin, $section);
    }
    
    
    /**
     * Return the online manual root page URL
     *
     * @return string
     */
    public function linkToRootPage()
    {
        return $this->linkToManual();
    }
    
    
    /**
     * Return the final URL to the appropriate manual page
     *
     * @param string $search  Search parameters
     * @param string $origin  Origin parameter
     * @param string $section Manual section parameter
     *
     * @return string
     */
    protected function linkToManual($search = '', $origin = '', $section = '')
    {
        $url = $this->mappingStorage->rootPage() . '?v=' . $this->shopVersion;
        
        $url .= $search ? "&search={$search}" : '';
        $url .= $origin ? "&origin={$origin}" : '';
        $url .= $section ? "&section={$section}" : '';
        
        return $url;
    }
    
    
    /**
     * Return the basename, fragment and parameters from an URL
     *
     * @param StringType $url URL
     *
     * @return string
     */
    protected function baseFromUrl(StringType $url)
    {
        $parsed     = parse_url($url->asString());
        $basename   = basename($parsed['path']);
        $fragment   = !empty($parsed['fragment']) ? "#{$parsed['fragment']}" : '';
        $parameters = !empty($parsed['query']) ? "?{$parsed['query']}" : '';
        
        return $basename . $parameters . $fragment;
    }
}