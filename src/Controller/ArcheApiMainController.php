<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of ArcheMainApiController
 *
 * @author nczirjak
 */
class ArcheApiMainController extends \Drupal\Core\Controller\ControllerBase
{
    
    public function api_gnd_persons(): JsonResponse
    {   
        $controller = new \Drupal\arche_gui_api\Controller\GND\GndPersonsController();
        return $controller->execute();
    }
    
    public function api_get_inversedata(string $repoid): JsonResponse
    {   
        $controller = new \Drupal\arche_gui_api\Controller\InverseDataController();
        return $controller->execute($repoid);
    }
    
    public function api_get_members(string $repoid): JsonResponse
    {   
        $controller = new \Drupal\arche_gui_api\Controller\MembersController();
        return $controller->execute($repoid);
    }
    
    public function api_get_rpr(string $repoid, string $lang = "en"): JsonResponse
    {   
        $controller = new \Drupal\arche_gui_api\Controller\RPRController();
        return $controller->execute($repoid, $lang);
    }
    
    public function api_get_ontology_jsplugin(string $lang = "en"): JsonResponse
    {   
        $controller = new \Drupal\arche_gui_api\Controller\Ontology\OntolgyJsController();
        return $controller->execute($lang);
    }
    
    
    
    
      
}
