<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of MembersController
 *
 * @author nczirjak
 */
class MembersController
{
    public function execute(string $repoid): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/getMembers/repoid?_format=json
         */
        
        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
        
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a search string"), 204, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\MembersObject();
        $content = $object->init($repoid);

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 204, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse(array("data" => $content), 200, ['Content-Type' => 'application/json']);
    }
}
