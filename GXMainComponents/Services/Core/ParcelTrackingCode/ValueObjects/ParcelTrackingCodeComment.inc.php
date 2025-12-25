<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeComment.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeComment
 */
class ParcelTrackingCodeComment
{
    /**
     * @var string
     */
    protected $comment;
    
    
    /**
     * TrackingCodesComment constructor.
     * Private to enforce usage of named constructor.
     *
     * @param string $comment Tracking code comment.
     */
    protected function __construct($comment)
    {
        new NonEmptyStringType($comment);
        $this->comment = $comment;
    }
    
    
    /**
     * Named constructor of tracking code comments.
     *
     * @param string $comment Tracking code comment.
     *
     * @return ParcelTrackingCodeComment New instance.
     */
    public static function write($comment)
    {
        return new static($comment);
    }
    
    
    /**
     * Returns the comment of a tracking code.
     *
     * @return string $comment Tracking code comment.
     */
    public function is()
    {
        return $this->comment;
    }
}