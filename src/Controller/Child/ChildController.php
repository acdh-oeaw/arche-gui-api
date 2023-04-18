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
    public function getChild(string $repoid, array $searchProps, string $lang = "en"): Response
    {
        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
         
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a search string"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\Child\ChildObject();
        $data = $object->init($repoid, $lang, $searchProps);
     
        $cols = [];
        if (count($data) > 0) {
            $cols = array_keys((array)$data[0]);
        }
       
        $response = new Response();
        $response->setContent(
            json_encode(
                array(
                    "aaData" => $data,
                    "iTotalRecords" => ($data[0]['sumcount']) ?  $data[0]['sumcount'] : 0,
                    "iTotalDisplayRecords" => ($data[0]['sumcount']) ?  $data[0]['sumcount'] : 0,
                    "draw" => intval($searchProps['draw']),
                    "cols" =>  $cols,
                    "order" => $searchProps['order'],
                    "orderby" => $searchProps['orderby'],
                    "childTitle" => $object->getChildTitle(),
                    "rootType" => $object->getRootType()
                 )
            )
        );
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }
    
    
    public function getActor(string $repoid, array $searchProps, string $lang = "en"): Response
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
        $content = $object->getActor($repoid, $lang, $searchProps);
     
        $response = new Response();
        $response->setContent(
            json_encode(
                array(
                    "aaData" => $content,
                    "iTotalRecords" => (isset($content[0]['sumcount'])) ?  $content[0]['sumcount'] : 0,
                    "iTotalDisplayRecords" => (isset($content[0]['sumcount'])) ?  $content[0]['sumcount'] : 0,
                    "draw" => (intval($searchProps['draw'])) ? intval($searchProps['draw']) : 0,
                )
            )
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
