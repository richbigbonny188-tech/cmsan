<?php

/* --------------------------------------------------------------
   ContentType.inc.php 2019-04-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentType
 *
 * This class represents the content type
 *
 * @category   System
 * @package    Content
 */
class ContentType implements ContentTypeInterface
{
    /**
     * "Content" content type
     */
    const CONTENT = 'content';
    
    /**
     * Link content type
     */
    const LINK = 'link';
    
    /**
     * File content type
     */
    const FILE = 'file';
    
    /**
     * Content type
     *
     * @var string
     */
    protected $type;
    
    
    /**
     * ContentType constructor
     *
     * @param string $type Content type
     */
    protected function __construct(string $type)
    {
        $this->type = $type;
    }
    
    
    /**
     * Create instance for "content" content type
     *
     * @return ContentType
     */
    public static function createForContent(): self
    {
        return new self(self::CONTENT);
    }
    
    
    /**
     * Create instance for link content type
     *
     * @return ContentType
     */
    public static function createForLink(): self
    {
        return new self(self::LINK);
    }
    
    
    /**
     * Create instance for file content type
     *
     * @return ContentType
     */
    public static function createForFile(): self
    {
        return new self(self::FILE);
    }
    
    
    /**
     * Return the content type
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }
}
