<?php

/* --------------------------------------------------------------
   DirectHelpManualParsedUrl.inc.php 2018-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a parsed URL
 */
class DirectHelpManualParsedUrl
{
    /**
     * URL base name
     *
     * @var string
     */
    protected $basename;
    
    /**
     * Parameters
     *
     * @var array
     */
    protected $parameters;
    
    
    /**
     * Return a new instance using the provided parameter as typed string
     *
     * @param NonEmptyStringType $url URL
     *
     * @return DirectHelpManualParsedUrl
     */
    public static function createUsing(NonEmptyStringType $url)
    {
        return new self($url->asString());
    }
    
    
    /**
     * Return a new instance using the provided parameter as raw string
     *
     * @param string $url URL
     *
     * @return DirectHelpManualParsedUrl
     */
    public static function createUsingRawString($url)
    {
        return new self($url);
    }
    
    
    /**
     * Create instance
     *
     * @param string $url URL
     */
    private function __construct($url)
    {
        $parts      = parse_url($url);
        $parameters = [];
        $basename   = basename($parts['path']);
        $fragment   = $parts['fragment'] ?? null;
        
        parse_str(isset($parts['query']) ? (string)$parts['query'] : '', $parameters);
        
        $this->basename = $basename ? : '';
        
        // Handle fragment as query parameter
        if ($fragment) {
            $parameters['#'] = $fragment;
        }
        
        $this->parameters = $parameters;
    }
    
    
    /**
     * Return the base name
     *
     * @return string
     */
    public function basename()
    {
        return $this->basename;
    }
    
    
    /**
     * Return the parameters
     *
     * @return array
     */
    public function parameters()
    {
        return $this->parameters;
    }
    
    
    /**
     * Return whether the comparison URL has the same file name (base name) as this object
     *
     * @param DirectHelpManualParsedUrl $foreign Comparison URL
     *
     * @return bool
     */
    public function isSameFileAs(self $foreign)
    {
        return $this->basename === $foreign->basename();
    }
    
    
    /**
     * Return whether the comparison URL has the same parameters as this object
     *
     * @param DirectHelpManualParsedUrl $foreign Comparison URL
     *
     * @return bool
     */
    public function hasSameParametersAs(self $foreign)
    {
        $intersection = array_intersect_assoc($this->parameters, $foreign->parameters());
        
        return count($intersection) === count($this->parameters);
    }
    
    
    /**
     * Return the amount of parameter intersection with the comparison URL
     *
     * @param DirectHelpManualParsedUrl $foreign Comparison URL
     *
     * @return bool
     */
    public function parameterIntersectionWith(self $foreign)
    {
        $intersection = array_intersect_assoc($this->parameters, $foreign->parameters());
        
        return count($intersection);
    }
}