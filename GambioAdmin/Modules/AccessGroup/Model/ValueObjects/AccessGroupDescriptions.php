<?php
/* --------------------------------------------------------------
   GroupDescriptions.php 2021-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class AccessGroupDescriptions
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\ValueObjects
 */
class AccessGroupDescriptions
{
    /**
     * @var array<string, string>
     */
    private $descriptions;
    
    
    /**
     * AccessGroupDescriptions constructor.
     */
    private function __construct()
    {
        $this->descriptions = [];
    }
    
    
    /**
     * @param array $descriptions
     *
     * @return AccessGroupDescriptions
     */
    public static function create(array $descriptions): AccessGroupDescriptions
    {
        $collection = new self();
        foreach ($descriptions as $languageCode => $name) {
            $collection->addDescription($languageCode, $name);
        }
        
        return $collection;
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function getDescription(string $languageCode): string
    {
        return $this->descriptions[strtolower($languageCode)] ?? '';
    }
    
    
    /**
     * @param string $languageCode
     * @param string $description
     */
    public function addDescription(string $languageCode, string $description): void
    {
        Assert::regex($languageCode, '/^[a-z]{2}$/i', 'Given language code must be a two-digit ISO code.');
        Assert::notWhitespaceOnly($description, 'Description can not be empty.');
        
        $this->descriptions[strtolower($languageCode)] = $description;
    }
}