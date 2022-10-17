<?php

namespace Drupal\arche_gui_api\Controller\Collection;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of CollectionScriptController
 *
 * @author nczirjak
 */
class CollectionScriptController extends \Drupal\Core\Controller\ControllerBase
{
    public function execute(string $repoid): Response
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/collection_dl_script/repoid?_format=json
         */
        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
        
        if (empty($repoid)) {
            return new Response(array("Repoid is not valid!"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\Collection\CollectionScriptObject();
        $content = $object->init($repoid);
        
        $response = new Response();
        $response->setContent($content);
        $response->headers->set('Content-Type', 'application/x-python-code');
        $response->headers->set('Content-Disposition', 'attachment; filename=collection_download_script.py');
        return $response;
    }
}
