<?php
/* --------------------------------------------------------------
  StyleEdit3TemplateConfigurationReader.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3TemplateConfigurationReaderInterface;

/**
 * Class StyleEdit3TemplateConfigurationReader
 */
class StyleEdit3TemplateConfigurationReader extends StyleEdit3ConfigurationReader
    implements StyleEdit3TemplateConfigurationReaderInterface
{
    /**
     * @return string[] paths to a StyleEdit3 json file from the root of the shop
     */
    public function configurations(): array
    {
        $result = [];
        
        foreach ($this->filesystem->listContents('StyleEdit3/templates') as $template) {
            
            if ($template['type'] === 'dir') {
                
                foreach ($this->filesystem->listContents($template['path']) as $templateFile) {
                    
                    if ($templateFile['type'] === 'file' && $templateFile['extension'] === 'json') {
                        
                        $result[] = $templateFile['path'];
                    }
                }
            }
        }
        
        return $result;
    }
}