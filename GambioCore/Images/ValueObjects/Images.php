<?php
/*--------------------------------------------------------------------
 Images.php 2020-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\Core\Images\ValueObjects;


class Images
{
    protected $value;

    /**
     * Images constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

}