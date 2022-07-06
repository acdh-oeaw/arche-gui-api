<?php

namespace Drupal\arche_gui_api\Controller\Ontology;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of OntolgyJsController
 *
 * @author nczirjak
 */
class OntolgyJsController
{
    public function execute(string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/getOntologyJSPluginData/lang?_format=json
         */

        $object = new \Drupal\arche_gui_api\Object\Ontology\OntolgyJsObject();
        $content = $object->init($lang);

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 204, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
}
