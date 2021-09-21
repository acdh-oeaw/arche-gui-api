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
    
    /**
     * ACDH:Perons for metadata editor
     * @param string $searchStr
     * @return Response
     */
    public function api_persons(string $searchStr): Response
    {   
        $controller = new \Drupal\arche_gui_api\Controller\PersonsController();
        return $controller->execute($searchStr);
    }
    
    /**
     * ACDH:Concepts for metadata editor
     * @param string $searchStr
     * @return Response
     */
    public function api_concepts(string $searchStr): Response
    {   
        $controller = new \Drupal\arche_gui_api\Controller\ConceptsController();
        return $controller->execute($searchStr);
    }
    
    /**
     * ACDH:Places for metadata editor
     * @param string $searchStr
     * @return Response
     */
    public function api_places(string $searchStr): Response
    {   
        $controller = new \Drupal\arche_gui_api\Controller\PlacesController();
        return $controller->execute($searchStr);
    }
    
    /**
     * ACDH:Publications for metadata editor
     * @param string $searchStr
     * @return Response
     */
    public function api_publications(string $searchStr): Response
    {   
        $controller = new \Drupal\arche_gui_api\Controller\PublicationsController();
        return $controller->execute($searchStr);
    }
    
    /**
     * ACDH:Organisations for metadata editor
     * @param string $searchStr
     * @return Response
     */
    public function api_organisations(string $searchStr): Response
    {   
        $controller = new \Drupal\arche_gui_api\Controller\OrganisationsController();
        return $controller->execute($searchStr);
    }
    
    
    
}
