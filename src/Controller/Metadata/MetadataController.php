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
    public function getTopThreeTopCollection(string $lang = "en", array $searchProps): Response
    {
        $searchProps['limit'] = 3;
        $searchProps['order'] = 'desc';
        $searchProps['orderby'] = 3;
       
        $data = $this->model->getData($lang, $searchProps);
        $objects = [];
        
        $guiData = $this->utils->formatResultToGui($data);
      
        foreach ($guiData as $v) {
            $objects[] =  new \Drupal\acdh_repo_gui\Object\ResourceObject($v, $this->repoDb);
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
    
    public function getData(string $lang = "en", array $searchProps): JsonResponse
    {
        $searchProps['limit'] = 3;
        $searchProps['order'] = 'desc';
        $searchProps['orderby'] = 3;
       
        $data = $this->model->getData($lang, $searchProps);
        $objects = [];
        
        $guiData = $this->utils->formatResultToGui($data);
      
        foreach ($guiData as $v) {
            $objects[] =  json_encode((array)new \Drupal\acdh_repo_gui\Object\ResourceObject($v, $this->repoDb));
        }
        
        
        if (count($objects) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }


        return new JsonResponse($objects, 200, ['Content-Type' => 'application/json']);
    }
}
