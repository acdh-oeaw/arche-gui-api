<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of GetInverseDataController
 *
 * @author nczirjak
 */
class InverseDataController {

    public function execute(string $repoid): JsonResponse {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/getInverseData/value?_format=json
         */

        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a search string"), 404, ['Content-Type' => 'application/json']);
        }

        $object = new \Drupal\arche_gui_api\Object\InverseDataObject();
        $gndContent = $object->init($repoid);

        if (count($gndContent) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($gndContent, 200, ['Content-Type' => 'application/json']);
    }

}
