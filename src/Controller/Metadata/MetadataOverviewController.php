<?php

namespace Drupal\arche_gui_api\Controller\Metadata;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of MetadataOverviewController
 *
 * @author nczirjak
 */
class MetadataOverviewController extends \Drupal\arche_gui_api\Controller\ArcheApiBaseController
{
    private $model;
    private $utils;
    
    public function __construct() {
        parent::__construct();
        $this->setModel();
        $this->utils = new \Drupal\arche_gui_api\Helper\Utils();
    }
    
    private function setModel() {
        $this->model = new \Drupal\arche_gui_api\Model\Metadata\MetadataModel();
    }
    
    
    /**
     * generates the detail view overview section api data
     * @param string $lang
     * @param array $searchProps
     * @return JsonResponse
     */
    public function getData(string $identifier, string $lang): JsonResponse
    {
        $identifier = $this->repoDb->getBaseurl().$identifier;
        $data = $this->model->getOverviewData($identifier, $lang);
     
        if (count($data) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }


        return new JsonResponse($data, 200, ['Content-Type' => 'application/json']);
    }
    
   
    
}
