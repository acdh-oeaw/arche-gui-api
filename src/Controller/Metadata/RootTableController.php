<?php

namespace Drupal\arche_gui_api\Controller\Metadata;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of RootTableController
 *
 * @author nczirjak
 */
class RootTableController
{
    public function execute(string $lang = "en"): Response
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/getRootTable/lang?_format=json
         */

        $object = new \Drupal\arche_gui_api\Object\Metadata\RootTableObject($lang);
        $content = $object->init();

        if (empty($content)) {
            return new \Symfony\Component\HttpFoundation\Response(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent('No data!');
        $response->setStatusCode(400);
        
        if (isset($content[0]) && !empty($content[0])) {
            $response->setContent($content[0]);
            $response->setStatusCode(200);
        }

        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
}
