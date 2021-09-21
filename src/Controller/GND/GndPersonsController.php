<?php

namespace Drupal\arche_gui_api\Controller\GND;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of GndPersonsController
 *
 * @author nczirjak
 */
class GndPersonsController extends \Drupal\Core\Controller\ControllerBase
{
    public function execute(): JsonResponse {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/gnd?_format=json
         */
        
        $object = new \Drupal\arche_gui_api\Object\GndPersonsObject();
        $gndContent = $object->init();
        
        if (count($gndContent) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        
        return new JsonResponse($gndContent, 200, ['Content-Type' => 'application/json']);
    }
}
