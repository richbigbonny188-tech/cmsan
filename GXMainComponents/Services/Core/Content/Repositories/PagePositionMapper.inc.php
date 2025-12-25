<?php

/**
 * Class PagePositionMapper
 */
class PagePositionMapper
{
    /**
     * PagePositionMapper constructor.
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
     * @param string $pageGroupId
     *
     * @return array
     * @throws \Exception
     */
    public static function getPagePositionForDatabase(string $pageGroupId): array
    {
        switch ($pageGroupId) {
            case 'mainNavigation':
                $position = 'pages_main';
                $fileFlag = 3;
                break;
            case 'secondaryNavigation':
                $position = 'pages_secondary';
                $fileFlag = 2;
                break;
            case 'info':
                $position = 'pages_info';
                $fileFlag = 1;
                break;
            case 'infoBox':
                $position = 'pages_info_box';
                $fileFlag = 0;
                break;
            case 'additional':
                $position = 'pages_additional';
                $fileFlag = 6;
                break;
            default:
                throw new Exception("Unknown PagePosition($pageGroupId)");
        }
    
        return ['position' => $position, 'fileFlag' => $fileFlag];
    }
    
    
    /**
     * @param string $position
     *
     * @return string
     * @throws \Exception
     */
    public static function getPagePositionFromDatabase(string $position): string
    {
        switch ($position) {
            case 'pages_info':
                
                return 'info';
            
            case 'pages_main' :
                
                return 'mainNavigation';
            
            case 'pages_info_box' :
                
                return 'infoBox';
            
            case 'pages_secondary' :
                
                return 'secondaryNavigation';
            
            case 'pages_additional' :
                
                return 'additional';
            
            default:
                
                throw new Exception('Unknown PagePosition(' . $position . ')');
        }
    }
}
