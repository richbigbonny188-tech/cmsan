<?php
/* --------------------------------------------------------------
  MailbeezModuleCenterModule.inc.php 2020-04-17
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class MailbeezModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class MailbeezModuleCenterModule extends AbstractModuleCenterModule
{
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('mailbeez_title');
        $this->description = $this->languageTextManager->get_text('mailbeez_description');
        $this->sortOrder   = 98998;
    }
    
    
    public function uninstall()
    {
        parent::uninstall();
        
        $this->db->set('value', json_encode(['value' => 'False']))
                 ->where('key', 'mailbeez/MAILBEEZ_MAILHIVE_STATUS')
                 ->update('gx_configurations');
    }
    
    
    public function install()
    {
        parent::install();
        
        $mailbeezStatus = $this->db->select('key')->where('key', 'mailbeez/MAILBEEZ_MAILHIVE_STATUS')->get(
            'gx_configurations'
        );
        
        if ($mailbeezStatus->num_rows() === 1) {
            $this->db->set('value', json_encode(['value' => 'True']))
                     ->where('key', 'mailbeez/MAILBEEZ_MAILHIVE_STATUS')
                     ->update('gx_configurations');
        }
    }
}