<?php
/* --------------------------------------------------------------
   placeholder.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio;

// don't display errors on screen for security reasons
ini_set('display_errors', '0');

/**
 * Example images/placeholder.php?size=400x200&font_size=36&color=ffd200&font_color=333
 * outputs a yellow 400x200 pixels image with black 36px text "400 x 200"
 */
class ImagePlaceholder
{
    /**
     * Creates and outputs a PNG image.
     *
     * Supported GET parameters:
     * size: 400x200 (image size), default 200x200
     * font_size: 36 (font size in px), default 16, 0 = no text
     * color: ff0000 or ddd format (background color as hex code), default a7a7a7
     * font_color: ff0000 or ddd format (font color as hex code), default ddd
     *
     * @return void
     */
    public static function create(): void
    {
        putenv('GDFONTPATH=' . realpath(__DIR__ . '/../includes/fonts'));
        $font = 'roboto.regular.ttf';
        
        $size       = (isset($_GET['size'])
                       && preg_match('/^[\d]{1,4}x[\d]{1,4}$/',
                                     $_GET['size'])) ? $_GET['size'] : '200x200';
        $dimensions = explode('x', $size);
        $fontSize   = (isset($_GET['font_size'])
                       && preg_match('/^[\d]{1,3}$/',
                                     $_GET['font_size'])) ? (int)$_GET['font_size'] : 16;
        $color      = (isset($_GET['color'])
                       && preg_match('/^([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/',
                                     $_GET['color'])) ? $_GET['color'] : 'ddd';
        $fontColor  = (isset($_GET['font_color'])
                       && preg_match('/^([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/',
                                     $_GET['font_color'])) ? $_GET['font_color'] : 'a7a7a7';
        $text       = $fontSize > 0 ? str_replace('x', ' x ', $size) : '';
        
        $cachedPlaceholders = glob(__DIR__ . '/../cache/placeholder_*.png');
        if (is_array($cachedPlaceholders) && count($cachedPlaceholders) > 1000) {
            http_response_code(503);
            die('Limit reached');
        }
        
        $filename = __DIR__ . "/../cache/placeholder_{$size}_{$fontSize}_{$color}_{$fontColor}.png";
        $success  = true;
        
        if (!file_exists($filename)) {
            // create image
            $image = imagecreatetruecolor($dimensions[0], $dimensions[1]);
            
            // create background color
            $color = self::convertHexToRGB($color);
            $color = imagecolorallocate($image, $color['red'], $color['green'], $color['blue']);
            
            // fill image with background color
            imagefilledrectangle($image, 0, 0, $dimensions[0] - 1, $dimensions[1] - 1, $color);
            
            if ($text !== '') {
                // calculate text dimensions
                $bbox = imagettfbbox($fontSize, 0, $font, $text);
                
                // X and Y coordinates for centering the text
                $x = $bbox[0] + (imagesx($image) / 2) - ($bbox[4] / 2);
                $y = $bbox[1] + (imagesy($image) / 2) - ($bbox[5] / 2);
                
                // create font color
                $fontColor = self::convertHexToRGB($fontColor);
                $fontColor = imagecolorallocate($image, $fontColor['red'], $fontColor['green'], $fontColor['blue']);
                
                // write text into image
                imagettftext($image, $fontSize, 0, $x, $y, $fontColor, $font, $text);
            }
            
            $success = imagepng($image, $filename);
            imagedestroy($image);
        }
        
        if ($success) {
            header('Content-Type: image/png');
            
            echo file_get_contents($filename);
        }
    }
    
    
    /**
     * @param string $hexColor color as hex code like ff0000 or ddd
     *
     * @return array
     */
    private static function convertHexToRGB(string $hexColor): array
    {
        $rgb = [];
        if (strlen($hexColor) === 6) {
            $colorVal     = hexdec($hexColor);
            $rgb['red']   = 0xFF & ($colorVal >> 0x10);
            $rgb['green'] = 0xFF & ($colorVal >> 0x8);
            $rgb['blue']  = 0xFF & $colorVal;
        } elseif (strlen($hexColor) === 3) {
            $rgb['red']   = hexdec(str_repeat(substr($hexColor, 0, 1), 2));
            $rgb['green'] = hexdec(str_repeat(substr($hexColor, 1, 1), 2));
            $rgb['blue']  = hexdec(str_repeat(substr($hexColor, 2, 1), 2));
        }
        
        return $rgb;
    }
}

ImagePlaceholder::create();