<?php

namespace Drupal\arche_gui_api\Controller\Collection;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of CollectionController
 *
 * @author nczirjak
 */
class CollectionController extends \Drupal\Core\Controller\ControllerBase
{
    public function execute(string $repoid, string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/get_collection_data_lazy/repoid?_format=json
         */
        $repoid = preg_replace('/[^0-9]/', '', $repoid);
        
        if (empty($repoid)) {
            return new JsonResponse(array("Repoid is not valid!"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\Collection\CollectionObject();
        $content = $object->init($repoid, $lang);
        
        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        
        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
}
