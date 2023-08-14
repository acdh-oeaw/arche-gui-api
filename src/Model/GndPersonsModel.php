<?php

namespace Drupal\arche_gui_api\Model;

/**
 * Description of GndPersonsModel
 *
 * @author nczirjak
 */
class GndPersonsModel extends \Drupal\arche_gui_api\Model\ArcheApiModel {

    private $repo;

    public function __construct() {
        parent::__construct();
        $this->repo = \acdhOeaw\arche\lib\Repo::factory($this->config);
    }

    /**
     * Check the gnd and acdhid
     * @param array $ids
     * @return string
     */
    private function checkId(array $ids): string {
        $gndId = "";
        $acdhId = "";
        
        foreach($ids as $id) {
            if (strpos($id, 'd-nb.info/gnd/') !== false) {
                $gndId = $id;
            }
            if (strpos($id, 'https://id.acdh.oeaw.ac.at/') !== false) {
                $acdhId = $id;
            }
        }
        
        if(!empty($gndId) && !empty($acdhId)) {
            return $gndId.'|'.$acdhId.PHP_EOL;
        }
        return "";
    }

    /**
     * Get the graphs and return the result as a string
     * @return string
     */
    public function getData(): string {

        $config = new \acdhOeaw\arche\lib\SearchConfig();
        $searchTerms[] = new \acdhOeaw\arche\lib\SearchTerm($this->repoDb->getSchema()->__get('namespaces')->rdfs . "type", $this->repoDb->getSchema()->__get('namespaces')->ontology . 'Person', '=');

        $results = $this->repo->getResourcesBySearchTerms($searchTerms, $config);
        $idProp = $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasIdentifier';
           
        $str = "";
        foreach ($results as $r) {
            $check = $this->checkId($r->getGraph()->allResources($idProp));
            if(!empty($check)) {
                $str .= $check;
            }
        }
        return $str;
    }

    
}
