<?php
/* --------------------------------------------------------------
   boxes.php 2022-07-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/* -----------------------------------------------------------------------------------------
   $Id: boxes.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(boxes.php,v 1.32 2003/05/27); www.oscommerce.com 
   (c) 2003	 nextcommerce (boxes.php,v 1.11 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  class tableBox {
    public $table_border = '0';
    public $table_width = '100%';
    public $table_cellspacing = '0';
    public $table_cellpadding = '2';
    public $table_parameters = '';
    public $table_row_parameters = '';
    public $table_data_parameters = '';
	public $table_class = '';

    // class constructor
    public function get_table_box_string($contents, $direct_output = false) {
      $tableBox_string = '<table style="background: #fff;" class="' . $this->table_class . '" border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
      if (xtc_not_null($this->table_parameters)) $tableBox_string .= ' ' . $this->table_parameters;
      $tableBox_string .= '>' . "\n";

      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        if (isset($contents[$i]['form']) && xtc_not_null($contents[$i]['form'])) $tableBox_string .= $contents[$i]['form'] . "\n";
        $tableBox_string .= '  <tr';
        if (xtc_not_null($this->table_row_parameters)) $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params']) && xtc_not_null($contents[$i]['params'])) $tableBox_string .= ' ' . $contents[$i]['params'];
        $tableBox_string .= '>' . "\n";

        if (is_array($contents[$i][0] ?? null)) {
          for ($x=0, $n2=sizeof($contents[$i]); $x<$n2; $x++) {
            if (isset($contents[$i][$x]['text']) && xtc_not_null($contents[$i][$x]['text'])) {
              $tableBox_string .= '    <td';
              if (isset($contents[$i][$x]['align']) && xtc_not_null($contents[$i][$x]['align'])) $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
              if (isset($contents[$i][$x]['params']) && xtc_not_null($contents[$i][$x]['params'])) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif (xtc_not_null($this->table_data_parameters)) {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              $tableBox_string .= '>';
              if (isset($contents[$i][$x]['form']) && xtc_not_null($contents[$i][$x]['form'])) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if (isset($contents[$i][$x]['form']) && xtc_not_null($contents[$i][$x]['form'])) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . "\n";
            }
          }
        } else {
          $tableBox_string .= '    <td';
          if (isset($contents[$i]['align']) && xtc_not_null($contents[$i]['align'])) $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
          if (isset($contents[$i]['params']) && xtc_not_null($contents[$i]['params'])) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif (xtc_not_null($this->table_data_parameters)) {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
        }

        $tableBox_string .= '  </tr>' . "\n";
        if (isset($contents[$i]['form']) && xtc_not_null($contents[$i]['form'])) $tableBox_string .= '</form>' . "\n";
      }

      $tableBox_string .= '</table>' . "\n";

      if ($direct_output == true) echo $tableBox_string;

      return $tableBox_string;
    }
  }

  class infoBox extends tableBox {
    public function __construct($contents) {
      $info_box_contents = array();
      $info_box_contents[] = array('text' => $this->infoBoxContents($contents));
      $this->table_cellpadding = '1';
      $this->table_parameters = 'class="infoBox"';
      $this->get_table_box_string($info_box_contents, true);
    }

    public function infoBoxContents($contents) {
      $this->table_cellpadding = '3';
      $this->table_parameters = 'class="infoBoxContents"';
      $info_box_contents = array();
      $info_box_contents[] = array(array('text' => xtc_draw_separator('pixel_trans.gif', '100%', '1')));
      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        $info_box_contents[] = array(array('align' => $contents[$i]['align'],
                                           'form' => $contents[$i]['form'],
                                           'params' => 'class="boxText"',
                                           'text' => $contents[$i]['text']));
      }
      $info_box_contents[] = array(array('text' => xtc_draw_separator('pixel_trans.gif', '100%', '1')));
      return $this->get_table_box_string($info_box_contents);
    }
  }

  class infoBoxHeading extends tableBox {
    public function __construct($contents, $left_corner = true, $right_corner = true, $right_arrow = false) {
      $this->table_cellpadding = '0';

      if ($left_corner == true) {
        $left_corner = xtc_image(DIR_WS_IMAGES . 'infobox/corner_left.gif');
      } else {
        $left_corner = xtc_image(DIR_WS_IMAGES . 'infobox/corner_right_left.gif');
      }
      if ($right_arrow == true) {
        $right_arrow = '<a href="' . $right_arrow . '">' . xtc_image(DIR_WS_IMAGES . 'infobox/arrow_right.gif', ICON_ARROW_RIGHT) . '</a>';
      } else {
        $right_arrow = '';
      }
      if ($right_corner == true) {
        $right_corner = $right_arrow . xtc_image(DIR_WS_IMAGES . 'infobox/corner_right.gif');
      } else {
        $right_corner = $right_arrow . xtc_draw_separator('pixel_trans.gif', '11', '14');
      }

      $info_box_contents = array();
      $info_box_contents[] = array(array('params' => 'height="14" class="infoBoxHeading"',
                                         'text' => $left_corner),
                                   array('params' => 'width="100%" height="14" class="infoBoxHeading"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="14" class="infoBoxHeading" nowrap',
                                         'text' => $right_corner));
	
	  $this->get_table_box_string($info_box_contents, true);
    }
  }

  class contentBox extends tableBox {
    public function __construct($contents) {
      $info_box_contents = array();
      $info_box_contents[] = array('text' => $this->contentBoxContents($contents));
      $this->table_cellpadding = '1';
      $this->table_parameters = 'class="infoBox"';
	  $this->get_table_box_string($info_box_contents, true);
    }

    public function contentBoxContents($contents) {
      $this->table_cellpadding = '4';
      $this->table_parameters = 'class="infoBoxContents"';
      return $this->get_table_box_string($contents);
    }
  }

  class contentBoxHeading extends tableBox {
    public function __construct($contents) {
      $this->table_width = '100%';
      $this->table_cellpadding = '0';

      $info_box_contents = array();
      $info_box_contents[] = array(array('params' => 'height="14" class="infoBoxHeading"',
                                         'text' => xtc_image(DIR_WS_IMAGES . 'infobox/corner_left.gif')),
                                   array('params' => 'height="14" class="infoBoxHeading" width="100%"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="14" class="infoBoxHeading"',
                                         'text' => xtc_image(DIR_WS_IMAGES . 'infobox/corner_right_left.gif')));

      $this->get_table_box_string($info_box_contents, true);
    }
  }

  class errorBox extends tableBox {
    public function __construct($contents) {
      $this->table_data_parameters = 'class="errorBox"';
	  $this->table_class = 'box-error';
      $this->get_table_box_string($contents, true);
    }
  }

  class warningBox extends tableBox {
    public function __construct($contents) {
      $this->table_data_parameters = 'class="warningBox"';
      $this->table_class = 'box-warning';
      $this->get_table_box_string($contents, true);
    }
  }

  class productListingBox extends tableBox {
    public function __construct($contents) {
      $this->table_parameters = 'class="productListing"';
      $this->get_table_box_string($contents, true);
    }
  }
