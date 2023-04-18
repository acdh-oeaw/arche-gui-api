<?php

namespace Drupal\arche_gui_api\Controller\Detail;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of BreadcrumbController
 *
 * @author nczirjak
 */
class BreadcrumbController extends \Drupal\arche_gui_api\Controller\ArcheApiBaseController
{
    private $model;
    
    public function __construct() {
        parent::__construct();
        $this->setModel();
    }
    
    private function setModel() {
        $this->model = new \Drupal\arche_gui_api\Model\Detail\BreadcrumbModel();
    }
    
    /**
     * generates the detail view overview section api data
     * @param string $lang
     * @param array $searchProps
     * @return JsonResponse
     */
    public function getData(string $identifier): JsonResponse
    {
        $data = new \Drupal\acdh_repo_gui\Object\BreadCrumbObject($this->model->getData($identifier));
     
        if (!$data || empty($data->getBreadCrumb())) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        $resp = $data->getBreadCrumb();
        return new JsonResponse($resp, 200, ['Content-Type' => 'application/json']);
    }
    
   
    
}
