<?php
/* --------------------------------------------------------------
   FontAwesomeIconClassProvider.inc.php 2017-10-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FontAwesomeIconClassProvider
{
    /**
     * @var array
     */
    protected static $map = [
        'media'        => 'fa fa-play-circle-o',
        'documents'    => 'fa fa-file-o',
        'tables'       => 'fa fa-table',
        'presentation' => 'fa fa-file-powerpoint-o',
        'images'       => 'fa fa-file-image-o',
        'text'         => 'fa fa-file-text-o',
        'archived'     => 'fa fa-file-archive-o'
    ];
    
    /**
     * @var array
     */
    protected static $media = [
        'webm',
        'ogx',
        'ogv',
        'oga',
        'ogg',
        'ogm',
        'spx',
        'mp3',
        'mp4',
        'wav',
        'asf',
        'mpg',
        'mpeg',
        'avi',
        'wmv',
        'mov',
        'ram'
    ];
    
    /**
     * @var array
     */
    protected static $documents = [
        'doc',
        'docx',
        'docm',
        'dot',
        'dotm',
        'dotx',
        'odt',
        'ods',
        'odg',
        'odc',
        'odf',
        'odi',
        'odm',
        'ott',
        'ots',
        'otp',
        'otg'
    ];
    
    /**
     * @var array
     */
    protected static $tables = [
        'xls',
        'xlsx',
        'xlsb',
        'xlsm',
        'xlt',
        'xltm',
        'xltx',
        'xlw',
        'csv',
        'ods'
    ];
    
    /**
     * @var array
     */
    protected static $presentation = [
        'odp',
        'ppa',
        'ppam',
        'pps',
        'ppsm',
        'ppsx',
        'ppt',
        'pptm',
        'pptx',
        'pptx',
        'pptx',
        'pot',
        'potm',
        'potx',
    ];
    
    /**
     * @var array
     */
    protected static $images = [
        'bmp',
        'gif',
        'jpg',
        'jpeg',
        'png'
    ];
    
    /**
     * @var array
     */
    protected static $text = [
        'rtf',
        'txt'
    ];
    
    /**
     * @var array
     */
    protected static $archived = [
        'rar',
        'tar',
        'zip',
        'bz2',
        '7z',
        's7z',
        'gz',
        'tar.gz'
    ];
    
    /**
     * @var array
     */
    protected static $custom = [
        'pdf'  => 'fa fa-file-pdf-o',
        'html' => 'fa fa-html5',
        'htm'  => 'fa fa-html5',
        'link' => 'fa fa-link'
    ];
    
    
    /**
     * Returns a font awesome class string by the given type.
     * If no type for the given file type was found, the fallback class 'fa fa-file-text-o' will be returned.
     *
     * @param string $type File type, which is represent by the font awesome icon class.
     *
     * @return string Font awesome class string.
     */
    public static function getClass($type)
    {
        if (array_key_exists($type, static::$custom)) {
            return static::$custom[$type];
        }
        
        foreach (static::$map as $iconType => $class) {
            if (in_array($type, static::$$iconType, true)) {
                return $class;
            }
        }
        
        return 'fa fa-file-text-o';
    }
}