<?php

namespace Drupal\arche_gui_api\Controller\FileFormats;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\arche_gui_api\Helper\FileFormats\FileFormatsHTMLHelper;

/**
 * Description of MetadataGuiController
 *
 * @author nczirjak
 */
class FileFormatsHTMLController
{
    private $helper;
    
    public function __construct() {
        $this->helper = new \Drupal\arche_gui_api\Helper\FileFormats\FileFormatsHTMLHelper();
    }
    
    public function execute(string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/getMetadataGuiHTML/lang?_format=json
         */

        
        $files = \acdhOeaw\ArcheFileFormats::getAll();
        $fileObjs = [];
        foreach($files as $k => $v) {
            $ff = new \Drupal\arche_gui_api\Object\FileFormatObject($v);
            if($ff->isValid()) {
                $fileObjs[] = $ff;
            }
          
        }
        
       
        if (count($fileObjs) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        
        $content = $this->helper->fetchHtmlContent($fileObjs);

        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
    
    
    
    
    
    
}
