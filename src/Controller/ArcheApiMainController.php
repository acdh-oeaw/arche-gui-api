<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Utility\Xss;

/**
 * Description of ArcheMainApiController
 *
 * @author nczirjak
 */
class ArcheApiMainController extends \Drupal\Core\Controller\ControllerBase
{
    private function setProps(): array
    {
        $offset = (empty($_POST['start'])) ? 0 : $_POST['start'];
        $limit = (empty($_POST['length'])) ? 10 : $_POST['length'];
        $draw = (empty($_POST['draw'])) ? 0 : $_POST['draw'];
        $search = (empty($_POST['search']['value'])) ? "" : $_POST['search']['value'];
        //datatable start columns from 0 but in db we have to start it from 1
        $orderby = (empty($_POST['order'][0]['column'])) ? 1 : (int)$_POST['order'][0]['column'];
        $order = (empty($_POST['order'][0]['dir'])) ? 'asc' : $_POST['order'][0]['dir'];
        return [
            'offset' => $offset, 'limit' => $limit, 'draw' => $draw, 'search' => $search,
            'orderby' => $orderby, 'order' => $order
        ];
    }
    
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
        $controller = new \Drupal\arche_gui_api\Controller\Detail\InverseDataController();
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
        $controller = new \Drupal\arche_gui_api\Controller\Detail\RPRController();
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
    
    
    public function api_getMetadataGuiHTML(string $lang): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\Metadata\MetadataGuiHTMLController();
        return $controller->execute($lang);
    }
    
    
    public function api_getFileFormatsHTML(string $lang): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\FileFormats\FileFormatsHTMLController();
        return $controller->execute($lang);
    }
    
    
    /**
     * Fetch the versions data for the tree
     * @param string $repoid
     * @return JsonResponse
     */
    public function api_versions(string $repoid): JsonResponse
    {
        $controller = new \Drupal\arche_gui_api\Controller\VersionsController();
        return $controller->execute($repoid);
    }
    
    /**
     * Fetch the newer verison for the alert div
     * @param string $repoid
     * @param string $lang
     * @return Response
     */
    public function api_versions_alert(string $repoid, string $lang = "en"): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\VersionsController();
        return $controller->getAlertDiv($repoid, $lang);
    }
    
    /**
     * fetch the versions list
     * @param string $repoid
     * @return Response
     */
    public function api_versions_list(string $repoid): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\VersionsController();
        return $controller->executeList($repoid);
    }
    
    /**
     * Fetch the values for the person view, where the actual person is the hasActor
     * @param string $repoid
     * @param string $lang
     * @return JsonResponse
     */
    public function api_get_hasActor(string $repoid, string $lang = "en"): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Child\ChildController();
        return $controller->getActor($repoid, $this->setProps(), $lang);
    }
    
    public function api_searchBlock(string $lang): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\SearchBlock\MainController();
        return $controller->getData($lang);
    }
    
    public function api_getHPTop(string $lang): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Metadata\MetadataController();
        return $controller->getTopThreeTopCollection($lang, $this->setProps());
    }
    
    public function api_root(string $lang="en"): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Metadata\MetadataController();
        return $controller->getRootData($lang, $this->setProps());
    }
    
    public function api_search(string $values): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Search\SearchController();
        return $controller->getData($values, $this->setProps());
    }
    
    public function api_overview(string $identifier, string $lang): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Metadata\MetadataOverviewController();
        return $controller->getData($identifier, $lang);
    }
    
    
    public function api_breadcrumb(string $identifier): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Detail\BreadcrumbController();
        return $controller->getData($identifier);
    }
    
    public function api_child(string $repoid, string $lang = "en"): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\Child\ChildController();
        return $controller->getChild($repoid, $this->setProps(), $lang);
    }
    
    public function api_sms_coordinates(): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\SmartSearch\SMCoordinatesController();
        return $controller->get();
    }
    
    public function api_smart_search(): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\SmartSearch\SmartSearchController();
        return $controller->search($_GET);
    }
    
    public function api_search_date_facets(): Response
    {
        $controller = new \Drupal\arche_gui_api\Controller\SearchBlock\MainController();
        return $controller->dateFacets();
    }
}
