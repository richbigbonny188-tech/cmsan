<?php
/* --------------------------------------------------------------
   AccessRoleNames.php 2021-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class AccessRoleNames
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\ValueObjects
 */
class AccessRoleNames
{
    /**
     * @var array<string, string>
     */
    private $names;
    
    
    /**
     * AccessRoleNames constructor.
     */
    private function __construct()
    {
        $this->names = [];
    }
    
    
    /**
     * @param array $names
     *
     * @return AccessRoleNames
     */
    public static function create(array $names): AccessRoleNames
    {
        $collection = new self();
        foreach ($names as $languageCode => $name) {
            $collection->addName($languageCode, $name);
        }
        
        return $collection;
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function getName(string $languageCode): string
    {
        return $this->names[strtolower($languageCode)] ?? '';
    }
    
    
    /**
     * @param string $languageCode
     * @param string $name
     */
    public function addName(string $languageCode, string $name): void
    {
        Assert::regex($languageCode, '/^[a-z]{2}$/i', 'Given language code must be a two-digit ISO code.');
        Assert::notWhitespaceOnly($name, 'Role name can not be empty.');
        
        $this->names[strtolower($languageCode)] = $name;
    }
}