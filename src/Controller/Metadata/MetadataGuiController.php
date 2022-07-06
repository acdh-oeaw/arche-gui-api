<?php

namespace Drupal\arche_gui_api\Controller\Metadata;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of MetadataGuiController
 *
 * @author nczirjak
 */
class MetadataGuiController
{
    public function execute(string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/getMetadataGui/lang?_format=json
         */

        $object = new \Drupal\arche_gui_api\Object\Metadata\MetadataGuiObject($lang);
        $content = $object->init();

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 204, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
}
