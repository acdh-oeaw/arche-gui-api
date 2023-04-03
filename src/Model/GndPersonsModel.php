<?php

namespace Drupal\arche_gui_api\Model;

/**
 * Description of GndPersonsModel
 *
 * @author nczirjak
 */
class GndPersonsModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getData(): array
    {
        $requestUrl = $this->repoDb->getBaseUrl() . "search?"
                . $this->getQueryPropVal() . ""
                . "&lang[]=" . $this->siteLang;
         
        try {
            $graph = new \EasyRdf\Graph();
            return $graph->newAndLoad($requestUrl)->toRdfPhp();
        } catch (\Exception | \EasyRdf\Exception  $ex) {
            die($ex->getMessage());
            \Drupal::logger('arche_rest_gui')->notice($ex->getMessage());
            return array();
        }
        return array();
    }
    
    private function getQueryPropVal(): string
    {
        return http_build_query(
            array(
                'property' => array(
                    $this->repoDb->getSchema()->__get('namespaces')->rdfs . "type",
                    $this->repoDb->getSchema()->__get('namespaces')->ontology . "hasIdentifier"
                ),
                'value' => array(
                    $this->repoDb->getSchema()->__get('namespaces')->ontology.'Person',
                    'gnd'
                ),
                'operator' => array('=', '~')
            )
        );
    }
}
