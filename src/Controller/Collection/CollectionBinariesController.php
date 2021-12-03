<?php

namespace Drupal\arche_gui_api\Controller\Collection;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of CollectionBinariesController
 *
 * @author nczirjak
 */
class CollectionBinariesController extends \Drupal\Core\Controller\ControllerBase
{
    public function execute(string $repoid): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/dl_collection_binaries/repoid?_format=json
         */
        $GLOBALS['resTmpDir'] = "";
        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
        
        if (empty($repoid)) {
            return new JsonResponse(array("Repoid is not valid!"), 404, ['Content-Type' => 'application/json']);
        }
        $binaries =  (json_decode($_POST['jsonData'], true)) ? json_decode($_POST['jsonData'], true) : array();
        
        if (count($binaries) == 0) {
            return new JsonResponse(array("POST was empty"), 404, ['Content-Type' => 'application/json']);
        }
        
        ($_POST['username']) ? $username = $_POST['username'] : $username = '';
        ($_POST['password']) ? $password = $_POST['password'] : $password = '';
        $object = new \Drupal\arche_gui_api\Object\Collection\CollectionBinariesObject();
        
        $content = $object->init($binaries, $repoid, $username, $password);
        
        if (empty($content)) {
            return new JsonResponse(array("Error! Collection binaries download error!"), 404, ['Content-Type' => 'application/json']);
        }
        
        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
}
