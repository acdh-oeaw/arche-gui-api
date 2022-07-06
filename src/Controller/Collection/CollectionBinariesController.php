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
    private $repoid = "";
    private $username = "";
    private $password = "";
    private $binaries = array();
    
    public function execute(string $repoid): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/dl_collection_binaries/repoid?_format=json
         */
        $GLOBALS['resTmpDir'] = "";
        $this->setRepoid($repoid);
     
        if (empty($this->repoid)) {
            return new JsonResponse(array("Repoid is not valid!"), 204, ['Content-Type' => 'application/json']);
        }
        $this->createBinariesData($_POST['jsonData']);
                
        if (count($this->binaries) == 0) {
            return new JsonResponse(array("POST was empty"), 204, ['Content-Type' => 'application/json']);
        }
        
        $this->setUsername($_POST['username']);
        $this->setPassword($_POST['password']);
       
        $object = new \Drupal\arche_gui_api\Object\Collection\CollectionBinariesObject();
        $content = $object->init($this->binaries, $this->repoid, $this->username, $this->password);
        
        if (empty($content)) {
            return new JsonResponse(array("Error! Collection binaries download error!"), 204, ['Content-Type' => 'application/json']);
        }
        
        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }

    private function setRepoid(string $repoid): void
    {
        $this->repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
    }

    private function createBinariesData(string $data): void
    {
        $this->binaries =  (json_decode($data, true)) ? json_decode($data, true) : array();
    }

    private function setUsername(string $data): void
    {
        $this->username = ($data) ? $data : '';
    }

    private function setPassword(string $data): void
    {
        $this->password = ($data) ? $data : '';
    }
}
