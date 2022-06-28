<?php

namespace Drupal\arche_gui_api\Controller\Child;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of RPRController
 *
 * @author nczirjak
 */
class ChildController
{
    public function execute(string $repoid, array $searchProps, string $lang = "en"): Response
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/getHasActors/repoid/lang?_format=json
         */

        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
         
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a search string"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\Child\ChildObject();
        $content = $object->init($repoid, $lang, $searchProps);
        
        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }

        $response = new Response();
        $response->setContent(
            json_encode(
                array(
                    "aaData" => $content,
                    "iTotalRecords" => ($content[0]['sumcount']) ?  $content[0]['sumcount'] : 0,
                    "iTotalDisplayRecords" => ($content[0]['sumcount']) ?  $content[0]['sumcount'] : 0,
                    "draw" => intval($searchProps['draw']),
                )
            )
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
