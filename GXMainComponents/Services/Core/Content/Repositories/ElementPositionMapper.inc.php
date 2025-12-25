<?php

/**
 * Class ElementPositionMapper
 */
class ElementPositionMapper
{
    /**
     * ElementPositionMapper constructor
     *
     * This class cannot be instantiated
     */
    private function __construct()
    {
    }
    
    
    /**
     * The position inside the json file differs from the actually needed one
     * in the Database. This method translates the position
     *
     * @param string $position
     *
     * @return string
     * @throws \Exception
     */
    public static function getElementPositionForDatabase(string $position): string
    {
        switch ($position) {
            case 'start' :
            case 'header' :
            case 'boxes' :
            case 'footer' :
            case 'withdrawal' :
            case 'styleedit':
                
                return "elements_$position";
            
            case 'other' :
                
                return 'elements_others';
            
            default :
                
                throw new Exception('Unknown ElementPosition(' . $position . ')');
        }
    }
    
    
    /**
     * @param string $position
     *
     * @return string
     * @throws Exception
     */
    public static function getElementPositionFromDatabase(string $position): string
    {
        switch ($position) {
            
            case 'elements_start' :
            case 'elements_header' :
            case 'elements_boxes' :
            case 'elements_footer' :
            case 'elements_withdrawal' :
            case 'elements_styleedit' :
                
                return str_replace('elements_', '', $position);
            
            case 'elements_others' :
                
                return 'other';
            
            default:
                
                throw new Exception('Unknown ElementPosition(' . $position . ')');
        }
    }
}