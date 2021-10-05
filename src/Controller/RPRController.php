<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of RPRController
 *
 * @author nczirjak
 */
class RPRController
{
    public function execute(string $repoid, string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/getRPR/repoid/lang?_format=json
         */

        $repoid = preg_replace('/[^0-9]/', '', $repoid);
         
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a search string"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\RPRObject();
        $content = $object->init($repoid, $lang);

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse(array("data" => $content), 200, ['Content-Type' => 'application/json']);
    }
}
