<?php
/* --------------------------------------------------------------
 NamespaceConverter.php 2020-01-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 14 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Compatibility\Repositories\Storage;

use function array_shift;
use function count;
use function explode;
use function is_array;

/**
 * Class NamespaceConverter
 * @package Gambio\Core\Configuration\Repositories\Components
 */
class NamespaceConverter
{
    private const SEPARATOR = '/';
    
    
    /**
     * Converts a flat array to a tree array.
     * The "/" char will be used as separator for the later array hierarchy.
     *
     * Example of flat and tree array:
     * ```php
     * $flatArray = [
     *      'some/flat'     => 'some-value'
     *      'some/flat/key' => 'some-flat-value'
     * ];
     *
     * $treeArray = [
     *      'some' => [
     *          'flat' => [
     *              '_' => 'some-value',
     *              'key' => 'some-flat-value'
     *          ]
     *      ],
     * ];
     * ```
     *
     * @param array $flat
     *
     * @return array
     */
    public function fromFlatToTree(array $flat): array
    {
        $data = [];
        
        foreach ($flat as $key => $value) {
            $segments = explode(self::SEPARATOR, $key);
            
            $currentKey  = array_shift($segments);
            $currentNode = &$data;
            
            while (count($segments) > 0) {
                if (isset($currentNode[$currentKey])) {
                    if (!is_array($currentNode[$currentKey])) {
                        $currentNodeValue         = $currentNode[$currentKey];
                        $currentNode[$currentKey] = ['_' => $currentNodeValue];
                    }
                } else {
                    $currentNode[$currentKey] = [];
                }
                $currentNode = &$currentNode[$currentKey];
                $currentKey  = array_shift($segments);
            }
            
            $currentNode[$currentKey] = $value;
        }
        
        return $data;
    }
    
    
    /**
     * Converts a tree array to a flat array.
     * The "/" char will be used as separator for the flattened array.
     *
     * Example of flat and tree array:
     * ```php
     * $flatArray = [
     *      'some/flat'     => 'some-value'
     *      'some/flat/key' => 'some-flat-value'
     * ];
     *
     * $treeArray = [
     *      'some' => [
     *          'flat' => [
     *              '_' => 'some-value',
     *              'key' => 'some-flat-value'
     *          ]
     *      ],
     * ];
     * ```
     *
     * @param array  $tree
     * @param string $prefix
     *
     * @return array
     */
    public function fromTreeToFlat(array $tree, string $prefix = ''): array
    {
        $data      = [];
        $topPrefix = $prefix === '' ? $prefix : $prefix . self::SEPARATOR;
        
        foreach ($tree as $key => $value) {
            if (is_array($value)) {
                $flattenedSubTree = $this->fromTreeToFlat($value, $key);
                
                foreach ($flattenedSubTree as $subtreeKey => $subtreeValue) {
                    $data[$topPrefix . $subtreeKey] = $subtreeValue;
                }
            } else {
                $configKey = $prefix . self::SEPARATOR . $key;
                $configKey = $configKey === self::SEPARATOR . $key ? $key : $configKey;
                
                $configKey        = $key === '_' ? $prefix : $configKey;
                $data[$configKey] = $value;
            }
        }
        
        return $data;
    }
}