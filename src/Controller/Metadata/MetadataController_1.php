<?php

namespace Drupal\arche_gui_api\Controller\Metadata;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of MetadataController
 *
 * @author nczirjak
 */
class MetadataController extends \Drupal\arche_gui_api\Controller\ArcheApiBaseController
{
    private $model;
    private $utils;
    
    public function __construct()
    {
        parent::__construct();
        $this->setModel();
        $this->utils = new \Drupal\arche_gui_api\Helper\Utils();
    }
    
    private function setModel()
    {
        $this->model = new \Drupal\arche_gui_api\Model\Metadata\MetadataModel();
    }
    
    /**
     * Get the top 3 topcollection for the homepage
     *
     * URL: https://arche-dev.acdh-dev.oeaw.ac.at/browser/api/getHPTop/en
     *
     * @param string $lang
     * @param array $searchProps
     * @return Response
     */
    public function getTopThreeTopCollection(string $lang, array $searchProps): Response
    {
        $searchProps['limit'] = 3;
        $searchProps['order'] = 'desc';
        $searchProps['orderby'] = 3;
       
        $data = $this->model->getData($lang, $searchProps);
        $objects = [];
        
        $guiData = $this->utils->formatResultToGui($data);
      
        foreach ($guiData as $v) {
            $objects[] =  new \Drupal\acdh_repo_gui\Object\ResourceObject($v, $this->repo);
        }
        
        
        if (count($objects) == 0) {
            return new Response(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
    
        $build = [
            '#theme' => 'acdh-repo-gui-main-page-left-block',
            '#result' => (array) $objects,
            '#cache' => ['max-age' => 0]
        ];
       
        return new Response(render($build));
    }
    
    /**
     *
     * @param string $lang
     * @param array $searchProps
     * @return JsonResponse
     */
    public function getData(string $lang, array $searchProps): JsonResponse
    {
        $searchProps['limit'] = 3;
        $searchProps['order'] = 'desc';
        $searchProps['orderby'] = 3;
       
        $data = $this->model->getData($lang, $searchProps);
        $objects = [];
      
        $guiData = $this->utils->formatResultToGui($data);
      
        foreach ($guiData as $v) {
            $objects[] =  json_encode((array)new \Drupal\acdh_repo_gui\Object\ResourceObject($v, $this->repo));
        }
        
        
        if (count($objects) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }


        return new JsonResponse($objects, 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * get the root view collections ordered by date desc by default
     * @param string $lang
     * @param array $searchProps
     * @return Response
     */
    public function getRootData(string $lang, array $searchProps): Response
    {
        $data = $this->model->getRootData($lang, $searchProps);
 
        $cols = [];
        if (count($data) > 0) {
            $cols = array_keys((array)$data[0]);
        }
        
        $response = new Response();
        $response->setContent(
            json_encode(
                array(
                    "aaData" => $data,
                    "iTotalRecords" => ($data[0]->sumcount) ?  $data[0]->sumcount : 0,
                    "iTotalDisplayRecords" => ($data[0]->sumcount) ?  $data[0]->sumcount : 0,
                    "draw" => intval($searchProps['draw']),
                    "cols" =>  $cols,
                    "order" => $searchProps['order'],
                    "orderby" => $searchProps['orderby']
                )
            )
        );
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
