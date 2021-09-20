<?php

namespace Drupal\arche_gui_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of ArcheMainApiController
 *
 * @author nczirjak
 */
class ArcheApiMainController extends ControllerBase 
{
    public function __construct()
    {
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language']) : $this->siteLang = "en";
        /*
        $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml';
        try {
            $this->repo = \acdhOeaw\arche\lib\Repo::factory($this->config);
            $this->repodb = \acdhOeaw\arche\lib\RepoDb::factory($this->config);
        } catch (\Exception $ex) {
            \Drupal::messenger()->addWarning($this->t('Error during the BaseController initialization!').' '.$ex->getMessage());
            return array();
        }
         * 
         */
    }
    
    public function api_persons(string $searchStr): Response
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/persons/MYVALUE?_format=json
         */

        $response = new Response();
/*
        if (empty($searchStr)) {
            return new JsonResponse(array("Please provide a search string"), 404, ['Content-Type' => 'application/json']);
        }

        $obj = $this->createDbHelperObject(array('type' => 'https://vocabs.acdh.oeaw.ac.at/schema#Person', 'searchStr' => strtolower($searchStr)));
        //get the data
        $this->modelData = $this->model->getViewData('persons', $obj);

        $this->result = $this->helper->createView($this->modelData, 'persons');
        if (count($this->result) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        */
        $response->setContent(json_encode('yes'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
