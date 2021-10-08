<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of ArcheMainApiController
 *
 * @author nczirjak
 */
class ArcheApiMainController extends \Drupal\Core\Controller\ControllerBase
{
    /**
     * GUI GND persons file generator API
     * @return JsonResponse
     */
    public function api_gnd_persons(): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\GND\GndPersonsController();
        return $controller->execute();
    }
    
    /**
     * GUI datatable api for the inverse data
     * @param string $repoid
     * @return JsonResponse
     */
    public function api_get_inversedata(string $repoid): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\InverseDataController();
        return $controller->execute($repoid);
    }
    
    /**
     * GUI datatable api for the Members
     * @param string $repoid
     * @return JsonResponse
     */
    public function api_get_members(string $repoid): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\MembersController();
        return $controller->execute($repoid);
    }
    
    /**
     * GUI datatable api for the related projects and resources
     * @param string $repoid
     * @param string $lang
     * @return JsonResponse
     */
    public function api_get_rpr(string $repoid, string $lang = "en"): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\RPRController();
        return $controller->execute($repoid, $lang);
    }
    
    /**
     * Drupal CKEDitor Ontology table generator script API endpoint
     * @param string $lang
     * @return JsonResponse
     */
    public function api_get_ontology_jsplugin(string $lang = "en"): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\Ontology\OntolgyJsController();
        return $controller->execute($lang);
    }
    
    /**
     * GUI collection/child tree view lazy loader for the JsTREE plugin
     * @param string $repoid
     * @param string $lang
     * @return JsonResponse
     */
    public function api_collection_data_lazy(string $repoid, string $lang = "en"): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\Collection\CollectionController();
        return $controller->execute($repoid, $lang);
    }
    
    /**
     * GUI collection download view, selected binaried download API
     * @param string $repoid
     * @return JsonResponse
     */
    public function api_dl_collection_binaries(string $repoid): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\Collection\CollectionBinariesController();
        return $controller->execute($repoid);
    }
    
    /**
     * Collection download Script, file generator API
     * @param string $repoid
     * @return Response
     */
    public function api_collection_dl_script(string $repoid): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Collection\CollectionScriptController();
        return $controller->execute($repoid);
    }
    
    /**
     * Root table html view, for the ontology check
     * @param string $lang
     * @return Response
     */
    public function api_getRootTable(string $lang): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Metadata\RootTableController();
        return $controller->execute($lang);
    }
    
    /**
     *
     * @param string $lang
     * @return JsonResponse
     */
    public function api_getMetadataGui(string $lang): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\Metadata\MetadataGuiController();
        return $controller->execute($lang);
    }
}
