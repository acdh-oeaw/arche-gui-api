<?php

namespace Drupal\arche_gui_api\Controller\SmartSearch;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of SMCoordinatesController
 *
 * @author nczirjak
 */
class SMCoordinatesController extends \Drupal\Core\Controller\ControllerBase
{
    private $config;
    private $model;

    public function __construct()
    {
        $this->config = \acdhOeaw\arche\lib\Config::fromYaml(\Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml');
        $this->setModel();
    }
    
    private function setModel()
    {
        $this->model = new \Drupal\arche_gui_api\Model\SmartSearch\SMCoordinatesModel();
    }

    public function get(): Response
    {
        $data = [];
        $data = $this->model->getData();


        if (count($data) === 0) {
            return new Response(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        return new Response(json_encode($data));
    }
}
