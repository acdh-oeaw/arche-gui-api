<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of VersionsController
 *
 * @author nczirjak
 */
class VersionsController
{
    public function execute(string $repoid, string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/versions/repoid/lang?_format=json
         */

        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
         
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a repoid string"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\VersionsObject();
        $content = $object->init($repoid, $lang);

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
}
